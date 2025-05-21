<?php

namespace App\Controllers\Hub;

use \App\Controllers\BaseController;

class Update extends BaseController
{

    public function _remap($method, ...$params)
    {

        if (method_exists($this, $method)) {
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
        
        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/hub/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        } else {

            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_HUB  WHERE HUB_ID = $entity_id;";
            $result = $this->db->query($sqlQuery);
            $found = false;

            foreach ($result->getResult('array') as $row) : {
                    if ($row['COUNT'] > 0) {
                        $found = true;
                    }
                }
            endforeach;

            if ($found) {
                $sqlQuery = "SELECT HUB_ID, HUB_NAME, HUB_DESCR, ST_X(HUB_LOCATION) AS LAT, ST_Y(HUB_LOCATION) AS LNG, REGION_ID FROM TBL_HUB WHERE HUB_ID = $entity_id";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row) : {
                        $data['hub_id'] = $row['HUB_ID'];
                        $data['hub_name'] = $row['HUB_NAME'];
                        $data['hub_descr'] = $row['HUB_DESCR'];
                        $data['region_no'] = $row['REGION_ID'];
                        $data['hub_latitude'] = $row['LAT'];
                        $data['hub_longitude'] = $row['LNG'];
                    }
                endforeach;
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }


        if ($this->request->getPost('form_update_hub') == "true") {
            $hub_id = trim($this->request->getPost('hub'));
            $hub_name = trim($this->request->getPost('hub_name'));
            $hub_descr = trim($this->request->getPost('hub_descr'));
            $region_no = trim($this->request->getPost('region_no'));
            $hub_latitude = trim($this->request->getPost('hub_latitude'));
            $hub_longitude = trim($this->request->getPost('hub_longitude'));

            $sql = "Update TBL_HUB SET hub_name = ?, hub_location = POINT(?,?), hub_descr=?, region_id = ? WHERE (hub_id = ?);";

            $this->db->query($sql, [
                $hub_name,
                replaceDirectionWithValue($hub_latitude),
                replaceDirectionWithValue($hub_longitude),
                $hub_descr, $region_no, $hub_id
            ]);
            return redirect()->to('/hub/search');
            exit();
        }
        echo view('hub/update', $data);
    }
}
