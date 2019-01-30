<?php defined('BASEPATH') OR exit('No direct script access allowed');


class SO_Model extends CI_Model {

    protected function makeAbsolutePathPublic($path) {
        $replacePath = FCPATH;

        $outPath = str_replace($replacePath, "", $path);

        return $outPath;
    }

    /* *************************
     *
     * 		  DATATABLES
     *
     * *************************/

    /**
     * Sets Table options based on request data from the datatables library
     */
    protected function datatables_setTableOptions($tableData) {
        if (isset($tableData['start']) && isset($tableData['length'])) {
            $this->db->limit($tableData['length'],$tableData['start']);
        }

        if (isset($tableData['order']) && isset($tableData['columns'])) {
            $order = $tableData['order'];
            $columns = $tableData['columns'];
            for ($i=0; $i < count($order);$i++) {
                $columnName = $columns[$order[$i]['column']]['data'];
                $this->db->order_by($columnName,$order[$i]['dir']);
            }
        }
    }

    /**
     * Prepares a datastructure which can be serialised into JSON and returned to a Datatable
     */
    protected function datatables_prepareAjaxResponse($tableData, $qResult, $totalCount) {
        $data['recordsTotal'] = $totalCount;
        $data['recordsFiltered'] = $totalCount;
        $data['draw'] = $tableData['draw'];
        $data['data'] = $qResult;
        return $data;
    }
}

?>