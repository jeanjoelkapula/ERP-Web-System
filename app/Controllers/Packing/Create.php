<?php namespace App\Controllers\Packing;

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }
        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/packing/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";

        if ($this->request->getPost('form_create_packing') == "true") {
            $order_no =  $this->db->escape(trim($this->request->getPost('order_no')));
            $delivery_date =$this->request->getPost('delivery_date');
            $pack_date = $this->request->getPost('packing_date');
            $ship_via = $this->db->escape(trim($this->request->getPost('ship_via')));
            $source_hub = $this->request->getPost('source_hub_id');
            $destination_hub = $this->request->getPost('destination_hub');
            $packing_stock = $this->request->getPost('stock');
            $site_delivery = $this->request->getPost('site-delivery');

            if($site_delivery === 'on'){
                $site_delivery = 1;
                $destination_hub = 'NULL';
            }
            else{
                $site_delivery = 0;
            }
           
            $sql = "INSERT INTO TBL_PACKING_BILL(";
            if (strpos($order_no, 'INT') === true) {
                $sql .= "INTERNAL_ORDER_NO,";

                $query = "SELECT SOURCE_HUB_ID, DESTINATION_HUB_ID FROM TBL_ORDER_INTERNAL WHERE ORDER_NO = $order_no;";
                $result = $this->db->query($query)->getResultArray()[0]; 

                $source_hub = $result['SOURCE_HUB_ID'];
                $destination_hub = $result['DESTINATION_HUB_ID'];
            }
            else {
                $sql .= "ORDER_NO,";
            }
             
             
             $sql .= "DELIVERY_DATE,PACK_DATE,
                                 CREATED_DATE,SHIP_VIA,STATUS,SOURCE_HUB_ID,DESTINATION_HUB,DELIVER_TO_SITE) VALUES
                ($order_no,
                 '$delivery_date',
                 '$pack_date',
                 now(),
                 $ship_via,
                 'PENDING',
                 $source_hub,
                 $destination_hub,
                 $site_delivery);";
          
            $this->db->query($sql); 
            $packing_bill_id = $this->db->insertID();
            if(isset($packing_stock)){
                foreach (array_keys($packing_stock)  as $ebq) {
                    $quantity = $packing_stock[$ebq]['quantity'];
                    $category = $packing_stock[$ebq]["category"];

                    $packing_stock_insert = "INSERT INTO TBL_PACKING_BILL_STOCK VALUES
                    ($packing_bill_id,'$ebq',$quantity"; 
                    
                    if (strpos($order_no, 'INT') != true) {
                        $packing_stock_insert .= ",$category);";
                    }
                    else {
                        $packing_stock_insert .= ",NULL);";
                    }
                    $this->db->query($packing_stock_insert);
                    echo $packing_stock_insert;
                };
            }

            return redirect()->to('/packing/search/');
            exit();

        }
        
       
        echo view('packing/create',$data);
        
    } 


    public function ajax($rpt_type = ''){

        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;

        if($rpt_type == 'order_get_quote_details'){
            $order_no = $this->db->escape(trim($this->request->getPost('order_no')));

            $sql = "SELECT * FROM TBL_ORDER WHERE ORDER_NO = $order_no";

            $result = $this->db->query($sql)->getResultArray();

            if ($result[0]['QUOTE_ID'] != null) {
                $sql = "SELECT q.* FROM TBL_ORDER o JOIN TBL_QUOTE q ON q.QUOTE_ID = o.QUOTE_ID WHERE o.ORDER_NO = $order_no;";
                $query = $this->db->query($sql);
                $res = $query->getResultArray();
            }
            else {
                $sql = "SELECT V.ORDER_NO FROM TBL_ORDER O
                INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID
                WHERE O.ORDER_NO = $order_no";
                $result = $this->db->query($sql)->getResultArray();

                $sql = "SELECT V.*, O.*, Q.*, S.STORE_ID, S.STORE_NAME FROM TBL_ORDER O        
                INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID          
                INNER JOIN (SELECT Q.*, O.ORDER_NO FROM TBL_ORDER O
                    INNER JOIN TBL_QUOTE Q ON Q.QUOTE_ID = O.QUOTE_ID         
                    WHERE (O.ORDER_NO = '".$result[0]['ORDER_NO']."')) Q ON Q.ORDER_NO = Q.ORDER_NO
                INNER JOIN TBL_CONTRACTOR C ON C.CONTRACTOR_ID = Q.CONTRACTOR_ID 
                INNER JOIN TBL_STORE S ON S.STORE_ID = Q.STORE_ID
                WHERE (O.ORDER_NO = $order_no);";
                $query = $this->db->query($sql);
                $res = $query->getResultArray();
            }
            
            
            echo json_encode($res);
        }

        if($rpt_type == 'get_source_hubs'){
            $order_no = $this->db->escape(trim($this->request->getPost('order_no')));

            $sql = "SELECT * FROM TBL_ORDER WHERE ORDER_NO = $order_no";

            $result = $this->db->query($sql)->getResultArray();

            if ($result[0]['QUOTE_ID'] != null) {
                $sql = "SELECT DISTINCT h.HUB_ID, h.HUB_NAME FROM TBL_ORDER o 
                JOIN TBL_QUOTE q ON o.QUOTE_ID = q.QUOTE_ID 
                JOIN TBL_QUOTE_STOCK qs ON qs.QUOTE_ID = q.QUOTE_ID 
                JOIN TBL_HUB h ON h.HUB_ID = qs.HUB_ID 
                WHERE o.ORDER_NO = $order_no;";
                $query = $this->db->query($sql);
                $res = $query->getResultArray();
                $data['source_hubs'] = $sql;
            }
            else {
                $sql = "SELECT H.HUB_ID, H.HUB_NAME FROM TBL_ORDER O INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID INNER JOIN TBL_HUB H ON H.HUB_ID = V.HUB_ID WHERE O.ORDER_NO = $order_no;";
                $query = $this->db->query($sql);
                $res = $query->getResult('array');

                $data['source_hubs'] = $sql;
            }
            
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }

        if($rpt_type == 'order_get_quote_stock'){
            $order_no = $this->db->escape(trim($this->request->getPost('order_no')));

            $sql = "SELECT * FROM TBL_ORDER WHERE ORDER_NO = $order_no";

            $result = $this->db->query($sql)->getResultArray();

            if ($result[0]['QUOTE_ID'] != null) {
                $hub_id = $this->db->escape(trim($this->request->getPost('hub_id')));
                $sql = " select s.*,qs.STOCK_CATEGORY,qs.QUANTITY,qs.HUB_ID,h.HUB_NAME,m.METRIC_DESCRIPTION
                        from TBL_QUOTE q
                        inner join TBL_QUOTE_STOCK qs on qs.QUOTE_ID = q.QUOTE_ID
                        inner join TBL_STOCK s on s.EBQ_CODE = qs.EBQ_CODE
                        inner join TBL_METRIC m on m.METRIC_ID=s.METRIC_ID
                        inner join TBL_ORDER o on o.QUOTE_ID = q.QUOTE_ID
                        inner join TBL_HUB h on h.HUB_ID = qs.HUB_ID
                        where (o.ORDER_NO = $order_no) and (qs.HUB_ID=$hub_id);";
             
                $query = $this->db->query($sql);
                $res = $query->getResultArray();
            }
            else {
                $sql = "SELECT V.ORDER_NO FROM TBL_ORDER O
                INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID
                WHERE O.ORDER_NO = $order_no";
                $result = $this->db->query($sql)->getResultArray();

                $sql = "SELECT  s.*, QS.*, h.HUB_ID, h.HUB_NAME, m.METRIC_DESCRIPTION FROM TBL_ORDER O        
                INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID          
                INNER JOIN (SELECT Q.*, O.ORDER_NO FROM TBL_ORDER O
                    INNER JOIN TBL_QUOTE Q ON Q.QUOTE_ID = O.QUOTE_ID         
                    WHERE (O.ORDER_NO = '".$result[0]['ORDER_NO']."')) Q ON Q.ORDER_NO = Q.ORDER_NO
                INNER JOIN TBL_CONTRACTOR C ON C.CONTRACTOR_ID = Q.CONTRACTOR_ID 
                INNER JOIN TBL_STORE S ON S.STORE_ID = Q.STORE_ID
                INNER JOIN TBL_QUOTE_STOCK QS on QS.QUOTE_ID = Q.QUOTE_ID
                INNER JOIN TBL_STOCK s on s.EBQ_CODE = QS.EBQ_CODE
                INNER JOIN TBL_METRIC m on m.METRIC_ID=s.METRIC_ID
                INNER JOIN TBL_HUB h on h.HUB_ID = QS.HUB_ID
                WHERE (O.ORDER_NO = $order_no);";

                $query = $this->db->query($sql);
                $res = $query->getResultArray();
            }
            echo json_encode($res);
        }

        if($rpt_type == 'get_internal_order_details'){
            $order_no = $this->db->escape(trim($this->request->getPost('order_no')));
            $sql = "SELECT * FROM TBL_ORDER_INTERNAL WHERE ORDER_NO = $order_no";

            $query = $this->db->query($sql);
            $res = $query->getResultArray()[0];

            echo json_encode($res);
        }

        if ($rpt_type == 'get_internal_order_stock') {
            $order_no = $this->db->escape(trim($this->request->getPost('order_no')));

            $sql = "SELECT S.*, M.METRIC_DESCRIPTION, IOS.QUANTITY FROM TBL_ORDER_INTERNAL OI
                INNER JOIN TBL_INTERNAL_ORDER_STOCK IOS ON IOS.ORDER_NO = OI.ORDER_NO
                INNER JOIN TBL_STOCK S ON S.EBQ_CODE = IOS.EBQ_CODE
                INNER JOIN TBL_METRIC M ON S.METRIC_ID = M.METRIC_ID
                WHERE OI.ORDER_NO = $order_no;";

            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }
    }

    
}
    