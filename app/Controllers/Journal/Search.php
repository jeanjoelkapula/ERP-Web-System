<?php namespace App\Controllers\Journal;

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
        if(!($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('stock_controller') || $this->ionAuth->inGroup('admin'))){
            return redirect()->to('/noAccess');
            exit();
        }
        $data = $this->data;
        $sql = "SELECT SJ.*,CONCAT(U.FIRST_NAME,' ',U.LAST_NAME) AS FULL_NAME, H.HUB_NAME,S.DESCRIPTION,S.AVG_COST FROM TBL_STOCK_JOURNAL SJ
            INNER JOIN TBL_HUB H ON H.HUB_ID = SJ.HUB_ID
            INNER JOIN TBL_STOCK S ON S.EBQ_CODE = SJ.EBQ_CODE
            INNER JOIN TBL_USER U ON U.ID = SJ.USER_ID
            ;";
        $result = $this->db->query($sql);

        $data['journals'] = $result->getResult('array');
        echo view('journal/search',$data);
            
    }
    
}
    