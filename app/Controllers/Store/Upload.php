<?php namespace App\Controllers\Store;

use \App\Controllers\BaseController;

class Upload extends BaseController {

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
        
        if($this->request->getMethod(true) == "GET") {
            $store = $this->request->getGet('store');
        }
        else{
            $store = trim($this->request->getPost('store'));
        }

        if (($store ==null) || ($store =='') ||  ($store <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $data["store"]=$store;
            $sql="SELECT * FROM TBL_STORE WHERE STORE_ID = $store;";
            $result = $this->db->query($sql)->getResult('array');

            $data['store_info'] = $result;

            if ($this->request->getPost('upload')=="true") {

                if (!empty($_FILES)) {
                    $store = trim($this->request->getPost('store'));
                    try{
          
                    $store_name="";
                    
                    foreach($result as $info){
                        $store_name = $info['STORE_NAME'];
                    }

                    
                    $total = count($_FILES['file']['name']);
                    $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/stores/'.$store.'-'.$store_name;
                        if (!file_exists($full_path)) {
                            mkdir($full_path, 0777, true);
                        }

                        for($i=0; $i < $total; ++$i) {
                            
                            $tempFile = $_FILES['file']['tmp_name'][$i];                    
                            
                            $targetPath = $full_path. '/';  
                            
                            $targetFile =  $targetPath. $_FILES['file']['name'][$i];  
                        
                            if (move_uploaded_file($tempFile,$targetFile)){

                                try{

                                    $sql = "SELECT COUNT(DOCUMENT_NAME) AS COUNT FROM TBL_STORE_DOCS WHERE (STORE_ID = $store) AND (DOCUMENT_NAME = ".$this->db->escape($_FILES['file']['name'][$i]).");";
                                    $r = $this->db->query($sql);
                                    $found = false;

                                    foreach($r->getResult('array') as $row){
                                        if ($row['COUNT'] > 0){
                                            $found =true;
                                        }
                                    }
                                    
                                    if ($found==false) {
                                        $sql="INSERT INTO TBL_STORE_DOCS (STORE_ID, DOCUMENT_CREATED_DATE, DOCUMENT_NAME) VALUES ($store, Now(),".$this->db->escape($_FILES['file']['name'][$i]).");";
                                        $this->db->query($sql);
                                    }

                                }
                                catch(Exception $e) {

                                }
                                
                               
                            } 
                        }

                        json_encode(array("url"=>"/store/documents?store?$store"));
                        die();
                    }
                    catch(Exception $e){
                        
                    }
                }

            }

            
        }
        
        echo view('store/upload', $data);
            
    }
    
}
    