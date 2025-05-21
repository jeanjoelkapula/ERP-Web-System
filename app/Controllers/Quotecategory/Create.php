<?php namespace App\Controllers\Quotecategory;

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
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/quotecategory/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";
        

        if ($this->request->getPost('form_create_quote_category') == "true") {

            $category_name = $this->db->escape(trim($this->request->getPost('category_name')));
        
            $sql = "insert into TBL_QUOTE_STOCK_CATEGORY (NAME) VALUES ($category_name);";
            $this->db->query($sql);
          
            return redirect()->to('/quotecategory/search/');
            exit();

        }
        
       
        echo view('quotecategory/create',$data);
        
    }    
    
}
    