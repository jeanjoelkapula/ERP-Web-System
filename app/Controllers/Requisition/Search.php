<?php namespace App\Controllers\Requisition;

use \App\Controllers\BaseController;

class Search extends BaseController {

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
        $sql = "SELECT R.*, H.HUB_NAME FROM TBL_REQUISITION R
        INNER JOIN TBL_HUB H ON H.HUB_ID = R.HUB_ID
        ";
        $result = $this->db->query($sql);

        $data['requisitions'] = $result->getResult('array');
        echo view('requisition/search',$data);
            
    }
    
}
    