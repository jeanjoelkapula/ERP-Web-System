<?php namespace App\Controllers\Contractor;

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
        
        $sql = "select * from TBL_CONTRACTOR;";

        $object = $this->db->query($sql);

        $data['object']=$object;

     

        if ($this->request->getPost('form_update_status') == "true") {                               
        
            $status = $this->db->escape(trim($this->request->getPost('business')));
            
            $contractorID = $this->db->escape(trim($this->request->getPost('contractor')));

            $inbusiness = $this->request->getPost('inbusiness');

            if($inbusiness == "on")
            {                 
                $sql = "update TBL_CONTRACTOR set in_business = 1 where CONTRACTOR_ID = $contractorID;";
            }
            else
            {
                $sql = "update TBL_CONTRACTOR set in_business = 0 where CONTRACTOR_ID = $contractorID;";
            }           

            $this->db->query($sql);
            
            return redirect()->to('/contractor/search');
            exit();
        }

        echo view('contractor/search', $data);
            
    }

    public function dl($rpt_type){

        $data = $this->data;

        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'contractorlist'){
            $generatefile->generate_contractorlist($data,'');
            exit;
        }
    }

}    