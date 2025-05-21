<?php namespace App\Controllers\Quote;

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
    

    public function index($entity_id=0)
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/quote/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";

        $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');

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
                $sql = "SELECT quote.*, store.STORE_NAME,quote_type.TYPE_NAME,contractor.CONTRACTOR_NAME  
                        FROM TBL_QUOTE quote 
                        JOIN TBL_QUOTE_TYPE quote_type 
                            ON quote_type.TYPE_ID = quote.QUOTE_TYPE_ID 
                        JOIN TBL_STORE store 
                            ON store.STORE_ID = quote.STORE_ID
                        JOIN TBL_CONTRACTOR contractor
                            ON contractor.CONTRACTOR_ID = quote.CONTRACTOR_ID
                        WHERE QUOTE_ID = $entity_id;";
                $result = $this->db->query($sql);

                // Return Quote information
                foreach ($result->getResult('array') as $row): 
                { 
                    $data['quote_id'] = $row['QUOTE_ID']; 
                    $data['quote_type_id'] = $row['QUOTE_TYPE_ID']; 
                    $data['store_id'] = $row['STORE_ID']; 
                    $data['contractor_id'] = $row['CONTRACTOR_ID']; 
                    $data['action_id'] = $row['ACTION_ID']; 
                    $data['ship_via'] = $row['SHIP_VIA'];
                    $data['delivery_date'] = $row['DELIVERY_DATE'];
                    $data['note'] = $row['NOTE'];
                    $data['status'] = $row['STATUS'];
                    $data['pki_percentage'] = $row['PKI_PERCENTAGE'];
                }
                endforeach;
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            if($stock_found){
                $sql = "SELECT qs.*,h.HUB_NAME FROM TBL_QUOTE_STOCK qs JOIN TBL_HUB h ON qs.HUB_ID = h.HUB_ID WHERE QUOTE_ID = '$entity_id';";
                $result = $this->db->query($sql);

                // Return Stock information
                $selected_stock = array();
                foreach ($result->getResult() as $row): 
                { 
                   array_push($selected_stock,$row);
                }
                endforeach;
                $data['selected_stock'] = $selected_stock;

                $sql = "SELECT DISTINCT HUB_ID FROM TBL_QUOTE_STOCK qs WHERE QUOTE_ID = '$entity_id';";
                $result = $this->db->query($sql);
                foreach ($result->getResult('array') as $row): 
                    { 
                        $data['hub_id'] = $row['HUB_ID'];
                    }
                endforeach;

            }
        }
        /*TODO: Assign relevant roles for quote approval*/
        // Approve Quote
        if ($this->request->getPost('form_approve_quote') == "true") {
            if($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('admin')){
                $sql = "UPDATE TBL_QUOTE SET status = 'APPROVED', approved_date=now() WHERE quote_id = $entity_id;";
                $query = $this->db->query($sql);

                if($query !== false){
                    $res = 'ok';
                } else {
                    $res = 'error';
                }

                echo json_encode($res);
                exit();
            }
            else{
                return redirect()->to('/noAccess');
                exit();
            }
           

        }
        //Decline Quote
        if ($this->request->getPost('form_decline_quote') == "true") {
            if($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('admin')){
                $decline_reason =  $this->db->escape(trim($this->request->getPost('reason')));
                $sql = "UPDATE TBL_QUOTE SET status = 'DECLINED', approved_date = now(),decline_reason=$decline_reason WHERE quote_id = $entity_id;";
                $query = $this->db->query($sql);

                if($query !== false){
                    $res = 'ok';
                } else {
                    $res = 'error';
                }

                echo json_encode($res);
                exit();   
            }
            else{
                return redirect()->to('/noAccess');
                exit();
            }
        }
      
        // Edit Quote
        if ($this->request->getPost('form_update_quote') == "true") {
            if(($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) || ($this->ionAuth->inGroup('electrical_manager')) || ($this->ionAuth->inGroup('electrical_administrator'))){
                $quote_id = $entity_id;
                $store_id = trim($this->request->getPost('store_id'));
                $contractor_id = trim($this->request->getPost('contractor_id'));
                $quote_type = trim($this->request->getPost('quote_type'));
                $action_id = trim($this->request->getPost('action_type'));
                $ship_via = $this->db->escape(trim($this->request->getPost('ship_via')));
                $stock_items = $this->request->getPost('stock');
                $delivery_date = $this->db->escape(trim($this->request->getPost('delivery_date')));
                $totalArr = $this->request->getPost('total');
                $pki_percentage = $this->request->getPost('pki_fee');
                $total = 0; 
                $note= $this->db->escape(trim($this->request->getPost('note')));
                $user_id = $data['_user_id'];
    
                if($totalArr != null){
                    $total = array_sum($totalArr);
                }
    
                $sql = "UPDATE TBL_QUOTE SET store_id = $store_id, contractor_id = $contractor_id, quote_type_id=$quote_type, action_id = $action_id,ship_via =$ship_via,delivery_date=$delivery_date,user_id=$user_id, total=$total,note=$note, pki_percentage=$pki_percentage WHERE quote_id = $quote_id;";
    
                $this->db->query($sql);
                // Remove all existing stock for the quote
                $removeExistingQuoteStock = "DELETE FROM TBL_QUOTE_STOCK WHERE quote_id = $quote_id;";
                $this->db->query($removeExistingQuoteStock);
                // Insert all stock for the quote
                if(isset($stock_items)){
    
                    foreach (array_keys($stock_items) as $ebq) {
                        $quantity = $stock_items[$ebq]['quantity'];
                        $category = $stock_items[$ebq]['category'];
                        $hub = $stock_items[$ebq]['hub'];
                        $markup = $stock_items[$ebq]['markup'];
                        $avg_cost = $stock_items[$ebq]['avg_cost'];
    
                        $stockSQL = "INSERT INTO TBL_QUOTE_STOCK VALUES($quote_id,'$ebq',$quantity,$category,$hub,$avg_cost,$markup);";
                        $this->db->query($stockSQL);  
                    }
                }
                return redirect()->to('/quote/search');
                exit();
            }
            else{
                return redirect()->to('/noAccess');
                exit();
            }
   
        }
                
        echo view('quote/update',$data);

}
    
}
    