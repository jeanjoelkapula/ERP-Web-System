<?php namespace App\Controllers\Region;

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

        if ($this->request->getPost('form_create_region') == "true") {

            $region_name = $this->db->escape(trim($this->request->getPost('region_name_var')));
            $region_manager = $this->db->escape(trim($this->request->getPost('region_manager_var')));
            $region_email = $this->db->escape(trim($this->request->getPost('region_email_var')));
            $region_number = $this->db->escape(trim($this->request->getPost('region_number_var')));
            $division_name = $this->db->escape(trim($this->request->getPost('division_var')));
            $division_id;
            


            $sql1 = "select DIVISION_ID from TBL_DIVISION where DIVISION_NAME = $division_name";
            $query = $this->db->query($sql1);
                        
            foreach ($query->getResult() as $row): {

                $division_id = $row->DIVISION_ID;

            } endforeach;
            
            

            $sql2 = "insert into TBL_REGION (REGION_NAME,REGION_MANAGER,DIVISION_ID, EMAIL, CONTACT_NUMBER) VALUES
                    ($region_name,$region_manager,$division_id, $region_email, $region_number);";

            $this->db->query($sql2);
            

            return redirect()->to('/region/search');
            exit();
                

        }
        
       
        echo view('region/create',$data);
        
    }
    
    
}
    