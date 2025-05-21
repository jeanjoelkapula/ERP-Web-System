<?php namespace App\Controllers\Invoice;

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
        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }

        require_once APPPATH . 'Config/Pepkor_Constants.php';
        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/invoice/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";
        $data['tax'] = TAX;

        if ($this->request->getPost('form_create_invoice') == "true") {
            $order_no =  $this->db->escape(trim($this->request->getPost('order_no')));
            $account = $this->db->escape(trim($this->request->getPost('account')));
            $tax_reference = $this->db->escape(trim($this->request->getPost('tax_reference')));
            $pastel_date = $this->db->escape(trim($this->request->getPost('pastel_date')));
            $pastel_invoice = $this->db->escape(trim($this->request->getPost('pastel_invoice')));
            $discount_percentage = $this->request->getPost('discount');
            $job_id = $this->request->getPost('job_id');
            $tax_percentage = TAX['percentage'];
            $total = $this->request->getPost('total');
            $invoice_stock =  $this->request->getPost('stock');

            //Insert into invoice table
            $sql = "INSERT INTO TBL_INVOICE(ORDER_NO,INVOICE_PAID,";

            if (isset($job_id) && !empty($job_id)) {
                $sql .= "JOB_ID,";
            }
             
            $sql .= "INVOICE_DATE_CREATED,PASTEL_INVOICE_DATE,PASTEL_INVOICE_NO,INVOICE_AMOUNT,TAX_PERCENTAGE,DISCOUNT_PERCENTAGE,ACCOUNT,TAX_REFERENCE) VALUES
                 ($order_no,
                1,";
            if (isset($job_id) && !empty($job_id)) {
                $sql .= "$job_id,";
            }
            $sql .= "
                now(),
                $pastel_date,
                $pastel_invoice,
                $total,
                $tax_percentage,
                $discount_percentage,
                $account,
                $tax_reference
                );";

            $this->db->query($sql); 

            $invoice_id = $this->db->insertID();
            if(isset($invoice_stock)){
                foreach(array_keys($invoice_stock) as $source){
                    foreach (array_keys($invoice_stock[$source])  as $ebq) {
                        $quantity = $invoice_stock[$source][$ebq]['quantity'];
                        $hub_id = $invoice_stock[$source][$ebq]['hub_id'];
                        $avg_cost = $invoice_stock[$source][$ebq]['avg_cost'];
                        $markup = $invoice_stock[$source][$ebq]['markup'];
                        $category = $invoice_stock[$source][$ebq]['category'];

                        //Insert used stock in Invoice Stock table
                        $invoice_stock_insert = "INSERT INTO TBL_INVOICE_STOCK(INVOICE_ID,EBQ_CODE,QUANTITY,STOCK_CATEGORY,AVG_COST,MARKUP,HUB_ID,ORIGIN) VALUES
                        ($invoice_id,'$ebq',$quantity,$category,$avg_cost,$markup,$hub_id,'$source');";

                        $this->db->query($invoice_stock_insert);
                        
                        //Remove quantities from hub stock 
                        $hub_stock_removal = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY-$quantity) WHERE (EBQ_CODE='$ebq') AND (HUB_ID='$hub_id');";
                        $this->db->query($hub_stock_removal);
                    };
                }
            }

            if (isset($job_id) && !empty($job_id)) {
                $sql = "UPDATE TBL_JOB SET COMPLETION_DATE = NOW(), JOB_STATUS = 'COMPLETED' WHERE ORDER_NO = $order_no AND JOB_ID = $job_id AND JOB_STATUS != 'CANCELLED'";
                $this->db->query($sql);    
            }

            return redirect()->to('/invoice/search/');
            exit();

        }
        
       
        echo view('invoice/create',$data);
        
    } 

    public function ajax($rpt_type = ''){

        if(!($this->ionAuth->inGroup('electrical_manager') &&  !($this->ionAuth->inGroup('admin')))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;

        if($rpt_type == 'order_get_quote_details'){
            $order_no = $this->db->escape(trim($this->request->getPost('order_no')));
            
            $sql = "SELECT * FROM TBL_ORDER WHERE ORDER_NO = $order_no";

            $result = $this->db->query($sql)->getResultArray();
            if ($result[0]['QUOTE_ID'] != null) {
                $sql = "SELECT q.*,s.STORE_NAME, jt.JOB_TYPE_DESCRIPTION, j.JOB_ID FROM TBL_ORDER o 
                JOIN TBL_QUOTE q ON q.QUOTE_ID = o.QUOTE_ID 
                JOIN TBL_STORE s ON s.STORE_ID = q.STORE_ID 
                JOIN TBL_ACTION_TYPE a ON a.ACTION_ID = q.ACTION_ID 
                LEFT JOIN TBL_JOB j on j.ORDER_NO = o.ORDER_NO
                LEFT JOIN TBL_JOB_TYPE jt ON j.JOB_TYPE_ID = jt.JOB_TYPE_ID
                WHERE o.ORDER_NO = $order_no;";
                $query = $this->db->query($sql);
                $res = $query->getResultArray();
            }
            else {
                $sql = "SELECT V.ORDER_NO FROM TBL_ORDER O
                INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID
                WHERE O.ORDER_NO = $order_no";
                $result = $this->db->query($sql)->getResultArray();

                $sql = "SELECT V.*, O.*, Q.*, S.STORE_ID, S.STORE_NAME FROM TBL_ORDER O        
                INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID          
                INNER JOIN (SELECT Q.*, O.ORDER_NO FROM TBL_ORDER O
                    INNER JOIN TBL_QUOTE Q ON Q.QUOTE_ID = O.QUOTE_ID         
                    WHERE (O.ORDER_NO = '".$result[0]['ORDER_NO']."')) Q ON Q.ORDER_NO = Q.ORDER_NO
                INNER JOIN TBL_CONTRACTOR C ON C.CONTRACTOR_ID = Q.CONTRACTOR_ID 
                INNER JOIN TBL_STORE S ON S.STORE_ID = Q.STORE_ID
                WHERE (O.ORDER_NO = $order_no);";
                $query = $this->db->query($sql);
                $res = $query->getResultArray();
            }
            
            echo json_encode($res);
        }

        if($rpt_type == 'order_get_quote_stock'){
            $order_no = $this->db->escape(trim($this->request->getPost('order_no')));

            $sql = "SELECT * FROM TBL_ORDER WHERE ORDER_NO = $order_no";

            $result = $this->db->query($sql)->getResultArray();

            if ($result[0]['QUOTE_ID'] != null) {
                $sql = " SELECT s.*,qs.STOCK_CATEGORY,qs.QUANTITY,qs.HUB_ID,s.AVG_COST,s.MARKUP,s.METRIC_ID
                    FROM TBL_QUOTE q
                    INNER JOIN TBL_QUOTE_STOCK qs ON qs.QUOTE_ID = q.QUOTE_ID
                    INNER JOIN TBL_STOCK s ON s.EBQ_CODE = qs.EBQ_CODE
                    INNER JOIN TBL_ORDER o ON o.QUOTE_ID = q.QUOTE_ID
                    WHERE (o.ORDER_NO = $order_no);";
                $query = $this->db->query($sql);
                $quote_stock = $query->getResultArray();
                
                // Categorise vocs into their distinct voc
                $stock_merged = array("QUOTE"=>$quote_stock);

            }
            else {
                $sql = "SELECT V.ORDER_NO FROM TBL_ORDER O
                INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID
                WHERE O.ORDER_NO = $order_no";
                $result = $this->db->query($sql)->getResultArray();

                $original_order_no = $result[0]['ORDER_NO'];
            
                $sql = "SELECT DISTINCT VOC_ID FROM TBL_VOC WHERE ORDER_NO='$original_order_no'";
                $result = $this->db->query($sql);

                foreach ($result->getResult('array') as $voc): 
                { 
                    $id = $voc['VOC_ID'];
                    //VOC
                    $sql = "SELECT s.*,vs.STOCK_CATEGORY,vs.VOC_STOCK_QUANTITY AS QUANTITY,s.AVG_COST,s.MARKUP,s.METRIC_ID,v.HUB_ID
                    FROM TBL_VOC v
                    INNER JOIN TBL_VOC_STOCK vs ON vs.VOC_ID = v.VOC_ID
                    INNER JOIN TBL_STOCK s ON s.EBQ_CODE = vs.STOCK_EBQ
                    INNER JOIN TBL_ORDER o ON o.ORDER_NO = v.ORDER_NO
                    WHERE (o.ORDER_NO = '$original_order_no') AND (v.VOC_ID=$id);";

                    $query = $this->db->query($sql);
                    $voc_stock = $query->getResultArray();
                    $stock_merged["VOC-".$id] = $voc_stock;

                }
                endforeach;

            }

            echo json_encode($stock_merged);
        }

        
    }

}
    