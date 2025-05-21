<?php namespace App\Controllers\Job;

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
        //check if admin user - if not - display noaccess
        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }
        
        $data = $this->data;

        if($this->request->getPost('form_create') == "true"){
            $order_no = $this->request->getPost('order_no');
            $job_notes = $this->db->escape($this->request->getPost('job_notes'));
            $job_level = $this->request->getPost('job_level');
            $job_type = $this->request->getPost('job_type_id');
            $sql = "insert into TBL_JOB (ORDER_NO,CREATED_DATE,NOTES,COMPLETION_DATE,JOB_LEVEL,JOB_TYPE_ID) VALUES ('$order_no',now(),$job_notes,null,$job_level,$job_type) ";
            $query = $this->db->query($sql);

            return redirect()->to('/job/search/');
            exit();
        }
       
        echo view('job/create',$data);
        
    }

    public function ajax($rpt_type){
        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }
        
        $data = $this->data;

        if($rpt_type == 'get_order_details'){
            $order_no = $this->request->getPost('order_no');
            $sql = "select o.*,u.first_name,u.last_name
                    from TBL_ORDER o
                    inner join TBL_USER u on u.id = o.USER_ID
                    where o.ORDER_NO = '$order_no'";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            
            echo json_encode($res);        

        }

        if($rpt_type == 'get_order_stock'){
            $order_no = $this->request->getPost('order_no');
            $sql = " select s.*,os.*
                    from TBL_STOCK s
                    inner join TBL_ORDER_STOCK os on os.EBQ_CODE = s.EBQ_CODE

                    where os.ORDER_NO = '$order_no'";
            $query = $this->db->query($sql);
            $res = $query->getResultArray();
            echo json_encode($res);
        }
    }

   
    public function create_jobtype(){
        $job_type_desc = $this->db->escape($this->request->getPost('new_job_type_name'));
        $sql = "insert into TBL_JOB_TYPE (JOB_TYPE_DESCRIPTION,ACTIVE) VALUES ($job_type_desc,1);";
        $query = $this->db->query($sql);
        return redirect()->to('/job/search/job_types');
        exit;
    }

    
}
    