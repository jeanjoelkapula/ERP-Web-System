<?php namespace App\Controllers\Hub;

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
        $sql = "SELECT hub.HUB_ID,ST_X(hub.HUB_LOCATION) as HUB_LATITUDE, ST_Y(hub.HUB_LOCATION) as HUB_LONGITUDE,hub.HUB_NAME,hub.HUB_DESCR,region.REGION_NO,region.REGION_NAME FROM TBL_HUB hub JOIN TBL_REGION region ON hub.REGION_ID = region.REGION_NO ;";
        $result = $this->db->query($sql);
        $data["result"] = $result;

        echo view('hub/search', $data);
            
    }
}
    