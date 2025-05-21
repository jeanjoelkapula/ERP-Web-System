<?php namespace App\Controllers\Purchase;

use \App\Controllers\BaseController;

class Preview extends BaseController {

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
        echo view('purchase/purchase_order_print_preview', $data);
        
    }
    
    
}
    