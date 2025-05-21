<?php namespace App\Controllers\Packing;

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/packing/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";

        if (!isset($entity_id) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else {
            // Check if the quote exists in TBL_QUOTE
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_PACKING_BILL  WHERE PACKING_BILL_ID = '$entity_id';";
            $result = $this->db->query($sqlQuery);
            $packing_found = false;

            foreach ($result->getResult('array') as $row): 
            { 
                if ($row['COUNT'] > 0) {
                    $packing_found = true;
                }
            }
            endforeach;

            // Check if the quote exists in TBL_QUOTE_STOCK
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_PACKING_BILL_STOCK  WHERE PACKING_BILL_ID = '$entity_id';";
            $result = $this->db->query($sqlQuery);
            $stock_found = false;

            foreach ($result->getResult('array') as $row): 
                { 
                    if ($row['COUNT'] > 0) {
                        $stock_found = true;
                    }
                }
                endforeach;



            if ($packing_found) {
                $sql = "SELECT * FROM TBL_PACKING_BILL bill WHERE PACKING_BILL_ID = $entity_id;";
                $result = $this->db->query($sql);

                // Return Quote information
                foreach ($result->getResult('array') as $row): 
                { 
                    $data['packing_bill_id'] = $row['PACKING_BILL_ID']; 
                    if ($row['ORDER_NO'] != null) {
                        $data['order_no'] = $row['ORDER_NO']; 
                    }
                    else {
                        $data['order_no'] = $row['INTERNAL_ORDER_NO']; 
                    }
                    $data['delivery_date'] = $row['DELIVERY_DATE']; 
                    $data['pack_date'] = $row['PACK_DATE']; 
                    $data['created_date'] = $row['CREATED_DATE']; 
                    $data['ship_via'] = $row['SHIP_VIA'];
                    $data['destination_hub'] = $row['DESTINATION_HUB'];
                    $data['source_hub'] = $row['SOURCE_HUB_ID'];
                    $data['status'] = $row['STATUS'];
                    $data['deliver_to_site'] = $row['DELIVER_TO_SITE'];
                }
                endforeach;
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            if($stock_found){
                $sql = "SELECT pbs.* FROM TBL_PACKING_BILL_STOCK pbs 
                WHERE PACKING_BILL_ID = '$entity_id';";
                $result = $this->db->query($sql);

                // Return Stock information
                $selected_stock = array();
                foreach ($result->getResult() as $row): 
                { 
                   array_push($selected_stock,$row);
                }
                endforeach;
                $data['selected_stock'] = $selected_stock;

            }
        }
        // Approve Packing Bill
        if ($this->request->getPost('form_approve_packing') == "true") {
            if($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('admin')){
                $sql = "UPDATE TBL_PACKING_BILL SET status = 'APPROVED' WHERE PACKING_BILL_ID = $entity_id;";
                $query = $this->db->query($sql);

                if($query !== false){
                    $res = 'ok';
                } else {
                    $res = 'error';
                }

                echo json_encode($res);
                exit();
            }
            else{
                return redirect()->to('/noAccess');
                exit();
            }

        }
        //Decline Packing Bill
        if ($this->request->getPost('form_decline_packing') == "true") {
            if($this->ionAuth->inGroup('electrical_manager') || $this->ionAuth->inGroup('admin')){
                $sql = "UPDATE TBL_PACKING_BILL SET status = 'DECLINED' WHERE PACKING_BILL_ID = $entity_id;";
                $query = $this->db->query($sql);

                if($query !== false){
                    $res = 'ok';
                } else {
                    $res = 'error';
                }

                echo json_encode($res);
                exit();
            }
            else{
                return redirect()->to('/noAccess');
                exit();

            }

        }
      
        // Edit Packing Bill
        if ($this->request->getPost('form_update_packing') == "true") {
            $order_no =  $this->db->escape(trim($this->request->getPost('order_no')));
            $delivery_date = $this->request->getPost('delivery_date');
            $pack_date = $this->request->getPost('packing_date');
            $ship_via = trim($this->request->getPost('ship_via'));
            $source_hub = $this->request->getPost('source_hub_id');
            $destination_hub = $this->request->getPost('destination_hub');
            $site_delivery = $this->request->getPost('site-delivery');

            if($site_delivery === 'on'){
                $site_delivery = 1;
                $destination_hub = 'NULL';
            }
            else{
                $site_delivery = 0;
            }

            $sql = "UPDATE TBL_PACKING_BILL SET DELIVERY_DATE = ?, PACK_DATE=?, SHIP_VIA = ?,SOURCE_HUB_ID=?,DESTINATION_HUB=? WHERE (PACKING_BILL_ID = ?);";

            $this->db->query($sql, [
                $delivery_date, $pack_date, $ship_via,$source_hub,$destination_hub,$entity_id
            ]);

            return redirect()->to('/packing/search');
            exit();
   
        }
                
        echo view('packing/update',$data);

}
    
}
    