<?php namespace App\Controllers\Stock;

use \App\Controllers\BaseController;
use \App\Models\GenerateFile;

class Search extends BaseController {

    public function _remap($method, ...$params)
    {

        if (method_exists($this, $method))
        {
            return call_user_func_array(array($this, $method), $params);
        } else if ($method == "index") {
            $this->index($method);
            exit;
        } else {
            return $this->index($method, ...$params);
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function index() {
        $data = $this->data;

        // populate stock list from database
        $sql = "SELECT DISTINCT 
        stock.EBQ_CODE,
        stock.PURCHASE_COST, 
        stock.DESCRIPTION,                         
        metric.METRIC_DESCRIPTION,
        (SELECT SUM(QUANTITY) FROM TBL_HUB_STOCK WHERE EBQ_CODE = stock.EBQ_CODE) AS QUANTITY,        
        stock.AVG_COST,
        stock.IS_ACTIVE,
        stock.IS_BUILT
        FROM TBL_STOCK stock
        INNER JOIN TBL_METRIC metric ON stock.METRIC_ID = metric.METRIC_ID,
        TBL_HUB_STOCK hub;";

        $object = $this->db->query($sql);

        $data['object']=$object;

        if ($this->request->getPost('form_update_stock') == "true") {       
            return redirect()->to('/stock/search');
            exit();
        }


        echo view('stock/search', $data);
    }

    public function dl($rpt_type = ''){
        $data = $this->data;

        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'stocklist'){
            $generatefile->generate_stocklist($data,'');
            exit;
        }
    }
}