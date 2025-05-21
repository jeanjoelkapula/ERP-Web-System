<?php namespace App\Controllers\Packing;

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
        
    
        public function index($entity_id=0)
        {   
            if(!($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('stock_controller') || $this->ionAuth->inGroup('admin'))){
                return redirect()->to('/noAccess');
                exit();
            }
            $data = $this->data;
            $data["action_type"] = "printer";
            $data["url"] = "/packing/printer/$entity_id";   
            if (!isset($entity_id) || ($entity_id <= 0)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();
            }
            else {
                 // Check if the quote exists in TBL_QUOTE
                 $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_PACKING_BILL  WHERE PACKING_BILL_ID = '$entity_id';";
                 $result = $this->db->query($sqlQuery);
                 $packing_bill_found = false;
     
                 foreach ($result->getResult('array') as $row): 
                 { 
                     if ($row['COUNT'] > 0) {
                         $packing_bill_found = true;
                     }
                 }
                 endforeach;
     
                 // Check if the quote exists in TBL_QUOTE_STOCK
                 $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_PACKING_BILL_STOCK WHERE PACKING_BILL_ID = '$entity_id';";
                 $result = $this->db->query($sqlQuery);
                 $stock_found = false;
     
                 foreach ($result->getResult('array') as $row): 
                     { 
                         if ($row['COUNT'] > 0) {
                             $stock_found = true;
                         }
                     }
                     endforeach;
     
     
     
                 if ($packing_bill_found) {
                     $sql = "SELECT pb.*,s.STORE_ID,s.STORE_NAME,u.first_name,u.last_name,u.phone,h.HUB_NAME FROM TBL_PACKING_BILL pb 
                     JOIN TBL_ORDER o ON o.ORDER_NO = pb.ORDER_NO 
                     JOIN TBL_QUOTE q ON q.QUOTE_ID = o.QUOTE_ID
                     JOIN TBL_STORE s ON s.STORE_ID = q.STORE_ID
                     JOIN TBL_USER u  ON u.id = q.USER_ID
                     LEFT JOIN TBL_HUB h   ON h.HUB_ID = pb.DESTINATION_HUB
                     WHERE pb.PACKING_BILL_ID = $entity_id;";
                     $result = $this->db->query($sql);
     
                     // Return Quote information
                     foreach ($result->getResult('array') as $row): 
                     { 
                         $data['packing_bill_id'] = $row['PACKING_BILL_ID']; 
                         $data['store']['id'] = $row['STORE_ID']; 
                         $data['store']['name'] = $row['STORE_NAME']; 
                         $data['ship_via'] = $row['SHIP_VIA'];
                         $data['delivery_date'] = $row['DELIVERY_DATE'];
                         $data['pack_date'] = $row['PACK_DATE'];
                         $data['created_by']['name'] = $row['first_name'].' '.$row['last_name'];
                         $data['created_by']['contact']= $row['phone'];
                         $data['destination_hub'] = $row['HUB_NAME'];
                         $data['deliver_to_site'] = $row['DELIVER_TO_SITE'];
                     }
                     endforeach;
                 }
                 else {
                     throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                 }
     
                 if($stock_found){
                     $sql = "SELECT * FROM TBL_PACKING_BILL_STOCK pbs JOIN TBL_STOCK stock ON stock.EBQ_CODE = pbs.EBQ_CODE WHERE pbs.PACKING_BILL_ID = $entity_id;";
                     $result = $this->db->query($sql);
     
                     // Return Stock information
                     $selected_stock = array();
                     foreach ($result->getResult() as $row): 
                     { 
                        array_push($selected_stock,$row);
                     }
                     endforeach;
                     $data['stock_to_pack'] = $selected_stock;
    
                 }
             
            }               
                           
        echo view('packing/packing_bill_print',$data);
        
    }
    
    
}
    