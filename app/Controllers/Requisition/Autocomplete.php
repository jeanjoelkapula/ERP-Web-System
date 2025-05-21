<?php namespace App\Controllers\Requisition;

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
            if ($this->request->getPost('requisition-updatefill') == "true") {

                $sid = (int)$this->request->getPost('sid');

                $sql = "SELECT * 
                    FROM TBL_REQUISITION
                    WHERE REQUISITION_NO = $sid;";
                $result = $this->db->query($sql);
                $req_update = [];

                foreach($result->getResult() as $row){
                    $req_update['REQUISITION_NO'] = $row->REQUISITION_NO;
                    $req_update['REQUISITION_DATE'] = $row->REQUISITION_DATE;
                    $req_update['NOTES'] = $row->NOTES;
                }
                
                
                
                return $this->response->setJSON($req_update);
            }

            if ($this->request->getPost('requisition-search') == "true") {
                
                $search_term =  $this->db->escape(trim($this->request->getPost('search-value')));                
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT *
                FROM TBL_REQUISITION
                WHERE ((CAST(REQUISITION_NO as CHAR) LIKE $search_term) 
                OR (CAST(REQUISITION_DATE as CHAR) LIKE $search_term)
                OR (CAST(NOTES as CHAR) LIKE $search_term)
                OR (CAST(EBQ_CODE as CHAR) LIKE $search_term))
                AND (APPROVAL_STATUS = 'APPROVED')
                AND (IS_COMPLETE = 0))C;";
                
                $result = $this->db->query($sqlQuery);
                $found = 'false';

                foreach ($result->getResult() as $row): 
                { 
                    if ($row->COUNT > 0) {
                        $found = 'true';
                    }
                }
                endforeach;

                if($found == 'true') {
                    $sql = "SELECT *
                    FROM TBL_REQUISITION
                    WHERE ((CAST(REQUISITION_NO as CHAR) LIKE $search_term) 
                    OR (CAST(REQUISITION_DATE as CHAR) LIKE $search_term)
                    OR (CAST(NOTES as CHAR) LIKE $search_term)
                    OR (EBQ_CODE LIKE $search_term))
                    AND (APPROVAL_STATUS = 'APPROVED')
                    AND (IS_COMPLETE = 0);";
                    $result = $this->db->query($sql);
                    $requisition_info = $result->getResult('array');
                    return $this->response->setJSON($requisition_info);
                }
                else {
                    //$this->response->setStatusCode(404)
                    //->setBody($found);
                }
            }

            if ($this->request->getPost('requisition-stock-request') == "true") {

                $ebq_code = $this->db->escape(trim($this->request->getPost('ebq-code')));

                // $ebq = $this->db->escape(trim($this->request->getPost('ebq')));
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT S.*, SC.QUANTITY FROM TBL_STOCK_COMBINATION SC
                INNER JOIN 
                    (SELECT S.*, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                    INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID) S
                ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                WHERE (SC.EBQ_CODE_LG = $ebq_code)) C;
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
                    $sql = "SELECT S.*, SC.QUANTITY FROM TBL_STOCK_COMBINATION SC
                    INNER JOIN 
                        (SELECT S.*, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                        INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID) S
                    ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                    WHERE (SC.EBQ_CODE_LG = $ebq_code);";
                    
                    $result = $this->db->query($sql);
                    $requisition_stock = $result->getResult('array');
                    return $this->response->setJSON($requisition_stock);
                }
                else {
                    // $this->response->setStatusCode(404)
                    // ->setBody($found);
                }
            }

            if ($this->request->getPost('requisition-stock-updatefill') == "true") {

                $requisition_id = $this->db->escape(trim($this->request->getPost('requisition-id')));

                // $ebq = $this->db->escape(trim($this->request->getPost('ebq')));
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM (SELECT R.EBQ_CODE, S.DESCRIPTION, M.METRIC_DESCRIPTION,  R.QUANTITY, R.APPROVED, R.APPROVAL_NOTE 
                FROM TBL_GRN_STOCK R, TBL_METRIC M, TBL_STOCK S
                WHERE R.EBQ_CODE  = S.EBQ_CODE 
                AND M.METRIC_ID = S.METRIC_ID
                AND R.REQUISITION_NO = $requisition_id
                GROUP BY S.EBQ_CODE, R.QUANTITY,  R.APPROVED, R.APPROVAL_NOTE) C;
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
                    $sql = "SELECT R.REQUISITION_NO, R.EBQ_CODE, S.DESCRIPTION, M.METRIC_DESCRIPTION,  R.QUANTITY,  R.APPROVED, R.APPROVAL_NOTE
                    FROM TBL_GRN_STOCK R, TBL_METRIC M, TBL_STOCK S
                    WHERE R.EBQ_CODE  = S.EBQ_CODE 
                    AND M.METRIC_ID = S.METRIC_ID
                    AND R.REQUISITION_NO = $requisition_id
                    GROUP BY R.EBQ_CODE, R.QUANTITY,  R.APPROVED, R.APPROVAL_NOTE;
                    ";
                    
                    $result = $this->db->query($sql);
                    $requisition_stock = $result->getResult('array');
                    return $this->response->setJSON($requisition_stock);
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
    