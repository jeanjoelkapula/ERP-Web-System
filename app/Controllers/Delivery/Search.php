<?php namespace App\Controllers\Delivery;

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
        
        // $data = $this->data;
        $data = $this->data;

        // populate stock list from database
        $sql = "SELECT DISTINCT * FROM TBL_DELIVERY_NOTE;";

        $object = $this->db->query($sql);

        $data['object']=$object;

        if ($this->request->getPost('form_update_delivery') == "true") {       
            return redirect()->to('/delivery/search');
            exit();
        }
        
        
        echo view('delivery/search',$data);
        // echo view('delivery/delivery_note_preview');
        
    }

    public function dl($rpt_type = ''){
        $data = $this->data;


        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'deliverylist'){

            $generatefile->generate_deliverylist($data);
            exit;
        }
    }
    
    
}
    