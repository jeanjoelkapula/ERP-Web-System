<?php namespace App\Controllers\Order;

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
        if(!($this->ionAuth->inGroup('electrical_manager')  && !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;
        $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');
        if($this->request->getPost('form_create_order') == "true"){
            $quote_id = $this->request->getPost('quote_id');
            $voc_id = $this->request->getPost('voc_id');
            // TODO: set to 0 temporarily
            $order_internal = 0;
            $order_notes = $this->db->escape($this->request->getPost('order_notes'));
            $order_no = $this->db->escape($this->request->getPost('order_no'));
            $order_stock = $this->request->getPost('stock');
            $selected_order_type = $this->request->getPost('selected_order_type');

            if ($selected_order_type == "quote") {
                $sql = "insert into TBL_ORDER (ORDER_NO,QUOTE_ID,ORDER_INTERNAL,ORDER_DATE_CREATED,ORDER_NOTES,STATUS,USER_ID)
                    values ($order_no,$quote_id,$order_internal,now(),$order_notes,'PENDING',".$data['_user_id'].") ";
                $query = $this->db->query($sql);
                
                
            }
            else {
                $sql = "insert into TBL_ORDER (ORDER_NO,VOC_ID,ORDER_INTERNAL,ORDER_DATE_CREATED,ORDER_NOTES,STATUS,USER_ID)
                    values ($order_no,$voc_id,$order_internal,now(),$order_notes,'PENDING',".$data['_user_id'].") ";
                $query = $this->db->query($sql);
            }
            
            foreach (array_keys($order_stock)  as $ebq) {
                $quantity = $order_stock[$ebq]['quantity'];

                //TODO: Uncomment this if we need to add stock category to order
                $category = $order_stock[$ebq]["category"];
                $order_stock_insert = "INSERT INTO TBL_ORDER_STOCK VALUES
                ($order_no,'$ebq',$quantity,$category);";
                $this->db->query($order_stock_insert);
            };

            return redirect()->to('/order/search/');
            exit();
        }
       
        
       
        echo view('order/create',$data);
        
    }

    public function create_internal(){

        if(!($this->ionAuth->inGroup('electrical_manager')  && !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;

        $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');


        if($this->request->getPost('form_create_order_internal') == "true"){
            
            $source_hub = $this->request->getPost('s_hub_id');
            $dest_hub = $this->request->getPost('d_hub_id');
            
            $order_internal = 1;
            $order_notes = $this->db->escape($this->request->getPost('order_notes'));

            $sql = "SELECT GetSequenceVal('InternalOrderSequence', 1) AS VALUE;";
            $query = $this->db->query($sql)->getResultArray();

            $order_no = "INT". $query[0]['VALUE'];
            $order_stock = $this->request->getPost('stock');

            $sql = "insert into TBL_ORDER_INTERNAL (ORDER_NO,SOURCE_HUB_ID,DESTINATION_HUB_ID, DATE_CREATED, USER_ID) values ('$order_no',$source_hub,$dest_hub, now(),".$data['_user_id']."); ";
            $query = $this->db->query($sql);


            foreach (array_keys($order_stock)  as $ebq) {
                $quantity = $order_stock[$ebq]['quantity'];

                $order_stock_insert = "INSERT INTO TBL_INTERNAL_ORDER_STOCK(ORDER_NO, EBQ_CODE, QUANTITY) VALUES
                ('$order_no','$ebq',$quantity);";
                $this->db->query($order_stock_insert);
            };

            return redirect()->to('/order/search/');
            exit();
        }
        

        
        echo view('order/create_internal',$data);
    }

    public function check_order_no($order_no = ''){
        $data = $this->data;
        $check = 0;
        $order_no = $this->db->escape($this->request->getPost('order_no'));
        $sql = "select count(*) as cnt from TBL_ORDER where ORDER_NO = $order_no";
        $query = $this->db->query($sql);
        foreach($query->getResult() as $row){
            $check = $row->cnt;
        }
        echo $check;
    }

    public function ajax($rpt_type = ''){

        if(!($this->ionAuth->inGroup('electrical_manager')  && !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;

        if($rpt_type == 'order_get_quote_details'){
            $quote_id = $this->request->getPost('quote_id');
            $sql = "select q.*,c.CONTACT_NUMBER as CONTRACTOR_CONTACT, c.EMAIL AS CONTRACTOR_EMAIL,c.CONTRACTOR_NAME,c.CONTRACTOR_ID,
                    a.*,r.*,qt.*,s.STORE_ID,s.STORE_TYPE_ID,s.AREA_ID,s.CONTACT_NUMBER,s.STORE_NAME,s.FF_CODE,s.OPENING_DATE,s.MAINTENANCE_MONTH,
                    s.IN_CENTER,s.TRADING_SIZE,s.BRANCH_SIZE,s.HUB_ID,s.STORE_MANAGER,s.IS_OPEN
                    from TBL_QUOTE q
                    inner join TBL_QUOTE_TYPE qt on qt.TYPE_ID = q.QUOTE_TYPE_ID
                    inner join TBL_CONTRACTOR c on c.CONTRACTOR_ID = q.CONTRACTOR_ID
                    inner join TBL_ACTION_TYPE at on at.ACTION_ID = q.ACTION_ID
                    inner join TBL_STORE s on s.STORE_ID = q.STORE_ID
                    inner join TBL_AREA a on a.AREA_NO = s.AREA_ID
                    inner join TBL_REGION r on r.REGION_NO = a.REGION_NO
                    where q.QUOTE_ID = $quote_id  ";
            $query = $this->db->query($sql);
            
            $res = $query->getResultArray();
            
            echo json_encode($res);
        }

        if($rpt_type == 'order_get_voc_details'){
            $voc_id = $this->request->getPost('voc_id');
            $sql = "SELECT Q.*,c.CONTACT_NUMBER as CONTRACTOR_CONTACT, c.EMAIL AS CONTRACTOR_EMAIL,c.CONTRACTOR_NAME,c.CONTRACTOR_ID,
            a.*,r.*,qt.*,s.STORE_ID,s.STORE_TYPE_ID,s.AREA_ID,s.CONTACT_NUMBER,s.STORE_NAME,s.FF_CODE,s.OPENING_DATE,s.MAINTENANCE_MONTH,
            s.IN_CENTER,s.TRADING_SIZE,s.BRANCH_SIZE,s.HUB_ID,s.STORE_MANAGER,s.IS_OPEN FROM TBL_QUOTE Q 
            INNER JOIN
            (SELECT V.ORDER_NO, O.QUOTE_ID FROM TBL_VOC V
                INNER JOIN TBL_ORDER O ON O.ORDER_NO = V.ORDER_NO
                WHERE V.VOC_ID = $voc_id) R ON Q.QUOTE_ID = R.QUOTE_ID
            inner join TBL_QUOTE_TYPE qt on qt.TYPE_ID = Q.QUOTE_TYPE_ID
            inner join TBL_CONTRACTOR c on c.CONTRACTOR_ID = Q.CONTRACTOR_ID
            inner join TBL_ACTION_TYPE at on at.ACTION_ID = Q.ACTION_ID
            inner join TBL_STORE s on s.STORE_ID = Q.STORE_ID
            inner join TBL_AREA a on a.AREA_NO = s.AREA_ID
            inner join TBL_REGION r on r.REGION_NO = a.REGION_NO; ";
            $query = $this->db->query($sql);
            
            $res = $query->getResultArray();
            
            echo json_encode($res);
        }

        if($rpt_type == 'get_contractors'){
            $sql = " select * from TBL_CONTRACTOR";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }

        if($rpt_type == 'get_contractor_detail'){
            $contractor_id = $this->request->getPost('contractor_id');
            $sql = "select * from TBL_CONTRACTOR where CONTRACTOR_ID = $contractor_id";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }

        if($rpt_type == 'get_stock_list'){
            $sql = "select * from TBL_STOCK where IS_ACTIVE = 1"; 
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);

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
        

        if($rpt_type == 'order_get_internal_stock'){
            $order_no = $this->request->getPost('order_no'); 
            $sql = " select s.*,os.QUANTITY,os.STOCK_CATEGORY
                    from TBL_STOCK s
                    inner join TBL_ORDER_STOCK os on os.EBQ_CODE = s.EBQ_CODE
                    where os.ORDER_NO = '$order_no'";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }

        if($rpt_type == 'order_get_voc_stock'){
            $voc_id = $this->request->getPost('voc_id'); // TODO: Need to return quantity currently in stock and set max in form to that
            $sql = " select s.*,vc.STOCK_CATEGORY,vc.VOC_STOCK_QUANTITY as QUANTITY
                    from TBL_VOC v
                    inner join TBL_VOC_STOCK vc on vc.VOC_ID = v.VOC_ID
                    inner join TBL_STOCK s on s.EBQ_CODE = vc.STOCK_EBQ 
                    inner join TBL_QUOTE_STOCK_CATEGORY qsc on qsc.ID = vc.STOCK_CATEGORY
                    where v.VOC_ID = $voc_id";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }

        
    }

   
    

    
}
    