<?php namespace App\Controllers\Contractor;

use \App\Controllers\BaseController;

class Create extends BaseController {

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
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }

        //setting action type and url for contractor create/update form
        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/contractor/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";
                
        if ($this->request->getPost('form_create_contractor') == "true") {
        
            $name = $this->db->escape(trim($this->request->getPost('name')));
            $contact_number = $this->db->escape(trim($this->request->getPost('number')));            
            $email = $this->db->escape(trim($this->request->getPost('email')));
        
            $sql = "insert into TBL_CONTRACTOR (contractor_name,contact_number,email,in_business) VALUES
            ($name,$contact_number,$email,1);";

            $this->db->query($sql);
            
            return redirect()->to('/contractor/search');
            exit();
        }
    
        echo view('contractor/create',$data);
        
    }
    
    
}
    