<?php namespace App\Controllers\Journal;

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
    

    public function index($entity_id=0)
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/journal/create";
        $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');
 
        
        if ($this->request->getPost('form_create_journal') == "true") {

            $hub = $this->request->getPost('hub_id');
            $journal_notes = $this->db->escape(trim($this->request->getPost('journal_notes')));
            $stock = $this->request->getPost('stock');
            $user_id = $data['_user_id'];

            if(isset($stock)){
                foreach (array_keys($stock)  as $ebq) {
                    $quantity_new = $stock[$ebq]['quantity'];
                    $quantity_old = $stock[$ebq]['current_quantity'];
                    $cost = $stock[$ebq]['total'];
                    $sql = "INSERT INTO TBL_STOCK_JOURNAL(EBQ_CODE,QUANTITY,ENTRY_DATE,USER_ID,HUB_ID,COST,NOTES,QUANTITY_CHANGE) 
                            VALUES ('$ebq', $quantity_new, now(), $user_id, $hub,$cost,$journal_notes,($quantity_new-$quantity_old));";
                    $this->db->query($sql);
                }
            }

            return redirect()->to('/journal/search');
            exit();
            
        }

        if ($this->request->getPost('form_approve_journal') == "true") {

            if(!isset($entity_id) && $entity_id<=0){
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();
            }
            else{
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK_JOURNAL WHERE JOURNAL_ID = '$entity_id';";
                $result = $this->db->query($sqlQuery);
                $journal_found = false;
    
                foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT'] > 0) {
                        $journal_found = true;
                    }
                }
                endforeach;

                if($journal_found){
                    if($data['_role_id'] == 1){
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
                    }
                    else{
                        // No permissions
                    }
                }
                else{
                    // No Journal
                }
            }
            return redirect()->to('/journal/search');
            exit();
            
        }

        if ($this->request->getPost('form_decline_journal') == "true") {
            $decline_reason = $this->db->escape(trim($this->request->getPost('reason')));

            if(!isset($entity_id) && $entity_id<=0){
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();
            }
            else{
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK_JOURNAL WHERE JOURNAL_ID = '$entity_id';";
                $result = $this->db->query($sqlQuery);
                $journal_found = false;
    
                foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT'] > 0) {
                        $journal_found = true;
                    }
                }
                endforeach;

                if($journal_found){
                    if($data['_role_id'] == 1){
                        $user = $data['_user_id'];
                        $update_journal = "UPDATE TBL_STOCK_JOURNAL SET STATUS = 'DECLINED', APPROVAL_DATE = now(), APPROVED_BY = $user, NOTES=$decline_reason  WHERE JOURNAL_ID = $entity_id;";
                        $this->db->query($update_journal);
                    }
                    else{
                        // No permissions
                    }
                }
                else{
                    // No Journal
                }
            }
            return redirect()->to('/journal/search');
            exit();
            
        }

       
        echo view('journal/create',$data);
        
    }    
    
}
    