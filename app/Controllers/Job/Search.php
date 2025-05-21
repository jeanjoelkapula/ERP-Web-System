<?php namespace App\Controllers\Job;

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
    

    public function index($type = '')
    {
        //check if admin user - if not - display noaccess
        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }
        
        $data = $this->data;
        $data['s_job_type_id'] = -1;
        if ($this->request->getPost('filter') == "true") {
            $data['s_job_type_id'] = $this->request->getPost('s_job_type_id');
        }
       
        echo view('job/search',$data);
        
    }

    public function job_types(){
        $data = $this->data;
        
        echo view('job/job_types',$data);
    }


    public function dl($rpt_type = ''){
        $data = $this->data;


        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'joblist'){
            $job_type = -1;
            $job_type = $this->request->getGet('s_job_type_id');
            $generatefile->generate_joblist($data,$job_type);
            exit;
        }
    }


   
    

    
}
    