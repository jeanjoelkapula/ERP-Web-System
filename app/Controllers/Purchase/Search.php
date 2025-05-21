<?php namespace App\Controllers\Purchase;

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
    

    public function index()
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;
        $sql = "SELECT * FROM TBL_PURCHASE_ORDER;";
         
        $result = $this->db->query($sql);
        $data["result"] = $result;

        echo view('purchase/search', $data);
            
    }

    public function dl($rpt_type = ''){
        $data = $this->data;

        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'purchase_list'){
            $generatefile->generate_purchase_orderlist($data,'');
            exit;
        }
    }
}
    