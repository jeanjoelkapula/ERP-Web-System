<?php namespace App\Controllers\Delivery;

use \App\Controllers\BaseController;

class Preview extends BaseController {

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
        //setting action type and url for contractor create/update form
        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/delivery/preview/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "false";
        $data['form_preview'] = "true";
        $data['entity_id'] = $entity_id;
                
        if (!isset($entity_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
                // get the order number                
                $sqlOrder = "SELECT DISTINCT TBLO.ORDER_NO,DATE(TBLO.ORDER_DATE_CREATED) AS ORDER_DATE 
                FROM TBL_ORDER TBLO INNER JOIN TBL_PACKING_BILL TPB ON TBLO.ORDER_NO = TPB.ORDER_NO 
                INNER JOIN TBL_DELIVERY_NOTE TDN ON TPB.PACKING_BILL_ID = TDN.PACKING_BILL_ID WHERE TDN.DELIVERY_ID = $entity_id;";

                $orderResult = $this->db->query($sqlOrder);
        
                // loop through the result to get the average cost of the item
                foreach ($orderResult->getResult('array') as $row) : {                        
                    $ord_no = $row['ORDER_NO']; 
                    $data['ordernum'] = $ord_no;
                    $ord_date = $row['ORDER_DATE'];                        
                }
                endforeach;

                // get the order details
                $sqlOrderDetails = "SELECT TQS.EBQ_CODE,TS.DESCRIPTION,TQS.QUANTITY 
                FROM TBL_ORDER TBLO 
                INNER JOIN TBL_QUOTE TQ ON TBLO.QUOTE_ID = TQ.QUOTE_ID 
                INNER JOIN TBL_QUOTE_STOCK TQS ON TQ.QUOTE_ID = TQS.QUOTE_ID 
                INNER JOIN TBL_STOCK TS ON TQS.EBQ_CODE = TS.EBQ_CODE
                WHERE TBLO.ORDER_NO = '$ord_no';";
                
                
                $resultDetails = $this->db->query($sqlOrderDetails);

                $data['orderObject']=$resultDetails;


                $sqlQuery = "SELECT * FROM TBL_DELIVERY_NOTE WHERE DELIVERY_ID = $entity_id";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row): 
                { 
                    $data['maintenancedate'] = $row['DELIVERY_DATE']; 
                    $data['waybill'] = $row['DELIVERY_WAYBILL']; 
                    $data['notes'] = $row['NOTES']; 
                    $data['packingbill'] = $row['PACKING_BILL_ID']; 
                    $data['is_signed'] = $row['IS_SIGNED_OFF']; 
                    $data['deliverymethod'] = $row['DELIVERY_METHOD']; 
                }
                endforeach;                  

         
        }

        if ($this->request->getPost('form_update_delivery') == "true") {
        
            // get the order chosen
            $orderNumber = $this->request->getPost('create-delivery');

            // get the packing bill id
            $sqlPackingBillID = "SELECT PACKING_BILL_ID FROM TBL_PACKING_BILL WHERE ORDER_NO = '$orderNumber';";

            // print_r($sqlPackingBillID);

            $PBResult = $this->db->query($sqlPackingBillID);

            // get the packing bill id
            foreach ($PBResult->getResult('array') as $row) : {
                $data['packing_bill_id'] = $row['PACKING_BILL_ID']; 
                $pbID = $row['PACKING_BILL_ID'];          
            }
            endforeach;

            $dDate = $this->db->escape(trim($this->request->getPost('maintenancedate')));
            $dWaybill = $this->db->escape(trim($this->request->getPost('waybill_number')));
            $notes = $this->db->escape(trim($this->request->getPost('notes')));
                
            if ($this->request->getPost('is_signed') == 'on') {
                $isSigned = 1;
            }
            else {
                $isSigned = 0;
            }

            $sql = "UPDATE TBL_DELIVERY_NOTE 
            SET 
            DELIVERY_DATE = $dDate,
            DELIVERY_WAYBILL = $dWaybill,
            PRICE = 0,
            NOTES = $notes,
            PACKING_BILL_ID = $pbID,
            IS_SIGNED_OFF = $isSigned
            WHERE 
            DELIVERY_ID = $entity_id;";
    

            // print_r($sql);

            $this->db->query($sql);

            return redirect()->to('/delivery/search');
            exit();

        }
    
        echo view('delivery/preview',$data);
        
    }    
}
    