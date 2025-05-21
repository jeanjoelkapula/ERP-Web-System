<?php namespace App\Controllers\Invoice;

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
    

    public function index($entity_id=0)
    {
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        if(!($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('electrical_admin') || $this->ionAuth->inGroup('admin'))){
            return redirect()->to('/noAccess');
            exit();
        }
        else {
          
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_INVOICE  WHERE INVOICE_ID = '$entity_id';";
            $result = $this->db->query($sqlQuery);
            $invoice_found = false;

            foreach ($result->getResult('array') as $row): 
            { 
                if ($row['COUNT'] > 0) {
                    $invoice_found = true;
                }
            }
            endforeach;

            if ($invoice_found && $this->request->getPost('form_invoice_paid') == "true") {
                    $sql = "UPDATE TBL_INVOICE SET INVOICE_PAID = 1 WHERE INVOICE_ID = $entity_id;";
                    $this->db->query($sql);
                    return redirect()->to('/invoice/search');
                    exit();
               
            }
        }
   
    }
    
}
    