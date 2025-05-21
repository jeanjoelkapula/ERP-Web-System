<?php namespace App\Controllers\Order;

use \App\Controllers\BaseController;
use \App\Models\Email;
use \App\Models\GenerateFile;

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
    

    public function index($entity_id)
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        
        $data['entity_id'] = $entity_id;
        if (strpos($entity_id, 'INT') === false) {
            $data['is_internal'] = 0;
            echo view('order/order', $data);
        }
        else {
            $data['is_internal'] = 1;
            $sql = "SELECT * FROM TBL_ORDER_INTERNAL WHERE ORDER_NO = '$entity_id';";
            $query = $this->db->query($sql)->getResultArray();

            if ($query[0]['STATUS'] == 'PENDING') {
                echo view('order/update_internal', $data);
            }
            else {
                echo view('order/order', $data);
            }
        }
        
    }

    public function ajax($rpt_type = ''){

        if(!($this->ionAuth->inGroup('electrical_manager')  && !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        if($rpt_type == 'order_get_quote_stock'){
            $quote_id = $this->request->getPost('quote_id'); // TODO: Need to return quantity currently in stock and set max in form to that
            $sql = " select s.*,qs.STOCK_CATEGORY,qs.QUANTITY
                    from TBL_QUOTE q
                    inner join TBL_QUOTE_STOCK qs on qs.QUOTE_ID = q.QUOTE_ID
                    inner join TBL_STOCK s on s.EBQ_CODE = qs.EBQ_CODE
                    where q.QUOTE_ID = $quote_id";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }

        if($rpt_type == 'approve_order'){
            $order_no = $this->request->getPost('order_no');
            $res = '';
            if (strpos($order_no, 'INT') === false) {
                $sql = "update TBL_ORDER set STATUS = 'APPROVED' where ORDER_NO = '$order_no'";
            }
            else {
                $sql = "update TBL_ORDER_INTERNAL set STATUS = 'APPROVED' where ORDER_NO = '$order_no'";
            }
            
            $query = $this->db->query($sql);

            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }
            echo json_encode($res);
        }

        if($rpt_type == 'decline_order'){
            $order_no = $this->request->getPost('order_no');
            $res = '';
            if (strpos($order_no, 'INT') === false) {
                $sql = "update TBL_ORDER set STATUS = 'DECLINED' where ORDER_NO = '$order_no'";            }
            else {
                $sql = "update TBL_ORDER_INTERNAL set STATUS = 'DECLINED' where ORDER_NO = '$order_no'";
            }
            
            $query = $this->db->query($sql);

            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }

            echo json_encode($res);
        }

    }

    public function update_internal(){
        $data = $this->data;

        $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');


        if($this->request->getPost('form_update_order_internal') == "true"){
            
            $source_hub = $this->request->getPost('s_hub_id');
            $dest_hub = $this->request->getPost('d_hub_id');
            
            $order_no = $this->db->escape($this->request->getPost('order_no'));

            $sql = "DELETE FROM TBL_INTERNAL_ORDER_STOCK WHERE ORDER_NO = $order_no";
            $this->db->query($sql);

            $order_stock = $this->request->getPost('stock');
            foreach (array_keys($order_stock)  as $ebq) {
                $quantity = $order_stock[$ebq]['quantity'];

                $order_stock_insert = "INSERT INTO TBL_INTERNAL_ORDER_STOCK(ORDER_NO, EBQ_CODE, QUANTITY) VALUES
                ($order_no,'$ebq',$quantity);";
                $this->db->query($order_stock_insert);
            };

            return redirect()->to('/order/search/');
            exit();
        }
        

        
        echo view('order/create_internal',$data);
    }

}