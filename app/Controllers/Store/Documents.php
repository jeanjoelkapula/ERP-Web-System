<?php namespace App\Controllers\Store;

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
    

    public function index($store=0)
    {    
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        //check for existence of the store id if coming from get request
        if (!empty($this->request->getGet('store'))){
            $store = $this->request->getGet('store');
        }

        if (($store ==null) || ($store =='') ||  ($store <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            echo $this->request->getGet('store');
            exit();
        }
        else {

            //get store details from db
            $sql="SELECT * FROM TBL_STORE WHERE STORE_ID = $store;";
            $result = $this->db->query($sql)->getResult('array');

            $data['store_info'] = $result;

            $store_name="";
                    
            foreach($result as $info){
                $store_name = $info['STORE_NAME'];
            }

            //create store directory if non existent 
            $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/stores/'.$store.'-'.$store_name;
            if (!file_exists($full_path)) {
                mkdir($full_path, 0777, true);
            }

            //get store files
            $file_results  = array();

            $sql = "SELECT * FROM TBL_STORE_DOCS WHERE STORE_ID = $store";
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

            $files = scandir($full_path);                 
            $data['file_results'] = $file_results;
            $data['store'] = $store;
        }
        
        echo view('store/documents', $data);
            
    }
    
    public function dl(){
        //check for existence of file name and store id in post request
        if ((!empty($this->request->getPost('file'))) && (!empty($this->request->getPost('store')))){
            $store = $this->request->getPost('store');
            $sql="SELECT * FROM TBL_STORE WHERE STORE_ID = $store;";
            $result = $this->db->query($sql)->getResult('array');
            $file = $this->request->getPost('file');
            $data['store_info'] = $result;

            $store_name="";
                    
            foreach($result as $info){
                $store_name = $info['STORE_NAME'];
            }

            //get store folder path and set headers for download
            $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/stores/'.$store.'-'.$store_name.'/';

            // Get parameters
            $file = urldecode($_REQUEST["file"]); 

            $filepath =  $full_path.trim($file);

            // print_r($filepath);
            // die();
        
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
            
            return redirect()->to('/store/documents?store='.$store);

            exit(); 

            // header('Content-Disposition: attachment; filename="'.basename($file).'"');
            // header('Expires: 0');
            // header('Cache-Control: must-revalidate');
            // header('Pragma: public');
            // header('Content-Length: ' . filesize($full_path."/".$file));
            // readfile($full_path."/".$file);

            // exit();         
            
        }
    }

    public function del(){
        //check for file name and store id in submission
        if ((!empty($this->request->getPost('file'))) && (!empty($this->request->getPost('store')))){

            //get store details
            $store = $this->request->getPost('store');
            $sql="SELECT * FROM TBL_STORE WHERE STORE_ID = $store;";
            $result = $this->db->query($sql)->getResult('array');
            $files = $this->request->getPost('file');
            $data['store_info'] = $result;

            $store_name="";
                    
            foreach($result as $info){
                $store_name = $info['STORE_NAME'];
            }

            $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/stores/'.$store.'-'.$store_name;
            foreach($files as $file) { 

                if (unlink($full_path."/".$file)) {  
                    $sql = "DELETE FROM TBL_STORE_DOCS WHERE STORE_ID = $store AND DOCUMENT_NAME = '$file'";
                    $this->db->query($sql);
                }  

            }

            return redirect()->to('/store/documents?store='.$store);

            exit();
            
        }
    }

}
    