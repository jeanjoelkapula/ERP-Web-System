<?php namespace App\Controllers\Division;

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

            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_DIVISION  WHERE DIVISION_ID = $entity_id;";
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
                $sqlQuery = "SELECT * FROM TBL_DIVISION WHERE DIVISION_ID = $entity_id";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row): 
                { 
                    $data['division_id'] = $row['DIVISION_ID']; 
                    $data['division_name'] = $row['DIVISION_NAME']; 
                    $data['division_manager'] = $row['DIVISION_MANAGER']; 
                    $data['division_email'] = $row['EMAIL']; 
                    $data['division_number'] = $row['CONTACT_NUMBER']; 
                    
                }
                endforeach;
            }
            else {
                //throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                //exit();

                $data['ent_id'] = $entity_id;
                echo view('division/error', $data);
                return false;
            }

        }
        
        if($this->request->getPost('division_update') == "true"){

            $division_id = $this->db->escape(trim($this->request->getPost('division_id_var')));
            $division_name = $this->db->escape(trim($this->request->getPost('division_name_var')));
            $division_manager = $this->db->escape(trim($this->request->getPost('division_manager_var')));   
            $division_email = $this->db->escape(trim($this->request->getPost('division_email_var')));
            $division_number = $this->db->escape(trim($this->request->getPost('division_number_var')));
            

            $sql = "Update TBL_DIVISION SET
                     DIVISION_NAME = $division_name,
                     DIVISION_MANAGER = $division_manager,
                     EMAIL = $division_email,
                     CONTACT_NUMBER = $division_number
                     WHERE DIVISION_ID = $division_id;";

            $this->db->query($sql);
            

            return redirect()->to('/division/search');
            exit();
        }

       
        echo view("division/update", $data);
        
    }
    
    
    
}