<?php namespace App\Controllers\Order;

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
        
        $data = $this->data;
        $data["s_date_from"] = -1;
        $data["s_date_to"] = -1; 

        if ($this->request->getPost('filter') == "true") {
            $data['s_date_from'] = $this->request->getPost('s_date_from');
            $data['s_date_to'] = $this->request->getPost('s_date_to');
        }

        echo view('order/search',$data);
        
    }

    public function dl($rpt_type = ''){
        $data = $this->data;


        

        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'orderlist'){
            $filters = array("s_date_from"=>$this->request->getGet('s_date_from'),
                             "s_date_to"=>$this->request->getGet('s_date_to'),);

            $generatefile->generate_orderlist($data,$filters);
            exit;
        }
    }


   
    

    
}
    