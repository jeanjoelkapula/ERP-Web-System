<?php namespace App\Controllers\Quote;

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/quote/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";

        $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');

        if ($this->request->getPost('form_create_quote') == "true") {
            $quote_stock = $this->request->getPost('stock');
            $hub_id = $this->request->getPost('hub_id');    
            $store_id = trim($this->request->getPost('store_id'));
            $contractor_id = trim(replaceDirectionWithValue($this->request->getPost('contractor_id')));
            $quote_type = trim(replaceDirectionWithValue($this->request->getPost('quote_type')));
            $action_id = trim($this->request->getPost('action_type'));
            $ship_via = $this->db->escape(trim($this->request->getPost('ship_via')));
            $delivery_date = $this->db->escape(trim($this->request->getPost('delivery_date')));
            $totalArr = $this->request->getPost('total');
            $user_id = $data['_user_id'];
            $total = 0;
            $pki_percentage = $this->request->getPost('pki_fee'); 
            $notes = $this->db->escape(trim($this->request->getPost('note')));
            if($totalArr != null){
                $total = array_sum($totalArr);
            }
             $sql = "INSERT INTO TBL_QUOTE(STORE_ID,QUOTE_TYPE_ID,CONTRACTOR_ID,
                                 TOTAL,ACTION_ID,SHIP_VIA,DELIVERY_DATE,USER_ID,CREATED_DATE,NOTE,PKI_PERCENTAGE) VALUES
                ($store_id,
                 $quote_type,
                 $contractor_id,
                 $total,
                 $action_id,
                 $ship_via,
                 $delivery_date,
                 $user_id,
                 now(),
                 $notes,
                 $pki_percentage);";
                 
            $this->db->query($sql); 
            $quote_id = $this->db->insertID();
            if(isset($quote_stock)){
                foreach (array_keys($quote_stock)  as $ebq) {
                    $quantity = $quote_stock[$ebq]['quantity'];
                    $category = $quote_stock[$ebq]["category"];
                    $markup = $quote_stock[$ebq]["markup"];
                    $avg_cost = $quote_stock[$ebq]["avg_cost"];

                    $quote_stock_insert = "INSERT INTO TBL_QUOTE_STOCK VALUES
                    ($quote_id,'$ebq',$quantity,$category,$hub_id,$avg_cost,$markup);";
                    $this->db->query($quote_stock_insert);
                };
            }

            return redirect()->to('/quote/search/');
            return true;
            exit();

        }
        
       
        echo view('quote/create',$data);
        
    }    

    public function ajax($rpt_type = ''){

        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;

        if($rpt_type == 'get_pref_contractor'){
            $store_id = $this->request->getPost('store_id');
            $sql = "SELECT c.CONTRACTOR_ID,s.HUB_ID FROM TBL_PREFERRED_CONTRACTOR c
            INNER JOIN TBL_STORE s ON s.STORE_ID = c.STORE_ID WHERE c.STORE_ID = $store_id;";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }

        if($rpt_type == 'precheck_hubstock'){
            $hub_id = $this->request->getPost('hub_id');
            $sql = "SELECT HS.*,S.DESCRIPTION,S.AVG_COST,S.MARKUP,M.METRIC_DESCRIPTION FROM TBL_HUB_STOCK HS 
            INNER JOIN TBL_STOCK S ON S.EBQ_CODE = HS.EBQ_CODE
            INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID 
            WHERE (HUB_ID = $hub_id) AND (S.IS_ACTIVE = 1);";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }

        if($rpt_type == 'precheck_quantity'){
            $quote_stock = $this->request->getPost('stock');
            $hub_id = $this->request->getPost('hub_id');
            // Check quotestock exists and is available
            $sql = "SELECT * FROM TBL_HUB_STOCK WHERE HUB_ID = $hub_id;";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            $unavailable_stock = array();
            foreach (array_keys($quote_stock) as $ebq) {
                $key = array_search($ebq, array_column($res, 'EBQ_CODE'));
                if($quote_stock[$ebq]['quantity'] > $res[$key]['QUANTITY']){
                    array_push($unavailable_stock,$ebq);
                }
            }
            echo json_encode($unavailable_stock);
            exit();
            return false;
            
        }
    }

}
    