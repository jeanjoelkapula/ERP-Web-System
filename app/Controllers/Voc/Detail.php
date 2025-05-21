<?php namespace App\Controllers\Voc;

use \App\Controllers\BaseController;
use \App\Models\Email;
use \App\Models\GenerateFile;

class Detail extends BaseController {

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        
        $data['entity_id'] = $entity_id;
        
        echo view('voc/view_detail', $data);
            
    }

    public function ajax($rpt_type = ''){

        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
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

        if($rpt_type == 'approve_voc'){
            $voc_id = $this->request->getPost('voc_id');
            $sql = "update TBL_VOC set VOC_STATUS = 'APPROVED' where VOC_ID = '$voc_id'";
            $query = $this->db->query($sql);
            $res = '';
            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }

            echo json_encode($res);
        }

        if($rpt_type == 'decline_voc'){
            $voc_id = $this->request->getPost('voc_id');
            $sql = "update TBL_VOC set VOC_STATUS = 'DECLINED' where VOC_ID = '$voc_id'";
            $query = $this->db->query($sql);

            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }

            echo json_encode($res);
        }

    }



}