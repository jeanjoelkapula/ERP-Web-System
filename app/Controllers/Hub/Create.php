<?php namespace App\Controllers\Hub;

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
        $data["url"] = "/hub/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";
        
        if(!isset($data["regions"])){
            $sql = "SELECT REGION_NO, REGION_NAME FROM TBL_REGION;";
            $result = $this->db->query($sql);
            $data["regions"] = $result;
        }

        if ($this->request->getPost('form_create_hub') == "true") {

            $hub_name = $this->db->escape(trim($this->request->getPost('hub_name')));
            $hub_latitude = $this->db->escape(trim(replaceDirectionWithValue($this->request->getPost('hub_latitude'))));
            $hub_longitude = $this->db->escape(trim(replaceDirectionWithValue($this->request->getPost('hub_longitude'))));
            $region_no = $this->db->escape(trim($this->request->getPost('region_no')));
            $hub_descr = $this->db->escape(trim($this->request->getPost('hub_descr')));
        
            $sql = "insert into TBL_HUB (HUB_NAME,HUB_LOCATION,HUB_DESCR,REGION_ID) VALUES

                    ($hub_name,
                    POINT(
                   ($hub_latitude),
                    $hub_longitude),
                    $hub_descr,
                    $region_no)
                    ;";
            $this->db->query($sql);
          
            return redirect()->to('/hub/search/');
            exit();

        }
        
       
        echo view('hub/create',$data);
        
    }    
    
}
    