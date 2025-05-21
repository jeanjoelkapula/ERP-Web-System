<?php namespace App\Controllers\Invoice;

use \App\Controllers\BaseController;

class Printer extends BaseController {
    
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
            if(!($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('electrical_admin')  || $this->ionAuth->inGroup('admin'))){
                return redirect()->to('/noAccess');
                exit();
            }
            require_once APPPATH . 'Config/Pepkor_Constants.php';
            $data = $this->data;
            $data["action_type"] = "preview";
            $data["url"] = "/invoice/preview/$entity_id";
            $data['pepkor_address'] = ADDRESS;
            $data['pepkor_billing'] = BILLING;
            $data['tax'] = TAX;
   
            if (!isset($entity_id) || ($entity_id <= 0)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
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
     
                 $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_INVOICE_STOCK WHERE INVOICE_ID = '$entity_id';";
                 $result = $this->db->query($sqlQuery);
                 $stock_found = false;
     
                 foreach ($result->getResult('array') as $row): 
                     { 
                         if ($row['COUNT'] > 0) {
                             $stock_found = true;
                         }
                     }
                     endforeach;
     
     
     
                 if ($invoice_found) {
                     $sql = "SELECT invoice.*, store.*, invoice_order.*, store_type.STORE_TYPE_DESCRIPTION, job_type.JOB_TYPE_DESCRIPTION,brand.BRAND_NAME 
                     FROM TBL_INVOICE invoice 
                     LEFT JOIN TBL_JOB job ON job.JOB_ID = invoice.JOB_ID
                     LEFT JOIN TBL_JOB_TYPE job_type ON job_type.JOB_TYPE_ID = job.JOB_TYPE_ID
                     LEFT JOIN TBL_ORDER invoice_order ON invoice_order.ORDER_NO = job.ORDER_NO
                     LEFT JOIN TBL_INVOICE_STOCK invoice_stock ON invoice_stock.INVOICE_ID = invoice.INVOICE_ID
                     LEFT JOIN TBL_QUOTE quote on quote.QUOTE_ID = invoice_order.QUOTE_ID   
                     LEFT JOIN TBL_STORE store ON store.STORE_ID = quote.STORE_ID 
                     LEFT JOIN TBL_STORE_TYPE store_type ON store_type.STORE_TYPE_ID = store.STORE_TYPE_ID 
                     LEFT JOIN TBL_BRAND brand ON brand.BRAND_ID = store_type.BRAND_ID
                     WHERE (invoice.INVOICE_ID = $entity_id);";
                     $result = $this->db->query($sql);
     
                     foreach ($result->getResult('array') as $row): 
                     { 
                         $data['invoice']['id'] = $row['INVOICE_ID'];
                         $data['invoice']['pastel_no'] = $row['PASTEL_INVOICE_NO']; 
                         $data['invoice']['created'] = $row['INVOICE_DATE_CREATED'];
                         $data['invoice']['pastel_created'] = $row['PASTEL_INVOICE_DATE'];
                         $data['invoice']['account'] = $row['ACCOUNT'];
                         $data['invoice']['tax_reference'] = $row['TAX_REFERENCE'];

                         $data['tax_percentage'] = $row['TAX_PERCENTAGE'];
                         $data['discount_percentage'] = $row['DISCOUNT_PERCENTAGE'];

                         $data['amount']['total'] = $row['INVOICE_AMOUNT'];
                         $data['amount']['tax'] = $row['INVOICE_AMOUNT']* ($data['tax_percentage']/100);
                         $data['amount']['excl_tax'] = $data['amount']['total']-$data['amount']['tax'];
                         $data['amount']['discount'] = $data['amount']['excl_tax']*($data['discount_percentage']/100);
                         $data['amount']['sub_total'] = $data['amount']['excl_tax']+ $data['amount']['discount'];

                         $data['store']['id'] = $row['STORE_ID']; 
                         $data['store']['name'] = $row['STORE_NAME']; 
                         $data['store']['type'] = $row['STORE_TYPE_DESCRIPTION']; 
                         $data['store']['brand'] = $row['BRAND_NAME']; 

                         $data['job']['type'] = $row['JOB_TYPE_DESCRIPTION'];

                         $data['order']['no'] = $row['ORDER_NO'];
                     }
                     endforeach;
                 }
                 else {
                     throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                 }
     
                 if($stock_found){

                    $sql = "SELECT DISTINCT ORIGIN FROM TBL_INVOICE_STOCK WHERE INVOICE_ID=$entity_id;";
                    $result = $this->db->query($sql);
                    // Categorise vocs into their distinct voc
                    $stock_merged = array();
                    foreach ($result->getResult('array') as $voc): 
                    { 
                        $origin = $voc['ORIGIN'];
                        //VOC
                        $sql = "SELECT invoice_stock.*,stock.DESCRIPTION FROM TBL_INVOICE_STOCK invoice_stock
                        INNER JOIN TBL_STOCK stock ON stock.EBQ_CODE = invoice_stock.EBQ_CODE
                        WHERE (invoice_stock.INVOICE_ID=$entity_id) AND (invoice_stock.ORIGIN='$origin');";
        
                        $query = $this->db->query($sql);
                        $origin_stock = $query->getResultArray();
                        $stock_merged[$origin] = $origin_stock;
                    }
                    endforeach;

                    $data['invoice_stock'] = $stock_merged;
    
                 }
             
            }               
                           
        echo view('invoice/invoice_print',$data);
        
    }
    
    
}
    