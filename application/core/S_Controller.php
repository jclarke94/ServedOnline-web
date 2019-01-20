<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class S_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper('form');
        $this->load->helper('url');

        $this->load->model("admin_model");
        $this->load->model("site_model");
        $this->load->model("client_model");
        $this->load->model("contractor_model");
        $this->load->model("notice_model");
    }

    /**
     * Writes an object to the response encoded in JSON
     */
    protected function JSON($obj, $statusCode = 200) {
        ob_end_clean();
        http_response_code($statusCode);
        header("Content-type: application/json");
        die(json_encode($obj));
    }

    protected function validateParams($params, $requiredParams, $DEBUG = FALSE) {
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                $out = array("success" => FALSE, "error" => "Missing required parameter " . $param);
                if ($DEBUG) { $out["debug"] = $postdata; }
                $this->JSON($out, 400);
            }
        }

        return TRUE;
    }

    protected function authorise($handleError = TRUE) {
        $header = getallheaders();

        $result = FALSE;
        if (isset($header["userID"]) && isset($header["token"])) {
            $result = $this->user_model->validateAuthToken($header["userID"], $header["token"]);
        }

        if (!$result) {
            if ($handleError) {
                $this->JSON(array("success" => FALSE, "error" => "Not Authorised"), 401);
            } else {
                return FALSE;
            }
        }

        return TRUE;
    }

    protected function authenticate($handleError = TRUE) {
        $result = FALSE;

        if (isset($_SESSION["userId"]) && isset($_SESSION["userToken"])) {
            $this->load->model("admin_model");
            $result = $this->admin_model->validateAuthToken($_SESSION["userId"], $_SESSION["userToken"]);
        }

        if (!$result) {
            if ($handleError) {
                $redirect = str_replace(base_url(), "", current_url());
                $redirect = str_replace("index.php", "", $redirect);

                if ($redirect != "") {
                    $redirect = "?redirect=" . $redirect;
                }

                redirect("/cms/login/" . $redirect, "refresh");
                die("<!-- Session go boom! -->");
            } else {
                return FALSE;
            }
        }

        return TRUE;
    }

    protected function invalidateSession() {
        unset($_SESSION["userId"]);
        unset($_SESSION["userToken"]);
    }

    protected function getUserId() {
        $header = getallheaders();

        // API
        if (isset($header["userID"])) {
            return intval($header["userID"]);
        }


        // Admin CMS
        if (isset($_SESSION["userId"])) {
            return intval($_SESSION["userId"]);
        }

        return FALSE;
    }


    /**
     * Creates an Alphanumeric string of a given length.
     *
     * Output will include lowercase and uppercase characters and numbers.
     */
    public function generateAlphanumericCode($length) {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMOPQRSTUVWXYZ123456789";

        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }

    function manageFileUpload($uploadPath, $postdataParam, $filename) {
        $this->load->library("upload");

        $uploadPath = FCPATH . $uploadPath;

        $this->upload->initialize($this->setUploadOptions($uploadPath, $filename));
        if (!$this->upload->do_upload($postdataParam)) {
            $uploadData = array();
            $uploadData["success"] = "NO";
            $uploadData["error"] = $this->upload->display_errors();
            return $uploadData;
            //$this->JSON(array("success" => FALSE, "error" => "Failed to upload image"), 400);
        }

        $uploadData = $this->upload->data();
        $filePath = $uploadData["full_path"];

        $imagetype = exif_imagetype($filePath);

        if ($imagetype == IMAGETYPE_JPEG) {
            // Rotate JPEGs with Orientation metadata set
            $this->load->library('image_lib');
            $config['image_library'] = 'GD2';
            $config['source_image'] = $filePath;

            $imgdata= exif_read_data($filePath);

            if (isset($imgdata["Orientation"])) {
                switch($imgdata['Orientation']) {
                    case 3:
                        $config['rotation_angle']=180;
                        break;
                    case 6:
                        $config['rotation_angle']=270;
                        break;
                    case 8:
                        $config['rotation_angle']=90;
                        break;
                }

                $this->image_lib->initialize($config);
                $this->image_lib->rotate();
                $this->image_lib->clear();
            }
        }

        $thumbFilename = $filename . "_thumb";

        list($imgWidth, $imgHeight) = getimagesize($filePath);

        if ($imgWidth > $imgHeight) {
            $newWidth = 300;
            $scaleFactor = $newWidth / $imgWidth;
            $newHeight = $imgHeight * $scaleFactor;
        } else {
            $newHeight = 300;
            $scaleFactor = $newHeight / $imgHeight;
            $newWidth = $imgWidth * $scaleFactor;
        }



        $imageExt = ".jpg";
        $image_p = imagecreatetruecolor($newWidth, $newHeight);
        if ($imagetype == IMAGETYPE_GIF) {
            $image = imagecreatefromgif($filePath);
            $imageExt = ".gif";
        } else if ($imagetype == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($filePath);
            $imageExt = ".png";
        } else {
            $image = imagecreatefromjpeg($filePath);
        }
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $imgWidth, $imgHeight);

        $thumbPath = $uploadData["file_path"] . $thumbFilename . $imageExt;
        $fullPath = $uploadData["file_path"] . $filename . $imageExt;

        if ($imagetype == IMAGETYPE_GIF) {
            imagegif($image_p, $thumbPath);
        } else if ($imagetype == IMAGETYPE_PNG) {
            imagepng($image_p, $thumbPath);
        } else {
            imagejpeg($image_p, $thumbPath);
        }

        rename($filePath, $fullPath);

        $uploadData = array();
        $uploadData["result"] = TRUE;
        $uploadData["fullPath"] = $fullPath;
        $uploadData["thumbPath"] = $thumbPath;
        $uploadData["width"] = $imgWidth;
        $uploadData["height"] = $imgHeight;

        return $uploadData;
    }

    function setUploadOptions($uploadPath) {
        //upload an image options
        $config = array();
        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['remove_spaces'] = TRUE;
        $config['overwrite']     = FALSE;

        if(!file_exists($config['upload_path']))
            mkdir($config['upload_path'],0777,TRUE);

        return $config;
    }








}
?>	