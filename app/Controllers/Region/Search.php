<?php namespace App\Controllers\Region;

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
        
        $data = $this->data;
        
        $sql = "select REGION_NO, REGION_NAME, REGION_MANAGER, TBL_REGION.EMAIL, TBL_REGION.CONTACT_NUMBER, DIVISION_NAME from TBL_REGION, TBL_DIVISION WHERE TBL_REGION.DIVISION_ID = TBL_DIVISION.DIVISION_ID order by REGION_NO;";

        $object = $this->db->query($sql);

        $data['object']=$object;

        echo view('region/search',$data);
            
    }
    
}
    