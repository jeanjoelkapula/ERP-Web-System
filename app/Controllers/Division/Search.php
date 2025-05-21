<?php namespace App\Controllers\Division;

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
        
        $sql = "select * from TBL_DIVISION";

        $object = $this->db->query($sql);

        $data['object']=$object;

        echo view('division/search',$data);
            
    }
    
}
    