<?php namespace App\Controllers\Job;

use \App\Controllers\BaseController;

class Update extends BaseController {

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
    

    public function index($entity_id)
    {
        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }

        $data = $this->data;

        $data['entity_id'] = $entity_id;

        echo view('job/view_detail',$data);
        
    }

    public function ajax($rpt_type){
        
        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }

        $data = $this->data;

        if($rpt_type == 'enable_jobtype'){
            $job_type_id = $this->request->getPost('job_type_id');
            $sql = "update TBL_JOB_TYPE set ACTIVE = 1 where JOB_TYPE_ID = $job_type_id";
            $query = $this->db->query($sql);
            $res = '';
            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }

            echo json_encode($res);
        }

        if($rpt_type == 'disable_jobtype'){
            $job_type_id = $this->request->getPost('job_type_id');
            $sql = "update TBL_JOB_TYPE set ACTIVE = 0 where JOB_TYPE_ID = $job_type_id";
            $query = $this->db->query($sql);

            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }

            echo json_encode($res);
        }

        if($rpt_type == 'cancel_job'){
            $job_id = $this->request->getPost('job_id');
            $sql = "update TBL_JOB set JOB_STATUS = 'CANCELLED', COMPLETION_DATE = now() where JOB_ID = $job_id";
            $query = $this->db->query($sql);

            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }

            echo json_encode($res);
        }
       
    }

   
    

    
}
    