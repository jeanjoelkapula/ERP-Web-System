<?php namespace App\Controllers\Requisition;

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
        $data["url"] = "/requisition/update";


        if ($this->request->getMethod() == 'get') {
                
            if (!isset($entity_id) || ($entity_id <= 0)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();
            }
            else {
                $sql = "SELECT R.*, H.HUB_NAME, S.DESCRIPTION FROM TBL_REQUISITION R
                INNER JOIN TBL_HUB H ON H.HUB_ID = R.HUB_ID
                LEFT JOIN TBL_STOCK S ON S.EBQ_CODE = R.EBQ_CODE
                WHERE R.REQUISITION_NO = $entity_id;";
                $result = $this->db->query($sql)->getResult('array')[0];
                $data['req_no'] = $result['REQUISITION_NO'];
                $data['ebq_code'] = $result['EBQ_CODE'];
                $data['item_description'] = $result['DESCRIPTION'];
                $data['completion_date'] = $result['REQUISITION_DATE'];
                $data['hub_id'] = $result['HUB_ID'];
                $data['notes'] = $result['NOTES'];
                $data['declined'] = ($result['APPROVAL_STATUS'] == 'DECLINED');
                $data['approved'] = ($result['APPROVAL_STATUS'] == 'APPROVED');
                $data['complete'] = ($result['IS_COMPLETE'] == 1);
                $sql = "SELECT EBQ_CODE, DESCRIPTION FROM TBL_STOCK WHERE IS_BUILT=1";
                $result = $this->db->query($sql);
                

                $stock = array();
                foreach($result->getResult('array') as $row) {

                    $sql = "SELECT S.*, SC.QUANTITY FROM TBL_STOCK_COMBINATION SC
                        INNER JOIN 
                            (SELECT S.*, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                            INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID) S
                        ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                        WHERE (SC.EBQ_CODE_LG = '".$row['EBQ_CODE']."');";

                    $r = $this->db->query($sql);
                    $sub_items = array();

                    foreach($r->getResult('array') as $r) {
                        array_push($sub_items, $r);
                    }

                    array_push($stock, array('largerItem'=> array('EBQ_CODE'=>$row['EBQ_CODE'], 'DESCRIPTION'=> $row['DESCRIPTION']),'subItems'=>$sub_items));
                    
                }
                $data['stock'] = $stock;

                $sql = "SELECT S.*, SC.QUANTITY FROM TBL_STOCK_COMBINATION SC
                INNER JOIN 
                    (SELECT S.*, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                    INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID) S
                ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                WHERE SC.EBQ_CODE_LG = '".$data['ebq_code']. "'";
                $r = $this->db->query($sql);
                $data['sub_items'] = $r->getResult('array');
            }
        }

        if ($this->request->getMethod() == 'post') {
            $requisition_no = $this->request->getPost('req_no');
            $requisition_item = $this->db->escape(trim($this->request->getPost('requisition_item')));
            $expected_date = $this->db->escape(trim($this->request->getPost('expected_date')));
            $hub = $this->request->getPost('hub');
            if ($this->request->getPost('requisition_notes')) {
                $requisition_notes = $this->db->escape(trim($this->request->getPost('requisition_notes')));
            }
            

            $sql = "UPDATE TBL_REQUISITION SET HUB_ID = $hub, EXPECTED_COMPLETION = $expected_date, EBQ_CODE=$requisition_item";
            if ($this->request->getPost('requisition_notes')) {
                $sql .= ",NOTES = $requisition_notes";
            }

            $sql .= " WHERE REQUISITION_NO = $requisition_no;";

            $this->db->query($sql);

            return redirect()->to('/requisition/search');
            exit();
        }

        echo view('requisition/update',$data);
        
    }   
    
    public function approve($entity_id=0)
    {
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $sql = "SELECT APPROVAL_STATUS FROM TBL_REQUISITION WHERE REQUISITION_NO = $entity_id;";
            $status = $this->db->query($sql)->getResult('array')[0]['APPROVAL_STATUS'];

            if (($status != "APPROVED") || ($status != "APPROVED")) {
                $sql = "UPDATE TBL_REQUISITION SET APPROVAL_STATUS='APPROVED' WHERE REQUISITION_NO = $entity_id";
                $query = $this->db->query($sql);

                if($query !== false){
                    $res = 'ok';
                } else {
                    $res = 'error';
                }
            }
            else {
                $res = 'error';
            }
            
            echo json_encode($res);
        }
    }
    
    public function decline($entity_id=0)
    {
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $sql = "SELECT APPROVAL_STATUS, IS_COMPLETE FROM TBL_REQUISITION WHERE REQUISITION_NO = $entity_id;";
            $status = $this->db->query($sql)->getResult('array')[0]['APPROVAL_STATUS'];
            $complete = $this->db->query($sql)->getResult('array')[0]['IS_COMPLETE'];

            if (($status != "DECLINED") && ($complete != 1)) {
                $sql = "UPDATE TBL_REQUISITION SET APPROVAL_STATUS='DECLINED' WHERE REQUISITION_NO = $entity_id";
                $query = $this->db->query($sql);

                if($query !== false){
                    $res = 'ok';
                } else {
                    $res = 'error';
                }
            }
            else {
                $res = 'error';
            }

            echo json_encode($res);
        }
    }

    public function complete($entity_id=0)
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {

            $sql = "SELECT IS_COMPLETE FROM TBL_REQUISITION WHERE REQUISITION_NO = $entity_id;";
            $status = $this->db->query($sql)->getResult('array')[0]['IS_COMPLETE'];
            if (($status != 1)) {
                $sql = "UPDATE TBL_REQUISITION SET IS_COMPLETE=1 WHERE REQUISITION_NO = $entity_id";
                $this->db->query($sql);
            }

            return redirect()->to('/requisition/search');
            exit();
        }
    }
}
    