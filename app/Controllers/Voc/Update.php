<?php namespace App\Controllers\Voc;

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
        
        $data = $this->data;
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }
                
        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        } 
        else {
            $sql = "SELECT EBQ_CODE,DESCRIPTION,AVG_COST,MARKUP,METRIC_DESCRIPTION FROM TBL_STOCK stock JOIN TBL_METRIC metric ON metric.METRIC_ID = stock.METRIC_ID WHERE stock.IS_ACTIVE = 1;"; 
            $result = $this->db->query($sql);
            $data["stock"] = $result->getResult('array');

            $sql = "SELECT COUNT(*) AS COUNT FROM TBL_VOC WHERE VOC_ID = $entity_id;";
            $result = $this->db->query($sql)->getResult('array');
            if ($result[0]['COUNT'] > 0) {
                $found = true;
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();
            }

            if ($found) {
                $sql = "SELECT * FROM TBL_VOC WHERE VOC_ID = $entity_id;";
                $result = $this->db->query($sql)->getResultArray()[0];
                $data['voc_id'] = $result['VOC_ID'];
                $data['order_no'] = $result['ORDER_NO'];
                $data['voc_status'] = $result['VOC_STATUS'];
                $data['create_dtm'] = $result['CREATED_DTM'];
                $data['hub_id'] = $result['HUB_ID'];
                $data['entity_id'] = $entity_id;

                $sql = " SELECT S.EBQ_CODE, S.DESCRIPTION, VS.STOCK_EBQ, VS.AVG_COST, VS.MARKUP, M.METRIC_DESCRIPTION, QSC.NAME, VS.STOCK_CATEGORY FROM TBL_VOC_STOCK VS
                INNER JOIN TBL_STOCK S ON S.EBQ_CODE = VS.STOCK_EBQ
                INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID
                INNER JOIN TBL_QUOTE_STOCK_CATEGORY QSC ON QSC.ID = VS.STOCK_CATEGORY
                WHERE VS.VOC_ID = $entity_id";

                $voc_stock = $this->db->query($sql)->getResultArray();
                $data['voc_stock'] = $voc_stock;
            }

        }

        if ($this->request->getPost('frm_update_voc') == "true") {
            $voc_id = $this->request->getPost('voc_id');
            $voc_stock = $this->request->getPost('stock');
            $hub_id = $this->request->getPost('hub_id');

            $sql = "UPDATE TBL_VOC SET HUB_ID = $hub_id WHERE VOC_ID = $voc_id;";
            $this->db->query($sql);

            $sql = "DELETE FROM TBL_VOC_STOCK WHERE VOC_ID = $voc_id";
            $this->db->query($sql);

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
        
       
        echo view('voc/update',$data);
        
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
    