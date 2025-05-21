<?php namespace App\Controllers\Area;

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
        //setting action type and url for area create/update form
        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/area/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {

            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_AREA  WHERE AREA_NO = $entity_id;";
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
                $sqlQuery = "SELECT * FROM TBL_AREA WHERE AREA_NO = $entity_id";
                $result = $this->db->query($sqlQuery);
                $found = false;

                foreach ($result->getResult('array') as $row): 
                { 
                    $data['area_no'] = $row['AREA_NO']; 
                    $data['area_name'] = $row['AREA_NAME']; 
                    $data['area_manager'] = $row['AREA_MANAGER']; 
                    $data['region_no'] = $row['REGION_NO'];
                    $data['contact_number'] = $row['CONTACT_NUMBER'];
                    $data['email_address'] = $row['EMAIL'];
                }
                endforeach;
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        if ($this->request->getPost('form_update_area') == "true") {

            $area_no = $this->request->getPost('area');
            $area_name = trim($this->request->getPost('area_name'));
            $area_manager = trim($this->request->getPost('area_manager'));            
            $region_no = trim($this->request->getPost('region_no'));
            $contact_number = trim($this->request->getPost('contact_number'));
            $email = trim($this->request->getPost('email_address'));

            $sql = "Update TBL_AREA SET area_name = ?, area_manager = ?, region_no = ?, email = ?, contact_number =? WHERE (area_no = ?);";

            $this->db->query($sql, [$area_name, $area_manager, $region_no,$email,$contact_number, $area_no]);

            return redirect()->to('/area/search');
            exit();

        }


        echo view('area/update'  ,$data);

    }

    public function update() {

        if ($this->request->getPost('form_create_area') == "true") {

            $area_no = $this->request->getPost('area');
            $area_name = trim($this->request->getPost('area_name'));
            $area_manager = trim($this->request->getPost('area_manager'));            
            $region_no = trim($this->request->getPost('region_no'));

            $sql = "Update TBL_AREA SET area_name = ?, area_manager = ?, region_no = ? WHERE (area_no = ?);";

            $this->db->query($sql, [$area_name, $area_manager, $region_no, $area_no]);

            return redirect()->to('/area/search');
            exit();

        }
        else {
            $data["action_type"] = "create";
            $data["url"] = "area/create";

            return redirect()->to('/area/create');
            exit();
        }
    }
}
