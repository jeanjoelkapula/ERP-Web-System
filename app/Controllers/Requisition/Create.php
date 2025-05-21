<?php namespace App\Controllers\Requisition;

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
        $data["url"] = "/requisition/create";

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
        
        if ($this->request->getPost('form_requisition') == "true") {

            $requisition_item = $this->db->escape(trim($this->request->getPost('requisition_item')));

            $expected_date = $this->db->escape(trim($this->request->getPost('expected_date')));
            $hub = $this->request->getPost('hub');
            if ($this->request->getPost('requisition_notes')) {
                $requisition_notes = $this->db->escape(trim($this->request->getPost('requisition_notes')));
            }
            

            $sql = "INSERT INTO TBL_REQUISITION(REQUISITION_DATE,";
            if (!empty($this->request->getPost('requisition_notes'))) {
                $sql .= "NOTES,";
            }
            $sql .= "HUB_ID, EXPECTED_COMPLETION, EBQ_CODE) VALUES (NOW(),";

            if (!empty($this->request->getPost('requisition_notes'))) {
                $sql .= $requisition_notes.",";
            }

            $sql .= "$hub, $expected_date, $requisition_item);";
            $this->db->query($sql);

            return redirect()->to('/requisition/search');
            exit();
            
        } 
       
        echo view('requisition/create',$data);
        
    }    
    
}
    