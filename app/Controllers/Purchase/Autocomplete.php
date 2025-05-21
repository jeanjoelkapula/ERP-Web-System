<?php namespace App\Controllers\Purchase;

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

            if ($this->request->getPost('purchase-updatefill') == "true") {

                $sid =  $this->request->getPost('sid');

                $sql = "SELECT PURCHASE_ORDER_ID, ORDER_DATE, DATE_REQUIRED, SHIP_VIA, VENDOR_NAME, VENDOR_ADDRESS, VENDOR_PO_BOX, VENDOR_ZIP_CODE, TOTAL, FUL_FILLED
                FROM TBL_PURCHASE_ORDER
                WHERE PURCHASE_ORDER_ID = '$sid';";
                $result = $this->db->query($sql);
                $req_update = [];

                foreach($result->getResult() as $row){
                    $req_update['PURCHASE_ORDER_ID'] = $row->PURCHASE_ORDER_ID;
                    $req_update['ORDER_DATE'] = $row->ORDER_DATE;
                    $req_update['DATE_REQUIRED'] = $row->DATE_REQUIRED;
                    $req_update['SHIP_VIA'] = $row->SHIP_VIA;
                    $req_update['VENDOR_NAME'] = $row->VENDOR_NAME;
                    $req_update['VENDOR_ADDRESS'] = $row->VENDOR_ADDRESS;
                    $req_update['VENDOR_PO_BOX'] = $row->VENDOR_PO_BOX;
                    $req_update['VENDOR_ZIP_CODE'] = $row->VENDOR_ZIP_CODE;
                    $req_update['FUL_FILLED'] = $row->FUL_FILLED;
                    $req_update['TOTAL'] = $row->TOTAL;
                }
                
                
                
                return $this->response->setJSON($req_update);
            }

            if ($this->request->getPost('purchase-search') == "true") {
                
                $search_term = $this->db->escape(trim($this->request->getPost('search-value')));                
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT PURCHASE_ORDER_ID, ORDER_DATE, VENDOR_NAME
                FROM TBL_PURCHASE_ORDER
                WHERE ((CAST(PURCHASE_ORDER_ID as CHAR) LIKE $search_term) 
                OR (CAST(ORDER_DATE as CHAR) LIKE $search_term) 
                OR (VENDOR_NAME LIKE $search_term))
                AND (APPROVAL_STATUS = 'APPROVED')
                AND (FUL_FILLED = 0)) C;";
                
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
                    $sql = "SELECT PURCHASE_ORDER_ID, ORDER_DATE, DATE_REQUIRED, SHIP_VIA, VENDOR_NAME, VENDOR_ADDRESS, VENDOR_PO_BOX, VENDOR_ZIP_CODE, TOTAL, FUL_FILLED
                    FROM TBL_PURCHASE_ORDER
                    WHERE ((CAST(PURCHASE_ORDER_ID as CHAR) LIKE $search_term) OR (CAST(ORDER_DATE as CHAR) LIKE $search_term) OR (VENDOR_NAME LIKE $search_term)) AND (APPROVAL_STATUS = 'APPROVED') AND (FUL_FILLED = 0);";
                    $result = $this->db->query($sql);
                    $purchase_order_info = $result->getResult('array');
                    return $this->response->setJSON($purchase_order_info);
                }
                else {
                    //$this->response->setStatusCode(404)
                    //->setBody($found);
                }
            }

            if ($this->request->getPost('purchase-stock-request') == "true") {

                $purchase_id = $this->db->escape(trim($this->request->getPost('purchase-id')));

                // $ebq = $this->db->escape(trim($this->request->getPost('ebq')));
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT P.EBQ_CODE, S.DESCRIPTION,M.METRIC_DESCRIPTION,  P.QUANTITY, P.UNIT_PRICE, SUM(P.QUANTITY*P.UNIT_PRICE) as TOTAL, P.APPROVED 
                FROM TBL_PURCHASE_ORDER_STOCK P, TBL_METRIC M, TBL_STOCK S
                WHERE P.EBQ_CODE  = S.EBQ_CODE 
                AND M.METRIC_ID = S.METRIC_ID
                AND P.PURCHASE_ORDER_ID = $purchase_id
                GROUP BY S.EBQ_CODE, P.QUANTITY, P.UNIT_PRICE, P.APPROVED) C;
                ";
                $result = $this->db->query($sqlQuery);
                $found = 'false';

                foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT']>0) {
                        $found = 'true';
                    }
                }
                endforeach;

                if($found == 'true') {
                    $sql = "SELECT P.EBQ_CODE, S.DESCRIPTION,M.METRIC_DESCRIPTION,  P.QUANTITY, P.UNIT_PRICE, SUM(P.QUANTITY*P.UNIT_PRICE) as TOTAL, IF(P.APPROVED = 1, 'yes', 'no') AS APPROVED
                    FROM TBL_PURCHASE_ORDER_STOCK P, TBL_METRIC M, TBL_STOCK S
                    WHERE P.EBQ_CODE  = S.EBQ_CODE 
                    AND M.METRIC_ID = S.METRIC_ID
                    AND P.PURCHASE_ORDER_ID = $purchase_id
                    GROUP BY S.EBQ_CODE, P.QUANTITY, P.UNIT_PRICE, P.APPROVED;
                    ";
                    
                    $result = $this->db->query($sql);
                    $data = $result->getResult('array');
                    return $this->response->setJSON($data);
                }
                else {
                    // $this->response->setStatusCode(404)
                    // ->setBody($found);
                }
            }

            if ($this->request->getPost('purchase-stock-updatefill') == "true") {

                $purchase_id = $this->db->escape(trim($this->request->getPost('purchase-id')));

                // $ebq = $this->db->escape(trim($this->request->getPost('ebq')));
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT P.EBQ_CODE, S.DESCRIPTION, M.METRIC_DESCRIPTION,  P.QUANTITY, P.COST, SUM(P.QUANTITY*P.COST) as TOTAL, P.APPROVED, P.APPROVAL_NOTE 
                FROM TBL_GRN_STOCK P, TBL_METRIC M, TBL_STOCK S
                WHERE P.EBQ_CODE  = S.EBQ_CODE 
                AND M.METRIC_ID = S.METRIC_ID
                AND P.PURCHASE_ORDER_ID = $purchase_id
                GROUP BY S.EBQ_CODE, P.QUANTITY, P.COST, P.APPROVED, P.APPROVAL_NOTE) C;
                ";
                $result = $this->db->query($sqlQuery);
                $found = 'false';

                foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT']>0) {
                        $found = 'true';
                    }
                }
                endforeach;

                if($found == 'true') {
                    $sql = "SELECT P.EBQ_CODE, S.DESCRIPTION,M.METRIC_DESCRIPTION,  P.QUANTITY, P.COST, SUM(P.QUANTITY*P.COST) as TOTAL, P.APPROVED, P.APPROVAL_NOTE
                    FROM TBL_GRN_STOCK P, TBL_METRIC M, TBL_STOCK S
                    WHERE P.EBQ_CODE  = S.EBQ_CODE 
                    AND M.METRIC_ID = S.METRIC_ID
                    AND P.PURCHASE_ORDER_ID = $purchase_id
                    GROUP BY S.EBQ_CODE, P.QUANTITY, P.COST, P.APPROVED, P.APPROVAL_NOTE;
                    ";
                    
                    $result = $this->db->query($sql);
                    $data = $result->getResult('array');
                    return $this->response->setJSON($data);
                }
                else {
                    // $this->response->setStatusCode(404)
                    // ->setBody($found);
                }
            }
                
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }

    }    
    
}
    