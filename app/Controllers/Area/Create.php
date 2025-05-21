<?php namespace App\Controllers\Area;

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
        $data["action_type"] = "create";
        $data["url"] = "/area/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";

        if(!isset($data["regions"])){
            $sql = "SELECT REGION_NO, REGION_NAME FROM TBL_REGION;";
            $result = $this->db->query($sql);
            $data["regions"] = $result;
        }
       

        if ($this->request->getPost('form_create_area') == "true") {
            $area_name = $this->db->escape(trim($this->request->getPost('area_name')));
            $area_manager = $this->db->escape(trim($this->request->getPost('area_manager')));
            $region_no = $this->db->escape(trim($this->request->getPost('region_no')));
            $email = $this->db->escape(trim($this->request->getPost('email_address')));
            $contact_number = $this->db->escape(trim($this->request->getPost('contact_number')));
        
            $sql = "insert into TBL_AREA (AREA_NAME,AREA_MANAGER,REGION_NO,EMAIL,CONTACT_NUMBER) VALUES
                    ($area_name,
                    $area_manager,
                    $region_no,
                    $email,
                    $contact_number
                    );";
            $this->db->query($sql);

            return redirect()->to('/area/search/');
            exit();
                

        }
        
       
        echo view('area/create',$data);
        
    }
    
    
}
    