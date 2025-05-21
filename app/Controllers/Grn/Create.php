<?php namespace App\Controllers\Grn;

use \App\Controllers\BaseController;

class Create extends BaseController {

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        
        if ($this->request->getPost('form_create_grn') == "true") {

            $source_id = $this->request->getPost('source-id');
            $type = $this->request->getPost('grn-type');
            $recieved_date = $this->db->escape(trim($this->request->getPost('grn_date')));
            $hub_name = $this->db->escape(trim($this->request->getPost('hub_var')));

            
            $user_id = $data["_user_id"];

            // get the hub ID
            $hub_id;        
            $hubIDSQL = "SELECT HUB_ID FROM TBL_HUB WHERE HUB_NAME = $hub_name;";
            $hubIDQuery = $this->db->query($hubIDSQL);
            foreach ($hubIDQuery->getResult('array') as $row){
                $hub_id = $row['HUB_ID'];
            }

            if($type == "purchase"){
                $insertGRNSQL = "INSERT INTO TBL_GRN (RECEIVED_DATE, HUB_ID, FK_USER, PURCHASE_ORDER_NO) VALUES ($recieved_date, $hub_id, $user_id,  '$source_id')";
                $this->db->query($insertGRNSQL);

                
                $grn_id;
                $getGRNIDSQL = "SELECT LAST_INSERT_ID() AS ID";
                $getGRNIDQuery = $this->db->query($getGRNIDSQL);
                foreach ($getGRNIDQuery->getResult('array') as $row){
                    $grn_id = $row['ID'];
                }

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
                        array_push($approvals, "1");   
                    }else{
                        array_push($approvals, "0"); 
                        $order_fulfilled = 0;
                    }
                    
                }
                
                foreach($note_textboxes as $textbox){
                    
                    array_push($notes, $this->db->escape(trim($textbox)));                    
                    
                }
                
                $insertBridgeTableSQL = "INSERT INTO TBL_GRN_STOCK (GRN_ID, EBQ_CODE, QUANTITY, COST, APPROVED, APPROVAL_NOTE, PURCHASE_ORDER_ID) VALUES ";

                for($i = 0;$i < (sizeof($ebqs));$i++){
                    $insertBridgeTableSQL .= "(
                    $grn_id,'"
                    .$ebqs[$i]
                    ."',".$quantities[$i]
                    .",".$prices[$i]
                    .",'".$approvals[$i]
                    ."',".$notes[$i]
                    .",'".$source_id."'),";
                    
                }  

                $insertBridgeTableSQL = substr($insertBridgeTableSQL,0,strlen($insertBridgeTableSQL)-1);
                $insertBridgeTableSQL .= ";";

                $this->db->query($insertBridgeTableSQL);

                //update stock item purchase costs and avgs
                for($i = 0;$i < (sizeof($ebqs));$i++){
                    if ($approvals[$i] == 1){
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

                        //find item in hub stock
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
                    }
                    
                }

                //update purchase fulfilled status
                $sql = "UPDATE TBL_PURCHASE_ORDER SET FUL_FILLED = $order_fulfilled WHERE PURCHASE_ORDER_ID = '$source_id';";
                $this->db->query($sql);

                return redirect()->to('/grn/search');
                exit();
            }
            elseif($type == "requisition"){
                //insert data into TBL_GRN first                
                $insertGRNSQL = "INSERT INTO TBL_GRN (RECEIVED_DATE, HUB_ID, FK_USER, REQUISITION_NO) VALUES ($recieved_date, $hub_id, $user_id,  $source_id)";
                $this->db->query($insertGRNSQL);

                $sql = "SELECT EBQ_CODE FROM TBL_REQUISITION WHERE REQUISITION_NO = $source_id";
                $result = $this->db->query($sql);

                $ebq_code = $result->getResult('array')[0]['EBQ_CODE'];
                
                //find item in hub stock
                $itemFound = false;

                $sql = "SELECT COUNT(*) AS COUNT FROM TBL_HUB_STOCK WHERE (EBQ_CODE = '$ebq_code') AND (HUB_ID = $hub_id);";
                $searchResult = $this->db->query($sql);

                if ($searchResult->getResult('array')[0]['COUNT'] > 0) {
                    $itemFound = true;
                }

                if ($itemFound){
                    $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY + 1) WHERE (EBQ_CODE = '$ebq_code') AND (HUB_ID = $hub_id);";
                }
                else {
                    $sql = "INSERT INTO TBL_HUB_STOCK VALUES ($hub_id, '$ebq_code', 1);";
                }
                $this->db->query($sql);

                $sql = "UPDATE TBL_REQUISITION SET IS_COMPLETE = 1 WHERE REQUISITION_NO = $source_id;";
                $this->db->query($sql);

                $sql = "SELECT HUB_ID FROM TBL_REQUISITION WHERE REQUISITION_NO = $source_id";
                $source_hub = $this->db->query($sql)->getResultArray()[0]['HUB_ID'];

                $sql = "SELECT SC.* FROM TBL_REQUISITION R
                    INNER JOIN TBL_STOCK_COMBINATION SC ON SC.EBQ_CODE_LG = R.EBQ_CODE
                    WHERE SC.EBQ_CODE_LG = '$ebq_code' AND R.REQUISITION_NO = $source_id;";

                $sub_items = $this->db->query($sql)->getResultArray();

                foreach($sub_items as $item) {
                    $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY - ".$item['QUANTITY'].") WHERE (EBQ_CODE = '".$item['EBQ_CODE_SUB']."') AND (HUB_ID = $source_hub);";

                    $this->db->query($sql);
                }

                return redirect()->to('/grn/search');
                exit();

            }else{
                echo view("/errors/html/production");
                exit();
            }   
            
        } 
       
        echo view('grn/create',$data);
        
    }    
    
}
    