<?php namespace App\Controllers\Delivery;

use \App\Controllers\BaseController;

class Printer extends BaseController {

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
        $data["url"] = "/delivery/printer/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "false";
        $data['form_preview'] = "false";
        $data['form_print'] = "true";
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
        echo view('delivery/delivery_note_print', $data);        
    }          
}
    