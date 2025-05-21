<?php namespace App\Controllers\Quote;

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
            if(!($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('electrical_admin') || $this->ionAuth->inGroup('admin'))){
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
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_QUOTE  WHERE QUOTE_ID = '$entity_id';";
                $result = $this->db->query($sqlQuery);
                $quote_found = false;
    
                foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT'] > 0) {
                        $quote_found = true;
                    }
                }
                endforeach;
    
                // Check if the quote exists in TBL_QUOTE_STOCK
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_QUOTE_STOCK  WHERE QUOTE_ID = '$entity_id';";
                $result = $this->db->query($sqlQuery);
                $stock_found = false;
    
                foreach ($result->getResult('array') as $row): 
                    { 
                        if ($row['COUNT'] > 0) {
                            $stock_found = true;
                        }
                    }
                    endforeach;
    
    
    
                if ($quote_found) {
                    $sql = "SELECT quote.*,actor.first_name,action_type.ACTION_NAME,actor.last_name,actor.phone, store.STORE_NAME,quote_type.TYPE_NAME,contractor.CONTRACTOR_NAME  
                       FROM TBL_QUOTE quote 
                       JOIN TBL_QUOTE_TYPE quote_type 
                           ON quote_type.TYPE_ID = quote.QUOTE_TYPE_ID 
                       JOIN TBL_STORE store 
                           ON store.STORE_ID = quote.STORE_ID
                       JOIN TBL_CONTRACTOR contractor
                           ON contractor.CONTRACTOR_ID = quote.CONTRACTOR_ID
                       JOIN TBL_USER actor
                           ON actor.id = quote.USER_ID
                       JOIN TBL_ACTION_TYPE action_type
                           ON action_type.ACTION_ID = quote.ACTION_ID
                       WHERE QUOTE_ID = $entity_id;";
                    $result = $this->db->query($sql);
    
                    // Return Quote information
                    foreach ($result->getResult('array') as $row): 
                    { 
                        $data['quote_id'] = $row['QUOTE_ID']; 
                        $data['quote_type_name'] = $row['TYPE_NAME']; 
                        $data['store']['id'] = $row['STORE_ID']; 
                        $data['store']['name'] = $row['STORE_NAME']; 
                        $data['contractor']['name'] = $row['CONTRACTOR_NAME']; 
                        $data['action_name'] = $row['ACTION_NAME']; 
                        $data['ship_via'] = $row['SHIP_VIA'];
                        $data['delivery_date'] = $row['DELIVERY_DATE'];
                        $data['note'] = $row['NOTE'];
                        $data['created_by']['name'] = $row['first_name'].' '.$row['last_name'];
                        $data['created_by']['contact']= $row['phone'];
                        $data['created_date'] = $row['CREATED_DATE'];
                        $data['pki_percentage'] = $row['PKI_PERCENTAGE'];
   
                    }
                    endforeach;
                }
                else {
                    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                }
    
                if($stock_found){
                    $sql = "SELECT * FROM TBL_QUOTE_STOCK quote_stock JOIN TBL_STOCK stock ON stock.EBQ_CODE = quote_stock.EBQ_CODE WHERE QUOTE_ID = '$entity_id';";
                    $result = $this->db->query($sql);
    
                    // Return Stock information
                    $selected_stock = array();
                    foreach ($result->getResult() as $row): 
                    { 
                       array_push($selected_stock,$row);
                    }
                    endforeach;
                    $data['selected_stock'] = $selected_stock;
   
                   $stockSQL = "SELECT sum(quote_stock.QUANTITY*stock.AVG_COST) as total from TBL_QUOTE_STOCK quote_stock join TBL_STOCK stock on stock.EBQ_CODE = quote_stock.EBQ_CODE where quote_stock.QUOTE_ID = $entity_id GROUP BY QUOTE_ID";
                   $stockResult = $this->db->query($stockSQL);
                   $data['stock_total'] = $stockResult->getResult()[0]->total;
                }
            
           }               
                                        
        echo view('quote/quote_print',$data);
        
    }
    
    
}
    