<?php namespace App\Controllers\Grn;

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        
        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $data['ent_id'] = $entity_id;

            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_GRN  WHERE GRN_ID = $entity_id;";
            $result = $this->db->query($sqlQuery);
            $found = false;

            foreach ($result->getResult('array') as $row) : {
                    if ($row['COUNT'] > 0) {
                        $found = true;
                    }
                }
            endforeach;

            if ($found) {
                $sqlQuery = "SELECT * FROM TBL_GRN  WHERE GRN_ID = '$entity_id';";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row) : {
                    $data['grn_id'] = $row['GRN_ID'];
                    $data['received_date'] = $row['RECEIVED_DATE'];
                    $data['hub_id'] = $row['HUB_ID'];
                    $data['rid'] = $row['REQUISITION_NO'];
                    $data['pid'] = $row['PURCHASE_ORDER_NO'];
                    
                }
                endforeach;
                
                $hub_idfill = $data['hub_id'];
                $sqlQuery = "SELECT HUB_NAME FROM  TBL_HUB WHERE HUB_ID = $hub_idfill;";
                $result = $this->db->query($sqlQuery);
                foreach ($result->getResult('array') as $row) : {
                    $data['hub_name'] = $row['HUB_NAME'];                    
                }endforeach;

                if ($data['rid'] != null) {

                    $sql = "SELECT EBQ_CODE FROM TBL_REQUISITION WHERE REQUISITION_NO =".$data['rid'];
                    $result = $this->db->query($sql);
                    $ebq_code = $result->getResult('array')[0]['EBQ_CODE'];

                    $sql = "SELECT S.*, SC.QUANTITY FROM TBL_STOCK_COMBINATION SC
                    INNER JOIN 
                        (SELECT S.*, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                        INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID) S
                    ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                    WHERE (SC.EBQ_CODE_LG = '$ebq_code');";

                    $result = $this->db->query($sql);

                    $data['sub_items'] = $result->getResult('array'); 
                    $data['ebq_code'] = $ebq_code;
                }
                else {
                    $sqlQuery = "SELECT R.*, GT.QUANTITY, GT.COST FROM TBL_GRN_STOCK GT
                    INNER JOIN 
                        (SELECT S.*, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                            INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID) R
                        ON R.EBQ_CODE = GT.EBQ_CODE 
                    WHERE (GT.GRN_ID = '$entity_id');";

                    $result = $this->db->query($sqlQuery);

                    $data['stock_items'] = $result->getResult('array'); 
                }

                   
                
                
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();
                
            }

            if ($this->request->getPost('form_update_grn') == "true") {
                $recieved_date = $this->db->escape(trim($this->request->getPost('grn_date')));
                $hub_name = $this->request->getPost('hub_var');
                $grn_id = $entity_id;
                $user_id = $data["_user_id"];
                
                $hub_id;
                $dasql = "SELECT HUB_ID from TBL_HUB where HUB_NAME = '$hub_name'";
                $hubresult = $this->db->query($dasql);

                foreach ($hubresult->getResult() as $row): {
                    $hub_id = $row->HUB_ID;
                }endforeach;

                //get previous hub id
                $sql = "SELECT HUB_ID FROM TBL_GRN WHERE GRN_ID = $grn_id;";
                $result = $this->db->query($sql)->getResultArray()[0];

                $previous_hub = $result['HUB_ID'];

                //update TBL_GRN first                
                $insertGRNSQL = "UPDATE TBL_GRN SET RECEIVED_DATE = $recieved_date, HUB_ID = $hub_id, FK_USER = $user_id WHERE GRN_ID = $grn_id";  
                $this->db->query($insertGRNSQL);
                
    
                $source_id = $this->request->getPost('source-id');
                $type = $this->request->getPost('grn-type');

                if($type == "purchase"){
                    //get previously entered stock
                    $sql = "SELECT * FROM TBL_GRN_STOCK WHERE GRN_ID = $grn_id";
                    $old_stock = $this->db->query($sql)->getResultArray();

                    $deleteSql= "DELETE FROM TBL_GRN_STOCK WHERE GRN_ID = $grn_id";
                    $this->db->query($deleteSql);
                    
                    //bridge table columns
                    $ebqs = $this->request->getPost('ebqs');
                    $quantities = $this->request->getPost('quantities');
                    $prices = $this->request->getPost('prices');
                    
                    //convert input controls to coulunm fields
                    $approval_checkboxes = $this->request->getPost('checkboxes');
                    $note_textboxes = $this->request->getPost('textboxes');
                    $approvals = [];
                    $notes = [];

                    $order_fulfilled = 1;
                    foreach($approval_checkboxes as $checkbox){
                        if($checkbox == "1"){
                            array_push($approvals, 1);   
                        }else{
                            array_push($approvals, 0); 
                        }  
                    }

                    foreach($note_textboxes as $textbox){
                        
                        array_push($notes, $textbox);                    
                        
                    }

                    $insertBridgeTableSQL = "INSERT INTO TBL_GRN_STOCK (GRN_ID, EBQ_CODE, QUANTITY, COST, APPROVED, APPROVAL_NOTE, PURCHASE_ORDER_ID) VALUES ";

                    for($i = 0;$i < (sizeof($ebqs));$i++){
                        $insertBridgeTableSQL .= "(
                        $grn_id,'"
                        .$ebqs[$i]
                        ."',".$quantities[$i]
                        .",".$prices[$i]
                        .",'".$approvals[$i]
                        ."','".$notes[$i]
                        ."','".$source_id."'),";
                    }  

                    $insertBridgeTableSQL = substr($insertBridgeTableSQL,0,strlen($insertBridgeTableSQL)-1);
                    $insertBridgeTableSQL .= ";";

                    $this->db->query($insertBridgeTableSQL);

                    //deduct quantities at previous hub
                    if ($previous_hub != $hub_id) {
                        foreach($old_stock as $row){
                            $quantity = $row['QUANTITY'];
                            $ebq = $row['EBQ_CODE'];
                            if ($row['APPROVED'] == 1) {
                                $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY - $quantity) WHERE (EBQ_CODE = '$ebq') AND (HUB_ID = $previous_hub);";
                                $this->db->query($sql);

                                $itemFound = false;

                                $sql = "SELECT COUNT(*) AS COUNT FROM TBL_HUB_STOCK WHERE (EBQ_CODE = '$ebq') AND (HUB_ID = $hub_id);";
                                $searchResult = $this->db->query($sql);

                                if ($searchResult->getResult('array')[0]['COUNT'] > 0) {
                                    $itemFound = true;
                                }

                                if ($itemFound) {
                                    $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY + $quantity) WHERE (EBQ_CODE = '$ebq') AND (HUB_ID = $hub_id);";
                                }
                                else {
                                    $sql = "INSERT INTO TBL_HUB_STOCK VALUES ($hub_id, '$ebq', $quantity);";
                                }
                                $this->db->query($sql);

                            }
                            
                        }
                    }
                   

                    //update stock item purchase costs and avgs
                    for($i = 0;$i < (sizeof($ebqs));$i++){
                        if ($approvals[$i] == 1){
                            //check if item has already been received
                            $itemReceived = false;

                            foreach($old_stock as $row) {
                                if (($row['EBQ_CODE'] == $ebqs[$i]) && ($row['APPROVED'] == 1)) {
                                    $itemReceived = true;
                                }
                            }

                            //find item in hub stock
                            if (!$itemReceived){
                                $itemFound = false;

                                $sql = "SELECT COUNT(*) AS COUNT FROM TBL_HUB_STOCK WHERE (EBQ_CODE = '$ebqs[$i]') AND (HUB_ID = $hub_id);";
                                $searchResult = $this->db->query($sql);

                                if ($searchResult->getResult('array')[0]['COUNT'] > 0) {
                                    $itemFound = true;
                                }

                                if ($itemFound){
                                    $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY + $quantities[$i]) WHERE (EBQ_CODE = '$ebqs[$i]') AND (HUB_ID = $hub_id);";
                                }
                                else {
                                    $sql = "INSERT INTO TBL_HUB_STOCK VALUES ($hub_id, '$ebqs[$i]', $quantities[$i]);";
                                }
                            
                                $this->db->query($sql);

                                $oldQuantity = 0;
                                $sqlSubtotalOld = "SELECT SUM(QUANTITY) AS QUANTITY FROM TBL_HUB_STOCK WHERE EBQ_CODE = '".$ebqs[$i]."'";
                
                                $oldSubtotalResult = $this->db->query($sqlSubtotalOld);
                                foreach ($oldSubtotalResult->getResult('array') as $row) : {
                                        
                                        if ($row['QUANTITY'] > 0) {
                                            $foundOldStock = true;
                                            $oldQuantity = $row['QUANTITY'];
                                        }
                                    }
                                endforeach;

                                // get the last cost for the item
                                $sqlCost = "SELECT PURCHASE_COST, AVG_COST FROM TBL_STOCK WHERE EBQ_CODE = '$ebqs[$i]';";
                                $costPreviously = 0;

                                $costResult = $this->db->query($sqlCost);
                                foreach ($costResult->getResult('array') as $row) : {
                                    $costPreviously = $row['PURCHASE_COST'];
                                }
                                endforeach;
                                
                                $oldSubtotal = ($oldQuantity * $costPreviously);
                                $newSubtotal = ($quantities[$i] * (float)$prices[$i]);

                                $newAvg = ($oldSubtotal + $newSubtotal) / ($oldQuantity + $quantities[$i]);

                                $sqlUpdateTBLSTOCK = 
                                "UPDATE TBL_STOCK 
                                SET
                                PURCHASE_COST = $prices[$i],
                                AVG_COST = $newAvg,
                                LAST_COST = $costPreviously
                                
                                WHERE
                                EBQ_CODE = '$ebqs[$i]';";
                                                
                                $this->db->query($sqlUpdateTBLSTOCK);

                            }

                        }
                        
                    }

                    //update purchase fulfilled status
                    $sql = "UPDATE TBL_PURCHASE_ORDER SET FUL_FILLED = $order_fulfilled WHERE PURCHASE_ORDER_ID = '$source_id';";
                    $this->db->query($sql);

                    return redirect()->to('/grn/search');
                    exit();
                }
                else {
                    if ($previous_hub != $hub_id) {
                        $sql = "SELECT EBQ_CODE FROM TBL_REQUISITION WHERE REQUISITION_NO = $source_id";
                        $result = $this->db->query($sql);
  
                        $ebq = $result->getResult('array')[0]['EBQ_CODE'];
                        
                        $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY - 1) WHERE (EBQ_CODE = '$ebq') AND (HUB_ID = $previous_hub);";
                        $this->db->query($sql);

                        $itemFound = false;

                        $sql = "SELECT COUNT(*) AS COUNT FROM TBL_HUB_STOCK WHERE (EBQ_CODE = '$ebq') AND (HUB_ID = $hub_id);";
                        $searchResult = $this->db->query($sql);

                        if ($searchResult->getResult('array')[0]['COUNT'] > 0) {
                            $itemFound = true;
                        }

                        if ($itemFound) {
                            $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY + 1) WHERE (EBQ_CODE = '$ebq') AND (HUB_ID = $hub_id);";
                        }
                        else {
                            $sql = "INSERT INTO TBL_HUB_STOCK VALUES ($hub_id, '$ebq', 1);";
                        }
                        $this->db->query($sql);
                    }
                }    
            
                return redirect()->to('/grn/search');
                exit();
            }  
        }

        
       
        echo view('grn/update',$data);
        
    } 
    
}
    