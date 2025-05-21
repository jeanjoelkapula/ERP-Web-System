<?php namespace App\Controllers\Store;

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
        
        //setting action type and url for store create/update form
        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/store/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";

        // populate the select with store types
        $storeTypeQuery = "SELECT STORE_TYPE_DESCRIPTION FROM TBL_STORE_TYPE;";

        $object = $this->db->query($storeTypeQuery);

        $data['object']=$object;

        // populate the select with hub names        
        $hubQuery = "SELECT HUB_NAME FROM TBL_HUB;";

        $objectHub = $this->db->query($hubQuery);

        $data['objectHub']=$objectHub;

        // populate the select with area names        
        $areaQuery = "SELECT AREA_NAME FROM TBL_AREA;";

        $objectArea = $this->db->query($areaQuery);

        $data['objectArea']=$objectArea;

        // populate the select with preferred contractor names        
        $contractorQuery = "SELECT CONTRACTOR_NAME FROM TBL_CONTRACTOR WHERE IN_BUSINESS=1;";

        $objectContractor = $this->db->query($contractorQuery);

        $data['objectContractor']=$objectContractor;

        // populate the select with ff codes                
        $ffcQuery = "SELECT FF_CODE FROM TBL_STORE;";

        $objectFFC = $this->db->query($ffcQuery);

        $data['objectFFC']=$objectFFC;

        if ($this->request->getPost('form_create_store') == "true") { 

            $storeID = $this->request->getPost('store_id');
            $storeName = $this->db->escape(trim($this->request->getPost('storename')));
            $storeType = $this->db->escape(trim($this->request->getPost('storedesc')));        
            $contact = $this->db->escape(trim($this->request->getPost('contact')));
            $latitude = $this->db->escape(trim(replaceDirectionWithValue($this->request->getPost('latitude'))));
            $longitude  = $this->db->escape(trim(replaceDirectionWithValue($this->request->getPost('longitude'))));
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
            $sqlStoreIDExists = "SELECT COUNT(*) AS COUNT FROM TBL_STORE WHERE STORE_ID = $storeID;";
            $storeResult = $this->db->query($sqlStoreIDExists);

            // print_r($sqlStoreIDExists);

            // die(); 
            
            foreach ($storeResult->getResult('array') as $row) : {
                if ($row['COUNT'] > 0) {
                    $found = true;
                    $data['found'] = true;
                }
            }
            endforeach;

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

            // if the store ID is not found in the database, the rest of the form can be processed
            if (!$found) {


            // get the hub ID
            $hubIDQuery = "SELECT HUB_ID FROM TBL_HUB WHERE HUB_NAME = $hub;";

            $hubID = $this->db->query($hubIDQuery);

            foreach ($hubID->getResult('array') as $row): 
            {         
                $hubIDString = $row['HUB_ID'];
            } 
            endforeach;


            // get the store type id        
            $storeTypeIDQuery = "SELECT STORE_TYPE_ID FROM TBL_STORE_TYPE WHERE STORE_TYPE_DESCRIPTION = $storeType;";

            $storeTypeID = $this->db->query($storeTypeIDQuery);            

            foreach ($storeTypeID->getResult('array') as $row): 
            {         
                $storeTypeIDString = $row['STORE_TYPE_ID'];
            } 
            endforeach;

            // get the area id            
            $areaIDQuery = "SELECT AREA_NO FROM TBL_AREA WHERE AREA_NAME = $area;";

            $areaID = $this->db->query($areaIDQuery);

            foreach ($areaID->getResult('array') as $row): 
            {         
                $areaIDString = $row['AREA_NO'];
            } 
            endforeach;

            $sql = "INSERT INTO TBL_STORE
            (STORE_ID,STORE_TYPE_ID,AREA_ID,LOCATION,CONTACT_NUMBER,STORE_NAME,FF_CODE,
            OPENING_DATE,MAINTENANCE_MONTH,IN_CENTER,TRADING_SIZE,BRANCH_SIZE,HUB_ID,STORE_MANAGER,IS_OPEN)
            VALUES
            ($storeID,
            $storeTypeIDString,
            $areaIDString,
            POINT($latitude,$longitude),
            $contact,
            $storeName,
            $ffcode,
            $openingDate,
            $maintenanceDate,
            $isCenter,
            $tradingSize,
            $branchSize,
            $hubIDString,
            $storemanager,
            $isOpen);";

            $this->db->query($sql);   
            
            // get the newly assigned store id
            $newStoreID = "SELECT STORE_ID FROM TBL_STORE WHERE STORE_NAME = $storeName";

            $newID = $this->db->query($newStoreID);

            foreach ($newID->getResult('array') as $row): 
            {         
                $newIDString = $row['STORE_ID'];
            } 
            endforeach;



            // get the contractor_id of the preferred contractor
            $sqlGetContrID = "SELECT CONTRACTOR_ID FROM TBL_CONTRACTOR WHERE CONTRACTOR_NAME = $preferredContractor";

            $contrID = $this->db->query($sqlGetContrID);

            foreach ($contrID->getResult('array') as $row): 
            {         
                $preferredContractorID = $row['CONTRACTOR_ID'];
            } 
            endforeach;

            // insert the preferred contractor
            $sqlPrefContr = "INSERT INTO TBL_PREFERRED_CONTRACTOR
            (STORE_ID,
            CONTRACTOR_ID)
            VALUES
            ($newIDString,
            $preferredContractorID);";

            $this->db->query($sqlPrefContr);

            return redirect()->to('/store/search');
            exit();      
        }                 
        }               
        echo view('store/create',$data);
        
    }
}
    