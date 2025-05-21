<?php

namespace App\Controllers\Quotecategory;

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
        $data["url"] = "/quotecategory/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        } else {

            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_QUOTE_STOCK_CATEGORY  WHERE ID = $entity_id;";
            $result = $this->db->query($sqlQuery);
            $found = false;

            foreach ($result->getResult('array') as $row) : {
                    if ($row['COUNT'] > 0) {
                        $found = true;
                    }
                }
            endforeach;

            if ($found) {
                $sqlQuery = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY WHERE ID = $entity_id;";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row) : {
                        $data['category_id'] = $row['ID'];
                        $data['category_name'] = $row['NAME'];
      
                    }
                endforeach;
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }


        if ($this->request->getPost('form_update_quote_category') == "true") {
            $category_id = trim($this->request->getPost('category_id'));
            $category_name = trim($this->request->getPost('category_name'));

            $sql = "Update TBL_QUOTE_STOCK_CATEGORY SET name = ? WHERE (id = ?);";

            $this->db->query($sql, [
                $category_name, $category_id
            ]);
            return redirect()->to('/quotecategory/search');
            exit();
        }
        echo view('quotecategory/update', $data);
    }
}
