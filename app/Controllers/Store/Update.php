<?php namespace App\Controllers\Store;

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
        
        //setting action type and url for store create/update form
        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/store/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";
                
        if ((!isset($entity_id)) || ($entity_id <= 0)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        }
        else 
        {
            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_STORE  WHERE STORE_ID = $entity_id;";
            $result = $this->db->query($sqlQuery);
            $found = false;

            foreach ($result->getResult('array') as $row): 
            { 
                if ($row['COUNT'] > 0) {
                    $found = true;
                }
            }
            endforeach;

            if ($found) {
                $sqlQuery = "SELECT STORE_ID, STORE_TYPE_ID, AREA_ID, ST_X(LOCATION) AS LAT, ST_Y(LOCATION) AS LNG, CONTACT_NUMBER, STORE_NAME, FF_CODE, OPENING_DATE, 
                MAINTENANCE_MONTH, IN_CENTER, TRADING_SIZE, BRANCH_SIZE, HUB_ID, STORE_MANAGER, IS_OPEN FROM TBL_STORE WHERE STORE_ID = $entity_id";

                $objectUpdateStore = $this->db->query($sqlQuery);

                foreach ($objectUpdateStore->getResult('array') as $row): 
                {                       
                    $data['store_id'] = $row['STORE_ID']; 
                    $data['storename'] = $row['STORE_NAME'];            
                    $data['storetype'] = $row['STORE_TYPE_ID'];
                    $data['areaid'] = $row['AREA_ID'];
                    $data['latitude'] = $row['LAT'];
                    $data['longitude'] = $row['LNG'];
                    $data['contact'] = $row['CONTACT_NUMBER'];
                    $data['ffcode'] = $row['FF_CODE'];
                    $data['openingdate'] = $row['OPENING_DATE'];
                    $data['maintenancedate'] = $row['MAINTENANCE_MONTH'];
                    $data['iscenter'] = $row['IN_CENTER'];
                    $data['tradingsize'] = $row['TRADING_SIZE'];
                    $data['branchsize'] = $row['BRANCH_SIZE'];
                    $data['hubid'] = $row['HUB_ID'];
                    $data['storemanager'] = $row['STORE_MANAGER'];
                    $data['isopen'] = $row['IS_OPEN'];       
                }
                endforeach;

                $sqlQuery = "SELECT CONTRACTOR_ID FROM TBL_PREFERRED_CONTRACTOR WHERE STORE_ID = $entity_id";

                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row): 
                { 
                    $data['contractorid'] = $row['CONTRACTOR_ID'];
                }
                endforeach;
            }
            else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        if ($this->request->getPost('form_update_store') == "true") {

            $storeid = $this->request->getPost('store_id');
            $storeName = $this->db->escape(trim($this->request->getPost('storename')));
            $storeType = $this->db->escape(trim($this->request->getPost('storedesc')));
            
            $contact = $this->db->escape(trim($this->request->getPost('contact')));
            $latitude = $this->db->escape(replaceDirectionWithValue(trim($this->request->getPost('latitude'))));
            $longitude  = $this->db->escape(replaceDirectionWithValue(trim($this->request->getPost('longitude'))));
            
            $ffcode = $this->db->escape(trim($this->request->getPost('ffcode')));
            $openingDate = $this->db->escape(trim($this->request->getPost('openingdate')));
            $maintenanceDate = $this->db->escape(trim($this->request->getPost('maintenancedate')));            
            $branchSize = $this->db->escape(trim($this->request->getPost('branchsize')));
            $tradingSize = $this->db->escape(trim($this->request->getPost('tradingsize')));
            $hub = $this->db->escape(trim($this->request->getPost('hub')));
            $area = $this->db->escape(trim($this->request->getPost('area')));            
            $preferredContractor = $this->db->escape(trim($this->request->getPost('prefcontractor')));
            $storemanager = $this->db->escape(trim($this->request->getPost('storemanager')));

            $data['store_id'] = $this->request->getPost('store_id');    
            $data['storename'] = $this->request->getPost('storename');    
            $data['storedesc'] = $this->request->getPost('storedesc');    
            $data['contact'] = $this->request->getPost('contact');    
            $data['latitude'] = $this->request->getPost('latitude');    
            $data['longitude'] = $this->request->getPost('longitude');    
            $data['ffcode'] = $this->request->getPost('ffcode');    
            $data['openingdate'] = $this->request->getPost('openingdate');    
            $data['maintenancedate'] = $this->request->getPost('maintenancedate');    
            $data['branchsize'] = $this->request->getPost('branchsize');    
            $data['tradingsize'] = $this->request->getPost('tradingsize');    
            $data['hub'] = $this->request->getPost('hub');    
            $data['area'] = $this->request->getPost('area');    
            $data['prefcontractor'] = $this->request->getPost('prefcontractor');    
            $data['storemanager'] = $this->request->getPost('storemanager');    

            if ($this->request->getPost('iscenter') == 'on') {
                $isCenter = 1;
            }
            else {
                $isCenter = 0;
            }

            if ($this->request->getPost('isopen') == 'on') {
                $isOpen = 1;
            }
            else {
                $isOpen = 0;
            }

                      // boolean variables for indicating whether the store id or store description already exist in the database
                      $found = false;            
                      $data['found'] = false;            
          
                      // determine if the entered store ID already exists
                      $sqlStoreIDExists = "SELECT COUNT(*) AS COUNT FROM TBL_STORE WHERE STORE_ID = $storeid;";
                      $storeResult = $this->db->query($sqlStoreIDExists);
          
                    //   print_r($storeid);

                    //   print_r("------------------------");

                    //   print_r($sqlStoreIDExists);
          
                    //   die(); 
                      
                      foreach ($storeResult->getResult('array') as $row) : {
                          if ($row['COUNT'] > 0 && $storeid!=$entity_id) {
                              $found = true;
                              $data['found'] = true;
                          }
                      }
                      endforeach;
          
                      // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
          
                      // if the store ID is not found in the database, the rest of the form can be processed
                      if (!$found) {

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            // alter tables drop fk
            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

 

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            $sqlUpdateTblHubStock = "UPDATE TBL_PREFERRED_CONTRACTOR SET STORE_ID = $storeid, CONTRACTOR_ID = $preferredContractor WHERE STORE_ID = $entity_id;";

            $this->db->query($sqlUpdateTblHubStock);

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

      

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            $sqlUpdateTblGRN = "UPDATE TBL_QUOTE SET STORE_ID = $storeid WHERE STORE_ID = $entity_id;";

            $this->db->query($sqlUpdateTblGRN);

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

            $sqlupdate = "UPDATE TBL_STORE SET STORE_ID = $storeid,STORE_TYPE_ID = $storeType, LOCATION = POINT($latitude, $longitude), CONTACT_NUMBER = $contact, STORE_NAME = $storeName, FF_CODE = $ffcode, OPENING_DATE = $openingDate, 
            MAINTENANCE_MONTH = $maintenanceDate, IN_CENTER = $isCenter, TRADING_SIZE = $tradingSize, BRANCH_SIZE = $branchSize, HUB_ID = $hub, STORE_MANAGER = $storemanager, IS_OPEN = $isOpen WHERE STORE_ID = $entity_id;";
            
            $this->db->query($sqlupdate);

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            // alter the tables, add the FKs back to the tables
            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------       

            return redirect()->to('/store/search');
            exit();

        }
    }
    
        echo view('store/update',$data);
        
    }

}
    