<?php namespace App\Controllers\Purchase;

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
    

    public function index($entity_id = '')
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/purchase/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";

        //check existence of entity id
        if (!isset($entity_id) || empty($entity_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_PURCHASE_ORDER WHERE PURCHASE_ORDER_ID = '$entity_id';";
            $result = $this->db->query($sqlQuery);
            $found = false;

            foreach ($result->getResult('array') as $row) : {
                if ($row['COUNT'] > 0) {
                    $found = true;
                }
            }
            endforeach;

            if ($found) {
                $sql = "SELECT S.EBQ_CODE, S.DESCRIPTION, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                INNER JOIN TBL_METRIC M ON S.METRIC_ID = M.METRIC_ID WHERE (S.IS_ACTIVE = 1);"; 
                $result = $this->db->query($sql);
                $data["stock"] = $result->getResult('array');
                $data['purchase_order_id'] = $entity_id;
                $sqlQuery = "SELECT * FROM TBL_PURCHASE_ORDER  WHERE PURCHASE_ORDER_ID = '$entity_id';";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row) : {
                    $data['purchase_order_no'] = $row['PURCHASE_ORDER_ID'];
                    $data['order_date'] = $row['ORDER_DATE'];
                    $data['date_required'] = $row['DATE_REQUIRED'];
                    $data['ship_via'] = $row['SHIP_VIA'];
                    $data['vendor_name'] = $row['VENDOR_NAME'];
                    $data['vendor_address'] = $row['VENDOR_ADDRESS'];
                    $data['vendor_po_box'] = $row['VENDOR_PO_BOX'];
                    $data['vendor_zip_code'] = $row['VENDOR_ZIP_CODE'];
                    $data['misc_charges'] = $row['MISC_CHARGES'];
                    $data['freight_charges'] = $row['FREIGHT_CHARGES'];
                    $data['po_amount'] = $row['TOTAL'];
                    $data['ful_filled'] = $row['FUL_FILLED'];
                    $data['approval_status'] = $row['APPROVAL_STATUS'];
                    $data['approved'] = $row['APPROVAL_STATUS'] ==='APPROVED';
                    $data['declined'] = $row['APPROVAL_STATUS'] ==='DECLINED';
                }
                endforeach;

                $sqlQuery = "SELECT PS.PURCHASE_ORDER_ID, PS.EBQ_CODE, S.DESCRIPTION, S.METRIC_DESCRIPTION, PS.QUANTITY, PS.UNIT_PRICE, PS.AMOUNT, PS.APPROVED FROM TBL_PURCHASE_ORDER_STOCK PS 
                    INNER JOIN 
                        (SELECT S.*, M.METRIC_DESCRIPTION FROM TBL_STOCK S
                            INNER JOIN TBL_METRIC M ON M.METRIC_ID = S.METRIC_ID) S ON PS.EBQ_CODE = S.EBQ_CODE
                    WHERE (PS.PURCHASE_ORDER_ID = '$entity_id')";

                $result = $this->db->query($sqlQuery);
                $data['order_stock'] = $result->getResult('array');
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit();
            }
        }

        if ($this->request->getPost('form_update') == "true") {
            $purchase_order_id =  $this->db->escape(trim($this->request->getPost('purchase_order_no')));
            $order_date = $this->db->escape(trim($this->request->getPost('order_date')));
            $date_required = $this->db->escape(trim($this->request->getPost('date_required')));
            $ship_via = $this->db->escape(trim($this->request->getPost('ship_via')));
            $vendor_name = $this->db->escape(trim($this->request->getPost('vendor_name')));
            $vendor_address= $this->db->escape(trim($this->request->getPost('vendor_address')));
            $vendor_po_box = $this->db->escape(trim($this->request->getPost('vendor_po_box')));
            $vendor_zip_code = $this->db->escape(trim($this->request->getPost('vendor_zip_code')));
            $misc_charges = $this->db->escape(trim($this->request->getPost('misc_charges')));
            $freigh_charges = $this->db->escape(trim($this->request->getPost('freight_charges')));
            $po_amount = $this->db->escape(trim($this->request->getPost('po_amount')));
            $order_stock = $this->request->getPost('stock');
            $user_id = $data['_user_id'];

            $sqlQuery = "SELECT * FROM TBL_PURCHASE_ORDER WHERE PURCHASE_ORDER_ID = $purchase_order_id;";
            $result = $this->db->query($sqlQuery);
            if ($result->getResult('array')[0]['FUL_FILLED'] != 1){
                //clear purchase order stock items
                $sql = "DELETE FROM TBL_PURCHASE_ORDER_STOCK WHERE PURCHASE_ORDER_ID = $purchase_order_id;";
                $this->db->query($sql);

                //build sql query and update order details
                $sql = "UPDATE TBL_PURCHASE_ORDER SET ORDER_DATE = $order_date, DATE_REQUIRED = $date_required, SHIP_VIA = $ship_via, VENDOR_NAME= $vendor_name";

                if (isset($vendor_address)) {
                    $sql .= ",VENDOR_ADDRESS = $vendor_address";
                }

                if (isset($vendor_po_box)) {
                    $sql .= ",VENDOR_PO_BOX = $vendor_po_box";
                }

                if (isset($vendor_zip_code)) {
                    $sql .= ",VENDOR_ZIP_CODE = $vendor_zip_code";
                }

                $sql .= ",MISC_CHARGES =  $misc_charges, FREIGHT_CHARGES = $freigh_charges, TOTAL = $po_amount, FUL_FILLED = 0, USER_ID = $user_id WHERE (PURCHASE_ORDER_ID = $purchase_order_id);";
                echo $sql;

                $this->db->query($sql); 

                //add purchase order stock items
                foreach (array_keys($order_stock)  as $ebq) {
                    $quantity = $order_stock[$ebq]['quantity'];
                    $price = $order_stock[$ebq]["price"];
                    $amount = $order_stock[$ebq]['amount'];

                    $order_stock_insert = "INSERT INTO TBL_PURCHASE_ORDER_STOCK VALUES
                    ($purchase_order_id,'$ebq',$quantity,$price,$amount, 0);";
                    $this->db->query($order_stock_insert);
                };
            }

            return redirect()->to('/purchase/search/');
            exit();
        }  

        //approve order
        if ($this->request->getPost('form_approve_purchase') == "true") {
            $sql = "UPDATE TBL_PURCHASE_ORDER SET APPROVAL_STATUS = 'APPROVED' WHERE PURCHASE_ORDER_ID = '$entity_id';";
            $this->db->query($sql);

            return redirect()->to('/purchase/search');
            exit();

        }

        //Decline order
        if ($this->request->getPost('form_decline_purchase') == "true") {
            $sql = "SELECT FUL_FILLED FROM TBL_PURCHASE_ORDER WHERE PURCHASE_ORDER_ID = '$entity_id';";
            $result = $this->db->query($sql);

            if ($result->getResult('array')[0]['FUL_FILLED']==0) {
                $sql = "UPDATE TBL_PURCHASE_ORDER SET APPROVAL_STATUS = 'DECLINED' WHERE PURCHASE_ORDER_ID = '$entity_id';";
                $this->db->query($sql);
            }

            return redirect()->to('/purchase/search');
            exit();

        }
       
        echo view('purchase/update',$data);
        
    } 

    public function form_approve_purchase($entity_id){
        if(!$this->ionAuth->isAdmin($this->ionAuth->user()->row()->id) &&  !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();
        }

        $sql = "UPDATE TBL_PURCHASE_ORDER SET APPROVAL_STATUS = 'APPROVED' WHERE PURCHASE_ORDER_ID = '$entity_id';";
        $query = $this->db->query($sql);

        if($query !== false){
            $res = 'ok';
        } else {
            $res = 'error';
        }

        echo json_encode($res);
    }

    public function form_decline_purchase($entity_id){
        if(!$this->ionAuth->isAdmin($this->ionAuth->user()->row()->id) &&  !$this->ionAuth->inGroup('electrical_manager')){
            return redirect()->to('/noAccess');
            exit();
        }

        $sql = "SELECT FUL_FILLED FROM TBL_PURCHASE_ORDER WHERE PURCHASE_ORDER_ID = '$entity_id';";
        $result = $this->db->query($sql);

        if ($result->getResult('array')[0]['FUL_FILLED']==0) {
            $sql = "UPDATE TBL_PURCHASE_ORDER SET APPROVAL_STATUS = 'DECLINED' WHERE PURCHASE_ORDER_ID = '$entity_id';";
            $query = $this->db->query($sql);

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
    
}
    