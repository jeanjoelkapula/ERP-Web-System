<?php namespace App\Controllers\Job;

use \App\Controllers\BaseController;

class Documents extends BaseController {

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
    

    public function index($job=0)
    {    
        if(!$this->ionAuth->isAdmin($this->data['_user_id']) && !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();  
        }
        
        $job = $this->request->getGet('job');

        if (($job ==null) || ($job =='') ||  ($job <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            echo $this->request->getGet('job');
            exit();
        }
        else {
            $sql="SELECT * FROM TBL_JOB WHERE JOB_ID = $job;";
            $result = $this->db->query($sql)->getResult('array');

            $data['job_info'] = $result;

            $order_no=0;
            $created_date="";
                    
            foreach($result as $info){
                $order_no = $info['ORDER_NO'];
                $created_date = $info['CREATED_DATE'];
            }

            $splitInput = explode(" ",$created_date);

            $stripDate = $splitInput[0];

            $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/jobs/'.$job.'_'.$order_no.'_'.$stripDate;

            if (!file_exists($full_path)) {
                mkdir($full_path, 0777, true);
            }

            // GET THE STORE ID FOR THE JOB
            $sqlStoreID = "SELECT STORE_ID FROM TBL_QUOTE TQ INNER JOIN TBL_ORDER TBLO ON TQ.QUOTE_ID = TBLO.QUOTE_ID
            INNER JOIN TBL_JOB TJ ON TBLO.ORDER_NO = TJ.ORDER_NO WHERE TJ.JOB_ID = $job;";
            $result = $this->db->query($sqlStoreID);

            $storeID = 0;

            foreach($result->getResult('array') as $row){
                $storeID = $row['STORE_ID'];
            }

            $file_results  = array();

            $sql = "SELECT * FROM TBL_STORE_DOCS WHERE STORE_ID = $storeID AND JOB_ID = $job";
            $result = $this->db->query($sql);
                    
            foreach($result->getResult('array') as $row) {
                $file = $row['DOCUMENT_NAME'];
                if ( '.'!=$file && '..'!=$file) { 
                    $file_path = $full_path.'/'.$file;
                    // Get the file extension
                    $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);  

                    $obj['name'] = $file;
                   //  $obj['size'] = filesize($full_path.'/'.$file);
                    $obj['ext'] = $file_ext;
                    $obj['date_created'] = $row['DOCUMENT_CREATED_DATE'];
                    $file_results[] = $obj;
                }
            }

            $data['file_results'] = $file_results;
            $data['job'] = $job;
        }
        
        echo view('job/documents', $data);
            
    }

    public function dl(){

        $data = $this->data;
     
        //check for existence of file name and job id in post request
        if ((!empty($this->request->getPost('file'))) && (!empty($this->request->getPost('job')))){

            $file = $this->request->getPost('file');

            $job = $this->request->getPost('job');

            $sql="SELECT * FROM TBL_JOB WHERE JOB_ID = $job;";

            $result = $this->db->query($sql)->getResult('array');

            $file = $this->request->getPost('file');
            $data["file"]=$file;

            $data['job_info'] = $result;

            $order_no=0;
            $created_date="";
                    
            foreach($result as $info){
                $order_no = $info['ORDER_NO'];
                $created_date = $info['CREATED_DATE'];
            }

            $splitInput = explode(" ",$created_date);

            $stripDate = $splitInput[0];

            $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/jobs/'.$job.'_'.$order_no.'_'.$stripDate.'/';       
        
            $file = urldecode($_REQUEST["file"]); 

            $filepath =  $full_path.trim($file);
        
            // Process download
            if(file_exists($filepath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filepath));
                flush(); // Flush system output buffer
                readfile($filepath);
                die();
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();                                
            }            
             
            return redirect()->to('/job/documents?job='.$job);

            exit();   
        }
    }

    public function del(){
        //check for file name and job id in submission
        if ((!empty($this->request->getPost('file'))) && (!empty($this->request->getPost('job')))){

            $files = $this->request->getPost('file');

            $job = $this->request->getPost('job');

            $sql="SELECT * FROM TBL_JOB WHERE JOB_ID = $job;";

            $result = $this->db->query($sql)->getResult('array');

            $file = $this->request->getPost('file');
            $data["file"]=$file;

            $data['job_info'] = $result;

            $order_no=0;
            $created_date="";
                    
            foreach($result as $info){
                $order_no = $info['ORDER_NO'];
                $created_date = $info['CREATED_DATE'];
            }

            $splitInput = explode(" ",$created_date);

            $stripDate = $splitInput[0];

            $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/jobs/'.$job.'_'.$order_no.'_'.$stripDate;                       

            foreach($files as $file) { 

                if (unlink($full_path."/".$file)) {  
                    $sql = "DELETE FROM TBL_STORE_DOCS WHERE JOB_ID = $job AND DOCUMENT_NAME = '$file'";
                    $this->db->query($sql);
                }  
            }

            return redirect()->to('/job/documents?job='.$job);

            exit();     
        }
    }
}
    