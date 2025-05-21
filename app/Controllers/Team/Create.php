<?php namespace App\Controllers\Team;

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
        
        $data = $this->data;
        $data['error_code'] = 0;

        //check if admin user - if not - display noaccess
        if(!$this->ionAuth->isAdmin($data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }

        if ($this->request->getPost('form_create') == "true") {
            
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $password_confirm = $this->request->getPost('password_confirm');
            $phone = $this->request->getPost('phone');
            $firstname = $this->request->getPost('firstname');
            $lastname = $this->request->getPost('lastname');
            $role_id = $this->request->getPost('role_id');
            $additional_data = array(
                'first_name'=>$firstname,
                'last_name'=>$lastname,
                'phone'=>$phone,
            );
            $role = array($role_id);

            /// check if email in use, if not return new user id - even though we validate this with javascript, incase the user has javascript disabled, also validate serverside
            //TODO: could return the user_id instead of redirecting & then add popup asking if they want to view the new user or not
            if($this->check_email_in_use($email) == 0){
                $new_user_id = $this->ionAuth->register($email,$password,$email,$additional_data,$role);
                return redirect()->to('/team/search/');
                exit();
            } else {
                //if email in use
                $data['error_code'] = 1;
                echo view('team/create',$data);
                exit();
            }

        }
        
       
        echo view('team/create',$data);
        
    }

    public function ajax($rpt_type = ''){

        $data = $this->data;


        
    }
    
    public function check_email_in_use($email = ''){
        $data = $this->data;
        $check = 0;
        $email = $this->db->escape($this->request->getPost('email'));
        $sql = "select count(*) as cnt from TBL_USER where email = $email";
        $query = $this->db->query($sql);
        foreach($query->getResult() as $row){
            $check = $row->cnt;
        }
           
        echo $check;
        
    }
    
}
    