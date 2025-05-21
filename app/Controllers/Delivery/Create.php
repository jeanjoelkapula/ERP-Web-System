<?php namespace App\Controllers\Delivery;

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

        //setting action type and url for contractor create/update form
        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/delivery/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";
        
        // get the order numbers
        $sqlOrderNumbers = "SELECT CONCAT(ORDER_NO,': ',DATE(ORDER_DATE_CREATED)) AS ORDER_DISPLAY FROM TBL_ORDER;";

        $orderNumbersResult = $this->db->query($sqlOrderNumbers);

        $data['order_numbers'] = $orderNumbersResult;
                
        if ($this->request->getPost('form_create_delivery') == "true") {

            // get the order chosen
            $orderNumber = $this->request->getPost('create-delivery');

            // get the packing bill id
            $sqlPackingBillID = "SELECT PACKING_BILL_ID FROM TBL_PACKING_BILL WHERE PACKING_BILL_ID = $orderNumber;";

            $PBResult = $this->db->query($sqlPackingBillID);

            // declare a packing Bill ID variable
            $packingBillID = '';
            
            // get the packing bill id
            foreach ($PBResult->getResult('array') as $row) : {
                $data['packing_bill_id'] = $row['PACKING_BILL_ID']; 
                $packingBillID = $row['PACKING_BILL_ID'];          
            }
            endforeach;
        
            $dDate = $this->db->escape(trim($this->request->getPost('maintenancedate')));
            $dWaybill = $this->db->escape(trim($this->request->getPost('waybill_number')));
            $notes = $this->db->escape(trim($this->request->getPost('notes')));
            $deliverymethod = $this->db->escape(trim($this->request->getPost('deliverymethod')));
        
            $sql = "INSERT INTO TBL_DELIVERY_NOTE (DELIVERY_DATE,DELIVERY_WAYBILL,PRICE,NOTES,PACKING_BILL_ID,IS_SIGNED_OFF,DELIVERY_METHOD)
            VALUES
            ($dDate,
            $dWaybill,
            0,
            $notes,
            $packingBillID,
            0,
            $deliverymethod);";
            
            $this->db->query($sql);
            
            return redirect()->to('/delivery/search');
            exit();
        }
    
        echo view('delivery/create',$data);
        
    }
    
    
}
    