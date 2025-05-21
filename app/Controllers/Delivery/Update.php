<?php namespace App\Controllers\Delivery;

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
    

    public function index($entity_id = 0)
    {
        if(!($this->ionAuth->inGroup('electrical_manager') && $this->ionAuth->inGroup('stock_controller')  && $this->ionAuth->inGroup('admin'))  ){
            return redirect()->to('/noAccess');
            exit();
        }

        //setting action type and url for contractor create/update form
        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/delivery/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";        
        $data['entity_id'] = $entity_id;
        $data['action_type'] = "update";
                
        if (!isset($entity_id)  || $entity_id <= 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else 
        {
            $sqlQueryCount = "SELECT COUNT(*) AS COUNT FROM TBL_DELIVERY_NOTE WHERE DELIVERY_ID = $entity_id;";
            $resultCount = $this->db->query($sqlQueryCount);
            $found = false;

            foreach ($resultCount->getResult('array') as $row): 
            { 
                if ($row['COUNT'] > 0) {
                    $found = true;
                }
            }
            endforeach;

            if ($found) 
            {
                $sqlQuery = "SELECT * FROM TBL_DELIVERY_NOTE WHERE DELIVERY_ID = $entity_id";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row): 
                { 
                    $data['maintenancedate'] = $row['DELIVERY_DATE']; 
                    $data['waybill'] = $row['DELIVERY_WAYBILL']; 
                    $data['notes'] = $row['NOTES']; 
                    $data['packingbill'] = $row['PACKING_BILL_ID']; 
                    $data['is_signed'] = $row['IS_SIGNED_OFF']; 
                    $data['deliverymethod'] = $row['DELIVERY_METHOD']; 
                }
                endforeach;    
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }              
        }

        if ($this->request->getPost('form_update_delivery') == "true") {
        
            // get the order chosen
            $pbNumber = $this->request->getPost('create-delivery');

            $dDate = $this->db->escape(trim($this->request->getPost('maintenancedate')));
            $dWaybill = $this->db->escape(trim($this->request->getPost('waybill_number')));
            $notes = $this->db->escape(trim($this->request->getPost('notes')));
            $deliverymethod = $this->db->escape(trim($this->request->getPost('deliverymethod')));
                
            if ($this->request->getPost('is_signed') == 'on') {
                $isSigned = 1;
            }
            else {
                $isSigned = 0;
            }

            $sql = "UPDATE TBL_DELIVERY_NOTE 
            SET 
            DELIVERY_DATE = $dDate,
            DELIVERY_WAYBILL = $dWaybill,
            PRICE = 0,
            NOTES = $notes,
            PACKING_BILL_ID = $pbNumber,
            IS_SIGNED_OFF = $isSigned,
            DELIVERY_METHOD = $deliverymethod
            WHERE 
            DELIVERY_ID = $entity_id;";

            $this->db->query($sql);

            return redirect()->to('/delivery/search');
            exit();

        }
    
        echo view('delivery/update',$data);
        
    }

    public function sign($entity_id=0){
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        if (!isset($entity_id)  || $entity_id <= 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            $sql = "UPDATE TBL_DELIVERY_NOTE 
            SET 
            IS_SIGNED_OFF = 1
            WHERE 
            DELIVERY_ID = $entity_id;";

            $this->db->query($sql);

            $sql = "SELECT PB.* FROM TBL_DELIVERY_NOTE D
                INNER JOIN TBL_PACKING_BILL PB ON PB.PACKING_BILL_ID = D.PACKING_BILL_ID
                WHERE D.DELIVERY_ID = $entity_id;";

            $result = $this->db->query($sql)->getResultArray()[0];   
            $packing_bill_id = $result['PACKING_BILL_ID'];        
            $source_hub = $result['SOURCE_HUB_ID'];
            $destination_hub = $result['DESTINATION_HUB'];

            $sql = "SELECT * FROM TBL_PACKING_BILL_STOCK
                WHERE PACKING_BILL_ID = $packing_bill_id;";
            $result = $this->db->query($sql);

            foreach ($result->getResult('array') as $row): 
            { 
                
                $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY - ".$row['QUANTITY'].") WHERE HUB_ID = $source_hub AND EBQ_CODE = '".$row['EBQ_CODE']. "';";
                $this->db->query($sql);

                $sql = "SELECT COUNT(*) AS COUNT FROM TBL_HUB_STOCK WHERE HUB_ID = $destination_hub AND EBQ_CODE = '".$row['EBQ_CODE']. "';";
                $r = $this->db->query($sql)->getResultArray()[0];

                if ($r['COUNT'] > 0) {
                    $sql = "UPDATE TBL_HUB_STOCK SET QUANTITY = (QUANTITY + ".$row['QUANTITY'].") WHERE HUB_ID = $destination_hub AND EBQ_CODE = '".$row['EBQ_CODE']. "';";
                    
                }
                else {
                    $sql = "INSERT INTO TBL_HUB_STOCK VALUES ($destination_hub, '".$row['EBQ_CODE']."', ".$row['QUANTITY']. ");";
                }
                $this->db->query($sql);
                
            }
            endforeach;
            
            return redirect()->to('/delivery/search');
            exit();
        }
    }
    
}
    