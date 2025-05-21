<?php namespace App\Controllers\Store;

use \App\Controllers\BaseController;
use \App\Models\GenerateFile;


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
        
        $data = $this->data;
        
        $sql = "SELECT S.STORE_ID,ST.STORE_TYPE_ID, ST.STORE_TYPE_DESCRIPTION, S.AREA_ID, ST_X(LOCATION) AS LAT, ST_Y(LOCATION) AS LNG, S.CONTACT_NUMBER, 
        S.STORE_NAME, S.FF_CODE, S.OPENING_DATE, S.MAINTENANCE_MONTH, S.IN_CENTER, S.TRADING_SIZE, S.BRANCH_SIZE, S.STORE_MANAGER, S.IS_OPEN
         FROM TBL_STORE S, TBL_STORE_TYPE ST
         WHERE (S.STORE_TYPE_ID = ST.STORE_TYPE_ID);";      

        $object = $this->db->query($sql);

        $data['object']=$object;        

        if ($this->request->getPost('form_update_store') == "true") {    
            
            return redirect()->to('/store/search');
            exit();
        }



        echo view('store/search', $data);
            
    }

    public function dl($rpt_type = ''){
        $data = $this->data;

        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'storelist'){
            $generatefile->generate_storelist($data);
            exit;
        }
    }
    
}
    