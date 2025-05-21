<?php namespace App\Controllers\Journal;

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/journal/update/$entity_id";
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
            // Check if the journal exists
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK_JOURNAL  WHERE JOURNAL_ID = '$entity_id';";
            $result = $this->db->query($sqlQuery);
            $journal_found = false;

            foreach ($result->getResult('array') as $row): 
            { 
                if ($row['COUNT'] > 0) {
                    $journal_found = true;
                }
            }
            endforeach;



            if ($journal_found) {
                $sql = "SELECT journal.*,stock.DESCRIPTION,hs.QUANTITY AS CURRENT_QUANTITY
                        FROM TBL_STOCK_JOURNAL journal
                        INNER JOIN TBL_STOCK stock ON stock.EBQ_CODE = journal.EBQ_CODE
                        INNER JOIN TBL_HUB_STOCK hs ON hs.HUB_ID = journal.HUB_ID AND hs.EBQ_CODE = journal.EBQ_CODE
                        WHERE JOURNAL_ID = $entity_id;";
                $result = $this->db->query($sql);

                // Return Journal information
                foreach ($result->getResult('array') as $row): 
                { 
                    $data['journal_id'] = $row['JOURNAL_ID']; 
                    $data['ebq_code'] = $row['EBQ_CODE']; 
                    $data['quantity'] = $row['QUANTITY']; 
                    $data['hub_id'] = $row['HUB_ID']; 
                    $data['notes'] = $row['NOTES']; 
                    $data['cost'] = $row['COST'];
                    $data['description'] = $row['DESCRIPTION'];
                    $data['quantity_change'] = $row['QUANTITY_CHANGE'];
                    $data['current_quantity'] = $row['CURRENT_QUANTITY'];
                }
                endforeach;
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        if ($this->request->getPost('form_approve_journal') == "true") {
            if($this->ionAuth->inGroup('electrical_manager')  || $this->ionAuth->inGroup('admin')){
                $journal_fetch_sql = "SELECT * FROM TBL_STOCK_JOURNAL WHERE JOURNAL_ID=$entity_id;";
                $result = $this->db->query($journal_fetch_sql);
                $user = $data['_user_id'];

                foreach ($result->getResult('array') as $row): 
                { 
                    $quantity = $row['QUANTITY'];
                    $ebq = $row['EBQ_CODE'];
                    $hub = $row['HUB_ID'];
                    $update_hub_stock = "UPDATE TBL_HUB_STOCK SET QUANTITY = $quantity WHERE (HUB_ID = $hub) AND (EBQ_CODE = '$ebq');";
                    $this->db->query($update_hub_stock);
                }
                endforeach;
                
                $update_journal = "UPDATE TBL_STOCK_JOURNAL SET STATUS = 'APPROVED', APPROVAL_DATE = now(), APPROVED_BY = $user  WHERE JOURNAL_ID = $entity_id;";
                $this->db->query($update_journal);
                return redirect()->to('/journal/search');
                exit();
            }
            else{
                return redirect()->to('/noAccess');
                exit();
            }
            
        }

        if ($this->request->getPost('form_decline_journal') == "true") {
            $decline_reason = $this->db->escape(trim($this->request->getPost('reason')));
            if($this->ionAuth->inGroup('electrical_manager')  || $this->ionAuth->inGroup('admin')){
                $user = $data['_user_id'];

                $sql = "SELECT * FROM TBL_STOCK_JOURNAL WHERE JOURNAL_ID = $entity_id;";
                $result = $this->db->query($sql)->getResultArray();

                $status = $result[0]['STATUS'];
                $total_quantity = $result[0]['QUANTITY'];
                $quantity_change = $result[0]['QUANTITY_CHANGE'];
                $ebq_code = $result[0]['EBQ_CODE'];
                $hub_id = $result[0]['HUB_ID'];

                if ($status == 'APPROVED') {
                    $update_hub_stock = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY - ($quantity_change)) WHERE (HUB_ID = $hub_id) AND (EBQ_CODE = '$ebq_code');";
                    $this->db->query($update_hub_stock);
                }

                $update_journal = "UPDATE TBL_STOCK_JOURNAL SET STATUS = 'DECLINED', APPROVAL_DATE = now(), APPROVED_BY = $user, NOTES=$decline_reason  WHERE JOURNAL_ID = $entity_id;";
                $this->db->query($update_journal);

               

                return redirect()->to('/journal/search');
                exit();      
            }
            else{
                return redirect()->to('/noAccess');
                exit();
            }
        
        }
      
        // Edit Quote
        if ($this->request->getPost('form_update_journal') == "true") {
            $hub = $this->request->getPost('hub_id');
            $journal_notes = $this->db->escape(trim($this->request->getPost('journal_notes')));
            $stock = $this->request->getPost('stock');
            $user_id = $data['_user_id'];

            if(isset($stock)){

                foreach (array_keys($stock)  as $ebq) {
                    $quantity_new = $stock[$ebq]['quantity'];
                    $quantity_old = $stock[$ebq]['current_quantity'];
                    $cost = $stock[$ebq]['total'];

                    $sql = "DELETE FROM TBL_STOCK_JOURNAL WHERE HUB_ID=$hub AND EBQ_CODE='$ebq'";
                    $this->db->query($sql);

                    // $sql = "UPDATE TBL_STOCK_JOURNAL SET EBQ_CODE='$ebq',QUANTITY=$quantity_new,ENTRY_DATE=now(),USER_ID=$user_id,HUB_ID=$hub,COST=$cost,NOTES=$journal_notes,QUANTITY_CHANGE=($quantity_new-$quantity_old) WHERE JOURNAL_ID = $entity_id;"; 
                    // $this->db->query($sql);
                   
                    $sql = "INSERT INTO TBL_STOCK_JOURNAL(EBQ_CODE,QUANTITY,ENTRY_DATE,USER_ID,HUB_ID,COST,NOTES,QUANTITY_CHANGE) 
                    VALUES ('$ebq', $quantity_new, now(), $user_id, $hub,$cost,$journal_notes,($quantity_new-$quantity_old));";
                    $this->db->query($sql);
                }
            }

            return redirect()->to('/journal/search');
            exit();
            
        }
                
        echo view('journal/update',$data);

}
    
}
    