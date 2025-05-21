<?php namespace App\Controllers\Area;

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
        $sql = "SELECT area.*,region.REGION_NAME FROM TBL_AREA area JOIN TBL_REGION region ON area.REGION_NO = region.REGION_NO ;";
        $result = $this->db->query($sql);
        $data["result"] = $result;

        echo view('area/search',$data);
            
    }
    
}
    