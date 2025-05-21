<?php namespace App\Controllers\Voc;

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
        
        $data = $this->data;
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }
                
        $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');

        if ($this->request->getPost('frm_create_voc') == "true") {
            $order_no = $this->request->getPost('order_no');
            $voc_stock = $this->request->getPost('stock');
            $hub_id = $this->request->getPost('hub_id');
            $sql = "insert into TBL_VOC (ORDER_NO,VOC_STATUS,CREATED_DTM, HUB_ID) values ('$order_no','PENDING',now(), $hub_id)";
            $query = $this->db->query($sql);
            
            $voc_id = $this->db->insertID();
            if(isset($voc_stock)){
                foreach (array_keys($voc_stock)  as $ebq) {
                    $quantity = $voc_stock[$ebq]['quantity'];
                    $category = $voc_stock[$ebq]["category"];
                    $markup = $voc_stock[$ebq]["markup"];
                    $avg_cost = $voc_stock[$ebq]["avg_cost"];
                    $hub = $voc_stock[$ebq]['hub'];

                    $voc_stock_insert = "INSERT INTO TBL_VOC_STOCK VALUES
                    ($voc_id,'$ebq',$quantity,$category,$avg_cost,$markup);";
                    $this->db->query($voc_stock_insert);
                };
            }
            
            return redirect()->to('/voc/search/');
            exit;
            
        }
        
       
        echo view('voc/create',$data);
        
    }

    public function ajax($rpt_type = ''){

        $data = $this->data;

        if($rpt_type == 'get_stock_list'){
            $sql = "select * from TBL_STOCK"; // TODO: ADD CHECK TO ENSURE ONLY APPROVED STOCK IS SHOWN
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);

        }

        if($rpt_type == 'voc_get_quote_details'){
            $order_no = $this->request->getPost('order_no');
            $sql = "select o.*,c.CONTACT_NUMBER as CONTRACTOR_CONTACT, c.EMAIL AS CONTRACTOR_EMAIL,c.CONTRACTOR_NAME,c.CONTRACTOR_ID,
                    a.*,r.*,qt.*,s.STORE_NAME,s.FF_CODE,s.CONTACT_NUMBER as STORE_CONTACT,q.*
                    from TBL_ORDER o
                    inner join TBL_QUOTE q on q.QUOTE_ID = o.QUOTE_ID
                    inner join TBL_QUOTE_TYPE qt on qt.TYPE_ID = q.QUOTE_TYPE_ID
                    inner join TBL_CONTRACTOR c on c.CONTRACTOR_ID = q.CONTRACTOR_ID
                    inner join TBL_ACTION_TYPE at on at.ACTION_ID = q.ACTION_ID
                    inner join TBL_STORE s on s.STORE_ID = q.STORE_ID
                    inner join TBL_AREA a on a.AREA_NO = s.AREA_ID
                    inner join TBL_REGION r on r.REGION_NO = a.REGION_NO
                    where o.ORDER_NO = '$order_no'  ";
            $query = $this->db->query($sql);

            
            $res = $query->getResultArray();

            echo json_encode($res);
        }
        
    }
    

    
}
    