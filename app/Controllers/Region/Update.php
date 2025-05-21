<?php namespace App\Controllers\Region;

use \App\Controllers\BaseController;

class Update extends BaseController {

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
    

    public function index($entity_id=0)
    {
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;

        if (!isset($entity_id) || ($entity_id <= 0)) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_REGION  WHERE REGION_NO = $entity_id;";
            $result = $this->db->query($sqlQuery);
            $found = false;

            foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT'] > 0) {
                        $found = true;
                    }
                }
                endforeach;

            if ($found) {
                $sqlQuery = "SELECT * FROM TBL_REGION WHERE REGION_NO = $entity_id";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row): 
                { 
                    $data['region_id'] = $row['REGION_NO']; 
                    $data['region_name'] = $row['REGION_NAME']; 
                    $data['region_manager'] = $row['REGION_MANAGER']; 
                    $data['region_email'] = $row['EMAIL']; 
                    $data['region_number'] = $row['CONTACT_NUMBER']; 
                    $data['division_id'] = $row['DIVISION_ID']; 
                    $data['secretary'] = $row['REGIONAL_SECRETARY']; 
                }
                endforeach;                
                    
            }
            else {
                //throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                //exit();

                $data['ent_id'] = $entity_id;
                echo view('region/error', $data);
                return false;
            }
        }
        
        
        if($this->request->getPost('form_update_region') == "true") {

            $region_id = $this->db->escape(trim($this->request->getPost('region_id')));
            $region_name = $this->db->escape(trim($this->request->getPost('region_name')));
            $region_manager = $this->db->escape(trim($this->request->getPost('region_manager')));
            $region_email = $this->db->escape(trim($this->request->getPost('region_email')));
            $region_number = $this->db->escape(trim($this->request->getPost('region_number')));
            $division_id = $this->db->escape(trim($this->request->getPost('division_id')));
                             
            $sql = "Update TBL_REGION SET
                     REGION_NAME = $region_name,
                     REGION_MANAGER = $region_manager,
                     EMAIL = $region_email,
                     CONTACT_NUMBER = $region_number,
                     DIVISION_ID = $division_id
                     WHERE REGION_NO = $entity_id;";

            $this->db->query($sql);
            
            return redirect()->to('/region/search');
            exit();
        }

       
        echo view("region/update", $data);
        
    }
    
    
    
}