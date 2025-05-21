<?php namespace App\Controllers\Division;

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

        $data = $this->data;
                
        if ($this->request->getPost('division_create') == "true") {

            $division_name = $this->db->escape(trim($this->request->getPost('division_name_var')));
            $division_manager = $this->db->escape(trim($this->request->getPost('division_manager_var')));
            $division_email = $this->db->escape(trim($this->request->getPost('division_email_var')));
            $division_number = $this->db->escape(trim($this->request->getPost('division_number_var')));
        
            $sql = "insert into TBL_DIVISION (DIVISION_NAME, DIVISION_MANAGER, EMAIL, CONTACT_NUMBER) VALUES
                    ($division_name, $division_manager, $division_email, $division_number);";
            $this->db->query($sql);
            

            return redirect()->to('/division/search/');
            exit();
                

        }
        
       
        echo view('division/create',$data);
        
    }
    
    
}
    