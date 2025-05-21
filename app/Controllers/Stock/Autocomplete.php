<?php namespace App\Controllers\Stock;

use \App\Controllers\BaseController;

class Autocomplete extends BaseController {

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
        try {
            if ($this->request->getPost('stock-search') == "true") {

                $stock_name = $this->db->escape(trim($this->request->getPost('search-value')));
                // $ebq = $this->db->escape(trim($this->request->getPost('ebq')));
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT S.EBQ_CODE, S.DESCRIPTION, HS.QUANTITY FROM TBL_STOCK S
                INNER JOIN TBL_HUB_STOCK HS ON HS.EBQ_CODE = S.EBQ_CODE WHERE (S.DESCRIPTION LIKE $stock_name) OR (S.EBQ_CODE LIKE $stock_name)) R;";
                $result = $this->db->query($sqlQuery);
                $found = 'false';

                foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT'] > 0) {
                        $found = 'true';
                    }
                }
                endforeach;

                if($found == 'true') {
                    $sql = "
                    SELECT S.EBQ_CODE, S.DESCRIPTION, HS.QUANTITY, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                    INNER JOIN TBL_HUB_STOCK HS ON HS.EBQ_CODE = S.EBQ_CODE 
                    INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID
                    WHERE (S.DESCRIPTION LIKE $stock_name) OR (S.EBQ_CODE LIKE $stock_name)
                    AND S.IS_ACTIVE = 1;";
                    $result = $this->db->query($sql);
                    $data = $result->getResult('array');
                    return $this->response->setJSON($data);
                }
                else {
                    //$this->response->setStatusCode(404)
                    //->setBody($found);
                }
            }

            if (($this->request->getPost('stock-hub-search') == "true") && (!empty($this->request->getPost('search-value')))) {

                $hub_id = $this->db->escape(trim($this->request->getPost('hub-id')));

                // $ebq = $this->db->escape(trim($this->request->getPost('ebq')));
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT S.EBQ_CODE, S.DESCRIPTION, HS.QUANTITY FROM TBL_STOCK S
                INNER JOIN TBL_HUB_STOCK HS ON HS.EBQ_CODE = S.EBQ_CODE WHERE (HS.HUB_ID = $hub_id) AND (S.IS_ACTIVE = 1);";
                $result = $this->db->query($sqlQuery);
                $found = 'false';

                foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT'] > 0) {
                        $found = 'true';
                    }
                }
                endforeach;

                if($found == 'true') {
                    $sql = "
                    SELECT S.EBQ_CODE, S.DESCRIPTION,S.AVG_COST, HS.QUANTITY, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                    INNER JOIN TBL_HUB_STOCK HS ON HS.EBQ_CODE = S.EBQ_CODE 
                    INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID
                    WHERE (HS.HUB_ID = $hub_id) AND (S.IS_ACTIVE = 1);";

                    $result = $this->db->query($sql);
                    $data = $result->getResult('array');
                    return $this->response->setJSON($data);
                }
                else {
                    // $this->response->setStatusCode(404)
                    // ->setBody($found);
                }
            }

            if ($this->request->getPost('stock-hub-search') == "true") {

                $hub_id = $this->db->escape(trim($this->request->getPost('hub-id')));
    
                $sql = "
                SELECT S.EBQ_CODE, S.DESCRIPTION, HS.QUANTITY, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                INNER JOIN TBL_HUB_STOCK HS ON HS.EBQ_CODE = S.EBQ_CODE 
                INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID
                WHERE (HS.HUB_ID = $hub_id) AND S.IS_ACTIVE = 1;";

                $result = $this->db->query($sql);
                $data = $result->getResult('array');
                return $this->response->setJSON($data);
            }
                
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }

    }    
    
}
    