<?php namespace App\Controllers\Purchase;

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/purchase/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";
        $data['form_enable'] = "true";
        $sql = "SELECT S.EBQ_CODE, S.DESCRIPTION, M.METRIC_DESCRIPTION FROM TBL_STOCK S
        INNER JOIN TBL_METRIC M ON S.METRIC_ID = M.METRIC_ID WHERE (S.IS_ACTIVE = 1);"; 
        
        $result = $this->db->query($sql);
        $data["stock"] = $result->getResult('array');

        if ($this->request->getPost('form_create') == "true") {
            $purchase_order_no = $this->db->escape(trim($this->request->getPost('purchase_order_no')));
            $order_date = $this->db->escape(trim($this->request->getPost('order_date')));
            $date_required = $this->db->escape(trim($this->request->getPost('date_required')));
            $ship_via = $this->db->escape(trim($this->request->getPost('ship_via')));
            $vendor_name = $this->db->escape(trim($this->request->getPost('vendor_name')));
            $vendor_address= $this->request->getPost('vendor_address');
            $vendor_po_box = $this->request->getPost('vendor_po_box');
            $vendor_zip_code = $this->request->getPost('vendor_zip_code');
            $misc_charges = $this->db->escape(trim($this->request->getPost('misc_charges')));
            $freigh_charges = $this->db->escape(trim($this->request->getPost('freight_charges')));
            $po_amount = $this->db->escape(trim($this->request->getPost('po_amount')));
            $order_stock = $this->request->getPost('stock');
            $user_id = $data['_user_id'];

            //build sql query and add purchase order
            $sql = "INSERT INTO TBL_PURCHASE_ORDER(PURCHASE_ORDER_ID, ORDER_DATE, DATE_REQUIRED, SHIP_VIA, VENDOR_NAME";
            if (!empty($vendor_address)) {
                $sql .= ",VENDOR_ADDRESS";
            }

            if (!empty($vendor_po_box)) {
                $sql .= ",VENDOR_PO_BOX";
            }

            if (!empty($vendor_zip_code)) {
                $sql .= ",VENDOR_ZIP_CODE";
            }

            $sql .= ",MISC_CHARGES, FREIGHT_CHARGES, TOTAL, FUL_FILLED, USER_ID) VALUES ($purchase_order_no,$order_date, $date_required, $ship_via, $vendor_name";
            
            if (!empty($vendor_address) || $vendor_address != '') {
                $sql .= ",".$this->db->escape($vendor_address);
            }

            if (!empty($vendor_po_box) || $vendor_po_box != '') {
                $sql .= ",".$this->db->escape($vendor_po_box);
            }

            if (!empty($vendor_zip_code) || $vendor_zip_code != '') {
                $sql .= ",".$this->db->escape($vendor_zip_code);
            }

            $sql .= ", $misc_charges, $freigh_charges, $po_amount, 0, $user_id);";
            $this->db->query($sql); 

            //add purchase order stock items
            foreach (array_keys($order_stock)  as $ebq) {
                $quantity = $order_stock[$ebq]['quantity'];
                $price = $order_stock[$ebq]["price"];
                $amount = $order_stock[$ebq]['amount'];

                $order_stock_insert = "INSERT INTO TBL_PURCHASE_ORDER_STOCK VALUES
                ($purchase_order_no,'$ebq',$quantity,$price,$amount, 0);";
                $this->db->query($order_stock_insert);
            };

            return redirect()->to('/purchase/search/');
            exit();
        }
        
       
        echo view('purchase/create',$data);
        
    }    

    public function ordercheck() {
        if(!($ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }
        if ($this->request->getMethod() == 'post') {
            if (!empty($this->request->getPost('purchase_order_no'))) {
                $entity_id = $this->request->getPost('purchase_order_no');
                $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_PURCHASE_ORDER WHERE PURCHASE_ORDER_ID = '$entity_id';";
                $result = $this->db->query($sqlQuery);
                $found = "false";

                foreach ($result->getResult('array') as $row) : {
                    if ($row['COUNT'] > 0) {
                        $found = "true";
                    }
                }
                endforeach;

                if ($found == "true") {
                    echo "true";
                }
                else {
                    echo "false";
                }
            }
        }
    }
    
}
    