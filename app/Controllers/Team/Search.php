<?php namespace App\Controllers\Team;

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
        $data['s_role_id'] = -1;
        $data['s_status_id'] = -1;

        //check if admin user - if not - display noaccess
        if(!$this->ionAuth->isAdmin($data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }

        if ($this->request->getPost('filter') == "true") {
            $data['s_role_id'] = $this->request->getPost('s_role_id');
            $data['s_status_id'] = $this->request->getPost('s_status_id');
        }

        echo view('team/search',$data);
            
    }
    
}
    