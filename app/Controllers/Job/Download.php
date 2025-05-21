<?php namespace App\Controllers\Job;

use \App\Controllers\BaseController;

class Download extends BaseController {

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
        
        $data = $this->data;

        if($this->request->getMethod(true) == "GET") {
            $file = $this->request->getGet('file');
        }
        else{
            $file = trim($this->request->getPost('file'));
        }

        if (($file ==null) || ($file =='')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $data["file"]=$file;

            $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/uploads/jobs/';
            
            if(isset($_REQUEST["file"])){
                // Get parameters
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
            }               
        }
        
        echo view('job/documents', $data);
            
    }
    
}
    