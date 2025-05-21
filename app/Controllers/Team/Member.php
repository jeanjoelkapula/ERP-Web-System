<?php namespace App\Controllers\Team;

use \App\Controllers\BaseController;
use \App\Models\Email;
use \App\Models\GenerateFile;

class Member extends BaseController {

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
    

    public function index($entity_id=0)
    {
         
        $data = $this->data;
        
        $data["entity_id"] = $entity_id;  

        $data["updated"] = $this->request->getGet('updated');
        $data["changepassword"] = 0;
        $data["changepassword"] = $this->request->getGet("changepassword");

        if ($this->request->getPost('form_update') == "true") {
            $active = 1;
            $entity_id =  $this->request->getPost('entity_id');
            $first_name = $this->request->getPost('first_name');
            $last_name = $this->request->getPost('last_name');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            $role_id = $this->request->getPost('role_id');
            $active = $this->request->getPost('active');
            if(($active == 'on') && ($entity_id != $data['_user_id'])){
                $active = 0;
                $sql = "update TBL_USER set active = 0 where id = $entity_id";
            } else {
                $sql = "update TBL_USER set active = 1 where id = $entity_id";
                $active = 1;
            }
            $query = $this->db->query($sql);
            $groups = array($role_id);
            
            
            
            // only update password if it was set 
            if ($this->request->getPost('password')){
                $password = $this->request->getPost('password');
                $password_confirm = $this->request->getPost('password_confirm');
                //set details array to update user
                $data = array(
                    "first_name"=>$first_name,
                    "last_name"=>$last_name,
                    "email"=>$email,
                    "phone"=>$phone,
                    "password"=>$password,
                    "password_confirm"=>$password_confirm,
                    "groups"=>$groups,
                    "temp_password"=>'0'
                );
            } else {
                $data = array(
                    "first_name"=>$first_name,
                    "last_name"=>$last_name,
                    "email"=>$email,
                    "phone"=>$phone,
                    "groups"=>$groups
                );
            }
            //update user
            if($this->ionAuth->update($entity_id,$data)){
                // user updated okay
                $sql = "select role_id from TBL_USER_ROLE where user_id = $entity_id";
                $query = $this->db->query($sql);
                $current_role_id = $query->getRow();
                if($role_id != $current_role_id->role_id){
                    $sql = "update TBL_USER_ROLE set role_id = $role_id where user_id = $entity_id";
                    $query = $this->db->query($sql);
                }
                return redirect()->to('/team/member/'.$entity_id.'?updated=1');
                exit();
            } else {
                // error updating user
                
                return redirect()->to('/team/member/'.$entity_id.'?updated=0');
                exit();
            }
         
            
        }

        //check if admin user
        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            if($data['_user_id'] != $entity_id){
                return redirect()->to('/noAccess');
                exit();
            }
        }
        
        echo view('team/member', $data);
            
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

    public function ajax($rpt_type = ''){

        $data = $this->data;

        if($rpt_type == 'suspend_user'){
            $user_id = $this->request->getPost('user_id');
            if ($user_id != $data['_user_id']) {
                $sql = "update TBL_USER set active = 0 where id = $user_id";
                $query = $this->db->query($sql);
                $res = '';
                if($query !== false){
                    $res = 'ok';
                } else {
                    $res = 'error';
                }
            }
            else {
                $res = 'error';
            }

            echo json_encode($res);
        }

        if($rpt_type == 'activate_user'){
            $user_id = $this->request->getPost('user_id');
            $sql = "update TBL_USER set active = 1 where id = $user_id";
            $query = $this->db->query($sql);
            $res = '';
            if($query !== false){
                $res = 'ok';
            } else {
                $res = 'error';
            }

            echo json_encode($res);
        }
        
    }
    



     
    function _randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 12; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    

    
}