<?php namespace App\Controllers\Contractor;

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
    

    public function index($entity_id = 0)
    {
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }

        //setting action type and url for contractor create/update form
        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/contractor/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";
                
        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_CONTRACTOR  WHERE CONTRACTOR_ID = $entity_id;";
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
                $sqlQuery = "SELECT * FROM TBL_CONTRACTOR WHERE CONTRACTOR_ID = $entity_id";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row): 
                { 
                    $data['id'] = $row['CONTRACTOR_ID']; 
                    $data['name'] = $row['CONTRACTOR_NAME']; 
                    $data['contact_number'] = $row['CONTACT_NUMBER']; 
                    $data['email'] = $row['EMAIL']; 
                    $data['inbusiness'] = $row['IN_BUSINESS']; 
                }
                endforeach;
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

        }

        if ($this->request->getPost('form_update_contractor') == "true") {
        
            $id = $this->request->getPost('contractor');
            $name = trim($this->request->getPost('name'));
            $contact_number = trim($this->request->getPost('number'));            
            $email = trim($this->request->getPost('email'));

            if ($this->request->getPost('inbusiness') == 'on') {
                $in_business = 1;
            }
            else {
                $in_business = 0;
            }
           
            $sql = "Update TBL_CONTRACTOR SET contractor_name = ?, contact_number = ?, email = ?, in_business = ? WHERE (contractor_id = ?);";

            $this->db->query($sql, [$name, $contact_number, $email, $in_business, $id]);

            return redirect()->to('/contractor/search');
            exit();

        }
    
        echo view('contractor/update',$data);
        
    }
    
}
    