<?php namespace App\Controllers\Packing;

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
        
    
        public function index($entity_id=0)
        {   
            if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
                return redirect()->to('/noAccess');
                exit();
            }

            $data = $this->data;
            $data["action_type"] = "preview";
            $data["url"] = "/packing/preview/$entity_id";   
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
                     $sql = "SELECT ORDER_NO, INTERNAL_ORDER_NO FROM TBL_PACKING_BILL WHERE PACKING_BILL_ID = $entity_id";
                     $result = $this->db->query($sql)->getResultArray();

                    if ($result[0]['ORDER_NO'] != null){
                        $data['is_internal'] = false;
                        $sql = "SELECT QUOTE_ID, VOC_ID FROM TBL_ORDER WHERE ORDER_NO = '".$result[0]['ORDER_NO']."';";
                        $rs = $this->db->query($sql)->getResultArray();

                        if ($rs[0]['QUOTE_ID'] != null) {
                            

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
                                $data['destination_hub'] = $row['HUB_NAME'];
                                $data['deliver_to_site'] = $row['DELIVER_TO_SITE'];
                            }
                            endforeach;
                        }
                        else {

                            $sql = "SELECT PB.*, H.HUB_NAME FROM TBL_PACKING_BILL PB 
                            INNER JOIN TBL_HUB H ON H.HUB_ID = PB.DESTINATION_HUB
                            WHERE PACKING_BILL_ID = $entity_id;";
                            $r = $this->db->query($sql);

                            // Return Quote information
                            foreach ($r->getResult('array') as $row): 
                                { 
                                    $data['packing_bill_id'] = $row['PACKING_BILL_ID']; 
                                    $data['ship_via'] = $row['SHIP_VIA'];
                                    $data['delivery_date'] = $row['DELIVERY_DATE'];
                                    $data['pack_date'] = $row['PACK_DATE'];
                                    $data['destination_hub'] = $row['HUB_NAME'];
                                    $data['deliver_to_site'] = $row['DELIVER_TO_SITE'];
                                }
                                endforeach;

                            $sql = "SELECT V.ORDER_NO FROM TBL_ORDER O
                            INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID
                            WHERE O.ORDER_NO =  '".$result[0]['ORDER_NO']."';";
                            $result = $this->db->query($sql)->getResultArray();

                            $sql = "SELECT O.ORDER_NO, Q.*, S.STORE_ID, S.STORE_NAME FROM TBL_ORDER O
                            INNER JOIN TBL_QUOTE Q ON Q.QUOTE_ID = O.QUOTE_ID
                            INNER JOIN TBL_STORE S ON S.STORE_ID = Q.STORE_ID
                            WHERE O.ORDER_NO = '".$result[0]['ORDER_NO']."';";
                            $result = $this->db->query($sql)->getResultArray();

                            foreach ($result as $row): 
                            {
                                $data['store']['id'] = $row['STORE_ID']; 
                                $data['store']['name'] = $row['STORE_NAME']; 
                            }
                            endforeach;
                        }
                    }
                    else {
                        $data['is_internal'] = true;

                        $sql = "SELECT * FROM TBL_PACKING_BILL bill WHERE PACKING_BILL_ID = $entity_id;";
                        $result = $this->db->query($sql);

                        // Return Quote information
                        foreach ($result->getResult('array') as $row): 
                        { 
                            $data['packing_bill_id'] = $row['PACKING_BILL_ID']; 
                            if ($row['ORDER_NO'] != null) {
                                $data['order_no'] = $row['ORDER_NO']; 
                            }
                            else {
                                $data['order_no'] = $row['INTERNAL_ORDER_NO']; 
                            }
                            $data['delivery_date'] = $row['DELIVERY_DATE']; 
                            $data['pack_date'] = $row['PACK_DATE']; 
                            $data['created_date'] = $row['CREATED_DATE']; 
                            $data['ship_via'] = $row['SHIP_VIA'];

                            $sql = "SELECT HUB_NAME FROM TBL_HUB WHERE HUB_ID = ".$row['DESTINATION_HUB'];
                            $hub = $this->db->query($sql)->getResultArray()[0];

                            $data['destination_hub'] = $hub['HUB_NAME'];

                            $sql = "SELECT HUB_NAME FROM TBL_HUB WHERE HUB_ID = ".$row['SOURCE_HUB_ID'];
                            $hub = $this->db->query($sql)->getResultArray()[0];

                            $data['source_hub'] = $hub['HUB_NAME'];
                            $data['status'] = $row['STATUS'];
                            $data['deliver_to_site'] = $row['DELIVER_TO_SITE'];
                        }
                        endforeach;
                    }
                    
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
                           
        echo view('packing/packing_bill_print_preview',$data);
        
    }
    
    
}
    