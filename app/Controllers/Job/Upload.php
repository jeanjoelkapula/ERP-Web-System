<?php namespace App\Controllers\Job;

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

        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }

        if($this->request->getMethod(true) == "GET") {
            $job = $this->request->getGet('job');
        }
        else{
            $job = trim($this->request->getPost('job'));
        }

        if (($job ==null) || ($job =='') ||  ($job <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $data["job"]=$job;
            $sql="SELECT * FROM TBL_JOB WHERE JOB_ID = $job;";
            $result = $this->db->query($sql)->getResult('array');

            $data['job_info'] = $result;

            if ($this->request->getPost('upload')=="true") {

                if (!empty($_FILES)) {
                    $job = trim($this->request->getPost('job'));
                    try{
          
                    $order_no=0;
                    $created_date="";
                    
                    foreach($result as $info){
                        $order_no = $info['ORDER_NO'];
                        $created_date = $info['CREATED_DATE'];
                    }

                    
                    $total = count($_FILES['file']['name']);
                    
                    $splitInput = explode(" ",$created_date);

                    $stripDate = $splitInput[0];

                    $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/jobs/'.$job.'_'.$order_no.'_'.$stripDate;
                        if (!file_exists($full_path)) {
                            mkdir($full_path, 0777, true);
                        }

                        for($i=0; $i < $total; ++$i) {
                            
                            $tempFile = $_FILES['file']['tmp_name'][$i];                    
                            
                            $targetPath = $full_path. '/';  
                            
                            $targetFile =  $targetPath. $_FILES['file']['name'][$i]; 
                            
                            $docName = $this->db->escape($_FILES['file']['name'][$i]);

                            if (move_uploaded_file($tempFile,$targetFile)){
                                                    
                                $sql = "SELECT COUNT(DOCUMENT_NAME) AS COUNT FROM TBL_STORE_DOCS WHERE (JOB_ID = $job) AND (DOCUMENT_NAME = $docName);";
                                
                                $r = $this->db->query($sql);
                                $found = false;

                                foreach($r->getResult('array') as $row){
                                    if ($row['COUNT'] > 0){
                                        $found =true;
                                    }
                                }
                                
                                if (!$found) {

                                    // GET THE STORE ID FOR THE JOB
                                    $sqlStoreID = "SELECT STORE_ID FROM TBL_QUOTE TQ INNER JOIN TBL_ORDER TBLO ON TQ.QUOTE_ID = TBLO.QUOTE_ID
                                    INNER JOIN TBL_JOB TJ ON TBLO.ORDER_NO = TJ.ORDER_NO WHERE TJ.JOB_ID = $job;";
                                    $result = $this->db->query($sqlStoreID);

                                    $storeID = 0;

                                    foreach($result->getResult('array') as $row){
                                        $storeID = $row['STORE_ID'];
                                    }
                                    
                                    $sql="INSERT INTO TBL_STORE_DOCS (STORE_ID,DOCUMENT_CREATED_DATE, DOCUMENT_NAME, JOB_ID) VALUES ($storeID, Now(),$docName, $job);";                                    
                                    $this->db->query($sql);
                                }                                                                                      
                            } 
                        }

                        json_encode(array("url"=>"/job/documents?job?$job"));
                        die();
                    }
                    catch(Exception $e){
                        
                    }
                }

            }

            
        }
        
        echo view('job/upload', $data);
            
    }
    
}
    