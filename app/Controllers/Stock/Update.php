<?php

namespace App\Controllers\Stock;

use \App\Controllers\BaseController;

class Update extends BaseController
{

    public function _remap($method, ...$params)
    {

        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        } else if ($method == "index") {
            $this->index($method);
            exit;
        } else {
            return $this->index($method, ...$params);
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }


    public function index($entity_id = "")
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "update";
        $data["url"] = "/stock/update/update";
        $data["url"] = "/stock/update/$entity_id";
        $data['form_create'] = "false";
        $data['form_update'] = "true";

        if (!isset($entity_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            exit();
        } else {

            $sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK WHERE EBQ_CODE = '$entity_id';";
            $result = $this->db->query($sqlQuery);
            $found = false;

            foreach ($result->getResult('array') as $row) : {
                    if ($row['COUNT'] > 0) {
                        $found = true;
                    }
                }
            endforeach;

            if ($found) {

                // get the data from the stock table
                $sqlQuery = "SELECT * FROM TBL_STOCK WHERE EBQ_CODE = '$entity_id';";
                $result = $this->db->query($sqlQuery);

                foreach ($result->getResult('array') as $row) : {                        
                        $data['ebq_code'] = $row['EBQ_CODE'];
                        $data['stock_description'] = $row['DESCRIPTION'];
                        $data['wastage'] = $row['WASTAGE'];
                        $data['markup'] = $row['MARKUP'];           
                        $data['purchase_cost'] = $row['PURCHASE_COST']; 
                        $data['is_active'] = $row['IS_ACTIVE'];
                        $data['is_built'] = $row['IS_BUILT'];
                        $data['metricdesc'] = $row['METRIC_ID'];
                        $data['minreorder'] = $row['MIN_REORDER'];
                        $data['last_cost'] = $row['LAST_COST'];                        
                    }
                endforeach;

                // get the data from the hub stock table
                $sqlQueryHubStock = "SELECT * FROM TBL_HUB_STOCK WHERE EBQ_CODE = '$entity_id';";
                $resultsqlQueryHubStock = $this->db->query($sqlQueryHubStock);

                foreach ($resultsqlQueryHubStock->getResult('array') as $row) : {
                        
                        $data['hub_id'] = $row['HUB_ID'];
                        $data['quantity'] = $row['QUANTITY'];
                  
                    }
                endforeach;

            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }


        if ($this->request->getPost('form_update_stock') == "true") {
            
            $stock_code = $this->request->getPost('ebq_code');
            $purchase_cost = $this->request->getPost('purchase_cost');
            $description = $this->db->escape(trim($this->request->getPost('stock_description')));
            $minreorder = $this->request->getPost('minreorder');
            $metName = $this->db->escape(trim($this->request->getPost('metricdesc')));
            $markup = $this->request->getPost('markup');
            $wastage = $this->request->getPost('wastage');
         
            $data['hub'] = $this->request->getPost('hubSearch');       

            $data['ebq_code'] = $this->request->getPost('ebq_code');
            $data['purchase_cost'] = $this->request->getPost('purchase_cost');
            $data['stock_description'] = $this->request->getPost('stock_description');
            $data['minreorder'] = $this->request->getPost('minreorder');
            $data['metricdesc'] = $this->request->getPost('metricdesc');
            $data['markup'] = $this->request->getPost('markup');
            $data['wastage'] = $this->request->getPost('wastage');

            $data['found'] = false;   
            $data['foundDescription'] = false;     

            $found = false;       
            $foundDescription = false;

            // determine if the entered EBQ already exists            
            $sqlEBQExists = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK WHERE EBQ_CODE = '$stock_code';";
            $ebqresult = $this->db->query($sqlEBQExists);
            
            // if the new ebq code is different to the existing ebq code but equals an existing record in the database, the user is notified that the existing ebq is already taken
            foreach ($ebqresult->getResult('array') as $row) : {
                if ($row['COUNT'] > 0 && $stock_code!=$entity_id) {
                    $found = true;
                    $data['found'] = true;
                }
            }
            endforeach;


            // determine if the entered description already exists
            $sqlDescriptionExists = "SELECT EBQ_CODE FROM TBL_STOCK WHERE DESCRIPTION = $description;";

            
            $descriptionResult = $this->db->query($sqlDescriptionExists);
            
            foreach ($descriptionResult->getResult('array') as $row) : {
                if ($row['EBQ_CODE'] != $entity_id && $row['EBQ_CODE'] != $stock_code) {
                    $foundDescription = true;
                    $data['foundDescription'] = true;
                }
            }
            endforeach;

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            // if the form submission is valid, ie it has a unique stock code and description, it can be processed
            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            if(!$found && !$foundDescription) 
            {

                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // if the stock item is active
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                if ($this->request->getPost('is_active') == 'on') {
                    $isactive = 1;
                } else {
                    $isactive = 0;
                }

                if ($this->request->getPost('is_built') == 'on') {
                    $isbuilt = 1;
                } else {
                    $isbuilt = 0;
                }      

                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // get metric ID for the entered metric type
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                $sqlMetric = "SELECT METRIC_ID FROM TBL_METRIC WHERE METRIC_DESCRIPTION = $metName;";
                $metricResult = $this->db->query($sqlMetric);
                foreach ($metricResult->getResult('array') as $row) : {
                        $metricID = $row['METRIC_ID'];
                    }
                endforeach;

                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // Calculating average prices
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        
                // CALCULATION FOR AVERAGE STOCK PRICE
                // -------------------------------------------------------
                // this is to calculate the average cost of the item
                // OLD STOCK * AVERAGE = SUBTOTAL
                // NEW STOCK * INCOMING PRICE = SUBTOTAL
                // (SUBTOTAL + SUBTOTAL) / STOCK ITEMS = NEW AVERAGE
                // -------------------------------------------------------

                // get the old quantity of stock
                $oldQuantity = 0;
                $sqlSubtotalOld = "SELECT SUM(QUANTITY) AS QUANTITY FROM TBL_HUB_STOCK WHERE EBQ_CODE = '$entity_id';";

                $oldSubtotalResult = $this->db->query($sqlSubtotalOld);
                foreach ($oldSubtotalResult->getResult('array') as $row) : {
                        
                        if ($row['QUANTITY'] > 0) {
                            $foundOldStock = true;
                            $oldQuantity = $row['QUANTITY'];
                        }
                    }
                endforeach;
                
                // get the old average cost of stock
                $sqlOldAverage = "SELECT AVG_COST FROM TBL_STOCK WHERE EBQ_CODE = '$entity_id';";

                $oldAverageResult = $this->db->query($sqlOldAverage);
                foreach ($oldAverageResult->getResult('array') as $row) : {
                    $oldAverage = $row['AVG_COST'];
                    if ($row['AVG_COST'] > 0) {
                        $foundOldStock = true;
                    }
                }
                endforeach;

                // get the last cost for the item
                $sqlCost = "SELECT PURCHASE_COST, AVG_COST FROM TBL_STOCK WHERE EBQ_CODE = '$entity_id';";

                $costResult = $this->db->query($sqlCost);
                foreach ($costResult->getResult('array') as $row) : {
                        $costPreviously = $row['PURCHASE_COST'];
                        $averagePreviously = $row['AVG_COST'];
                }
                endforeach;

                // TODO: clarify if updates to the PURCHASE price will affect the average cost

                // the new average is calculated by adding the new purchase price to the old average and dividing by 2
                $newAvg = ($oldQuantity * (float)$purchase_cost);

                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // ITEM WITH NO SUB ITEMS: if the stock item is a SINGLE ITEM with NO SUBSIDIARY ITEMS, it can be inserted in the stock table
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------  
                if ($this->request->getPost('is_built') != 'on') 
                {
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // if the STOCK NAME IS UNCHANGED, the stock update is simple
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    if($entity_id == $stock_code) {

                        $sqlUpdateTBLSTOCK = 
                        "UPDATE TBL_STOCK 
                        SET
                        AVG_COST = $newAvg,
                        DESCRIPTION = $description,
                        WASTAGE = $wastage,
                        MARKUP = $markup,
                        MIN_REORDER = $minreorder,
                        LAST_COST = $costPreviously,
                        IS_ACTIVE = $isactive,
                        METRIC_ID = $metricID,
                        IS_BUILT = $isbuilt
                        WHERE
                        EBQ_CODE = '$entity_id';";
                                        
                        $this->db->query($sqlUpdateTBLSTOCK);
                    }
                    else 
                    {  

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update table hub stock, set the new EBQ code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    
                    $sqlUpdateTblHubStock = "UPDATE TBL_HUB_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblHubStock);


                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update table grn stock, set the new EBQ code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblGRN = "UPDATE TBL_GRN_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblGRN);


                    $this->db->query($sqlUpdateTblEBQFK);

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update table ebq barcode, set the new EBQ code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblEBQ_bar = "UPDATE TBL_EBQ_BARCODE SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblEBQ_bar);

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update table order stock, set the new EBQ code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                
                    $sqlUpdateTblORDER = "UPDATE TBL_ORDER_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblORDER);
                                                                            

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // UPDATE BOTH SUB AND LARGE EBQ_CODES IN TBL_STOCK_COMBINATION
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblStockComb1 = "UPDATE TBL_STOCK_COMBINATION SET EBQ_CODE_LG = '$stock_code' WHERE EBQ_CODE_LG = '$entity_id';";

                    $this->db->query($sqlUpdateTblStockComb1);

                    $sqlUpdateTblStockComb2 = "UPDATE TBL_STOCK_COMBINATION SET EBQ_CODE_SUB = '$stock_code' WHERE EBQ_CODE_SUB = '$entity_id';";

                    $this->db->query($sqlUpdateTblStockComb2);


                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update TBL_INVOICE_STOCK with new EBQ_Code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblInvoice = "UPDATE TBL_INVOICE_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblInvoice);

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update TBL_PACKING_BILL_STOCK with new EBQ_Code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblPBS = "UPDATE TBL_PACKING_BILL_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblPBS);


                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update TBL_PURCHASE_ORDER_STOCK with new EBQ_Code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblPOS = "UPDATE TBL_PURCHASE_ORDER_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblPOS);


                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update TBL_QUOTE_STOCK with new EBQ_Code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblQS = "UPDATE TBL_QUOTE_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblQS);

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update TBL_REQUISITION with new EBQ_Code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblRS = "UPDATE TBL_REQUISITION SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblRS); 

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update TBL_STOCK_JOURNAL with new EBQ_Code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblSJ = "UPDATE TBL_STOCK_JOURNAL SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                    $this->db->query($sqlUpdateTblSJ); 

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // update TBL_VOC_STOCK with new EBQ_Code
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdateTblVS = "UPDATE TBL_VOC_STOCK SET STOCK_EBQ = '$stock_code' WHERE STOCK_EBQ = '$entity_id';";

                    $this->db->query($sqlUpdateTblVS);

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // UPDATE TBL_STOCK: Change the EBQ name and the other values
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    $sqlUpdtStock = 
                    "UPDATE TBL_STOCK 
                    SET
                    EBQ_CODE = '$stock_code',  
                    PURCHASE_COST = $purchase_cost,
                    AVG_COST = $newAvg,
                    DESCRIPTION = $description,
                    WASTAGE = $wastage,
                    MARKUP = $markup,
                    MIN_REORDER = $minreorder,
                    LAST_COST = $costPreviously,
                    IS_ACTIVE = $isactive,
                    METRIC_ID = $metricID,
                    IS_BUILT = $isbuilt
                    WHERE
                    EBQ_CODE = '$entity_id';";   
                    
                    $this->db->query($sqlUpdtStock);

                                                
                    }

                    

                    // delete any combination items
                    $sqlDeleteComboItems =                         
                    "DELETE FROM TBL_STOCK_COMBINATION WHERE EBQ_CODE_LG = '$entity_id';";
    
                    $this->db->query($sqlDeleteComboItems);    

    
                } 
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // LARGE ITEM WITH SUB ITEMS: the stock item is a LARGE stock item WITH subsidiary stock items
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------  
                else 
                {
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // get the total value of the sub stock
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------   

                    // get the stock objects
                    if(isset($_POST['create-stock'])) {

                        $subStockQuantity = $_POST['create-stock'];   
                        
                        $totalValueOfLargeItem = 0;
                            
                        // determine the purchase cost                     
                        foreach ($subStockQuantity as $stockCode => $stockQuantityValue) : {                    
                       
                            // a variable to hold the average cost of the subsidiary items
                            $valueOfSubItemsQuery = "SELECT AVG_COST FROM TBL_STOCK WHERE EBQ_CODE = '$stockCode';";                                               

                            $subResult = $this->db->query($valueOfSubItemsQuery);
                            
                            // loop through the result to get the average cost of the item
                            foreach ($subResult->getResult('array') as $row) : {                        
                                $valueOfSubItem = $row['AVG_COST'];                        
                            }
                            endforeach;

                            // determine the total value of the single subsidiary item by multiplying its average cost by its quantity
                            $valueMultipliedByQuantity = $valueOfSubItem * $stockQuantityValue;                        

                            // add the value of the SUB ITEM to the LARGE ITEM
                            $totalValueOfLargeItem = $totalValueOfLargeItem + $valueMultipliedByQuantity; 

                        }
                        endforeach;   
                        

                    }                                         

              
                    // get the count of rows in the stock combination table
                    $sqlCountSC = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK_COMBINATION WHERE EBQ_CODE_LG = '$entity_id';";
                    $countOfRowsSC = $this->db->query($sqlCountSC);


                    // a variable to count the rows of stock it the large item
                    $rowCountFromDB = 0;
                    $rowCountFromUI = 0;
                          
                    foreach ($countOfRowsSC->getResult('array') as $row) : {
                        $rowCountFromDB = $row['COUNT']; 
                    }
                    endforeach;
                    
                    // arrays of the stock from the db and from the UI
                    $arrayOfAddedStock=array();
                    $arrayOfDBSubStock=array();

                    foreach ($subStockQuantity as $stockCode => $stockQuantityValue) : {
                        
                        // as the stock loops, add each stock code to the array for comparison against the db array later
                        array_push($arrayOfAddedStock,$stockCode);

                    }
                    endforeach;

                    // count the number of rows of added stock
                    $rowCountFromUI = count($arrayOfAddedStock);
                                            
                    // insert subsidiary stock items into TBL_STOCK_COMBINATION
                    foreach ($subStockQuantity as $stockCode => $stockQuantityValue) : {
                        
                        // a variable to hold the average cost of the subsidiary items
                        $stockArrayQuery = "SELECT EBQ_CODE_SUB FROM TBL_STOCK_COMBINATION WHERE EBQ_CODE_LG = '$entity_id';";                                               

                        $stockArrayResult = $this->db->query($stockArrayQuery);

                        
                        // populate the db array of sub stock item rows
                        foreach ($stockArrayResult->getResult('array') as $row) : {                        
                            $singleStockID = $row['EBQ_CODE_SUB'];  
                            array_push($arrayOfDBSubStock,$singleStockID);                      
                        }
                        endforeach;                                        

                        // remove any SUB STOCK from the database that were removed from the UI
                        $differenceOfSubItemRows=array_diff($arrayOfDBSubStock,$arrayOfAddedStock);
                        
                        // a boolean to determine if the sub item is already in the stock combination table
                        $foundThisEBQ = false;

                        // determine if the entered sub stock code already exists
                        $sqlEBQExists = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK_COMBINATION WHERE EBQ_CODE_SUB = '$stockCode' AND EBQ_CODE_LG = '$entity_id';";
                        $ebqresult = $this->db->query($sqlEBQExists);
                        
                        // if the count of rows is more than zero, the entered sub item already exists
                        foreach ($ebqresult->getResult('array') as $row) : {
                            if ($row['COUNT'] > 0) {
                                $foundThisEBQ = true;                                    
                            }
                        }
                        endforeach;

                        // if the item DOES NOT exist, insert it into the stock combination table
                        if(!$foundThisEBQ)
                        {
                            // create new entries in the table     
                            $sqlQueryInst = "INSERT INTO TBL_STOCK_COMBINATION (EBQ_CODE_LG, EBQ_CODE_SUB, QUANTITY) VALUES ('$entity_id', '$stockCode', $stockQuantityValue);";                            

                            $this->db->query($sqlQueryInst); 

                        }
                        // if the stock item DOES exist, update its existing record
                        else                    
                        {
                            // get the value of the quantity of the sub item and calculate the difference
                            $sqlQuantityUpdate = "UPDATE TBL_STOCK_COMBINATION SET QUANTITY = $stockQuantityValue WHERE EBQ_CODE_SUB = '$stockCode' AND EBQ_CODE_LG = '$entity_id';"; 
                                                                    
                            $this->db->query($sqlQuantityUpdate);

                        }                            
                        
                        // remove any stock from the DB that was removed from the LARGE stock item
                        if ($rowCountFromUI != $rowCountFromDB)
                        {
                            foreach($differenceOfSubItemRows as $item) {

                                $stockToDelete = $item;
                                
                                $sqlDeleteSC =                         
                                "DELETE FROM TBL_STOCK_COMBINATION WHERE EBQ_CODE_LG = '$entity_id' AND EBQ_CODE_SUB = '$stockToDelete';";
                
                                $this->db->query($sqlDeleteSC);    
                            }
                        }

                    }
                    endforeach;                                                             

                    // if the stock name is unchanged, the stock update is simple
                    if($entity_id == $stock_code) {
                        
                        // get the last cost for the item
                        $sqlCostHere = "SELECT PURCHASE_COST, AVG_COST FROM TBL_STOCK WHERE EBQ_CODE = '$entity_id';";

                        $costResultHere = $this->db->query($sqlCostHere);
                        foreach ($costResultHere->getResult('array') as $row) : {
                                $costPreviouslyHere = $row['PURCHASE_COST'];
                                $averagePreviouslyHere = $row['AVG_COST'];
                        }
                        endforeach;

                        // the new average is calculated by adding the new purchase price to the old average and dividing by 2
                        $newAvgHere = ($averagePreviouslyHere + $totalValueOfLargeItem) / 2;

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // Update TBL_STOCK: the LARGE ITEM can be updated into the database with the new values accumulated from the SUB items
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        
                        $sqlStockUpdt = 
                        "UPDATE TBL_STOCK 
                        SET
                        PURCHASE_COST = $totalValueOfLargeItem,
                        AVG_COST = $newAvgHere,
                        DESCRIPTION = $description,
                        WASTAGE = $wastage,
                        MARKUP = $markup,
                        MIN_REORDER = $minreorder,
                        LAST_COST = $costPreviouslyHere,
                        IS_ACTIVE = $isactive,
                        METRIC_ID = $metricID,
                        IS_BUILT = $isbuilt
                        WHERE
                        EBQ_CODE = '$entity_id';";
                                        
                        $this->db->query($sqlStockUpdt); 
                                            
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // Update TBL_STOCK_COMBINATION: the stock comprises of other stock items that need to be added to the Stock Combination Table
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        // loop through the stock object to insert subsidiary stock items into TBL_STOCK_COMBINATION
                        foreach ($subStockQuantity as $stockCode => $stockQuantityValue) : {

                            // create new entries in the table     
                            $sqlQuerySC = 
                            "UPDATE TBL_STOCK_COMBINATION 
                            SET QUANTITY =  $stockQuantityValue 
                            WHERE EBQ_CODE_LG = '$entity_id' 
                            AND EBQ_CODE_SUB = '$stockCode';";                            

                            $this->db->query($sqlQuerySC);                                   

                        }
                        endforeach;                                
                        
                    }
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // else the STOCK CODE IS CHANGED, all the fks need to be dropped and values updated
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    else
                    {                

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update table hub stock, set the new EBQ code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        
                        $sqlUpdateTblHubStock = "UPDATE TBL_HUB_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblHubStock);

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update table grn stock, set the new EBQ code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblGRN = "UPDATE TBL_GRN_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblGRN);

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update table ebq barcode, set the new EBQ code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblEBQ_bar = "UPDATE TBL_EBQ_BARCODE SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblEBQ_bar);

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update table order stock, set the new EBQ code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    
                        $sqlUpdateTblORDER = "UPDATE TBL_ORDER_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblORDER);
                                                                                

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // UPDATE BOTH SUB AND LARGE EBQ_CODES IN TBL_STOCK_COMBINATION
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblStockComb1 = "UPDATE TBL_STOCK_COMBINATION SET EBQ_CODE_LG = '$stock_code' WHERE EBQ_CODE_LG = '$entity_id';";

                        $this->db->query($sqlUpdateTblStockComb1);

                        $sqlUpdateTblStockComb2 = "UPDATE TBL_STOCK_COMBINATION SET EBQ_CODE_SUB = '$stock_code' WHERE EBQ_CODE_SUB = '$entity_id';";

                        $this->db->query($sqlUpdateTblStockComb2);


                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update TBL_INVOICE_STOCK with new EBQ_Code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblInvoice = "UPDATE TBL_INVOICE_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblInvoice);
                                  

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update TBL_PACKING_BILL_STOCK with new EBQ_Code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblPBS = "UPDATE TBL_PACKING_BILL_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblPBS);


                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update TBL_PURCHASE_ORDER_STOCK with new EBQ_Code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblPOS = "UPDATE TBL_PURCHASE_ORDER_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblPOS);

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update TBL_QUOTE_STOCK with new EBQ_Code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblQS = "UPDATE TBL_QUOTE_STOCK SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblQS);


                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update TBL_REQUISITION with new EBQ_Code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblRS = "UPDATE TBL_REQUISITION SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblRS); 


                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update TBL_STOCK_JOURNAL with new EBQ_Code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblSJ = "UPDATE TBL_STOCK_JOURNAL SET EBQ_CODE = '$stock_code' WHERE EBQ_CODE = '$entity_id';";

                        $this->db->query($sqlUpdateTblSJ); 


                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // update TBL_VOC_STOCK with new EBQ_Code
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        $sqlUpdateTblVS = "UPDATE TBL_VOC_STOCK SET STOCK_EBQ = '$stock_code' WHERE STOCK_EBQ = '$entity_id';";

                        $this->db->query($sqlUpdateTblVS);

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                     
                        // get the last cost for the item
                        $sqlCostHere = "SELECT PURCHASE_COST, AVG_COST FROM TBL_STOCK WHERE EBQ_CODE = '$entity_id';";

                        $costResultHere = $this->db->query($sqlCostHere);
                        foreach ($costResultHere->getResult('array') as $row) : {
                                $costPreviouslyHere = $row['PURCHASE_COST'];
                                $averagePreviouslyHere = $row['AVG_COST'];
                        }
                        endforeach;

                        // the new average is calculated by adding the new purchase price to the old average and dividing by 2
                        $newAvgHere = ($averagePreviouslyHere + $totalValueOfLargeItem) / 2;

                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // Update TBL_STOCK: change the name of the EBQ_CODE and the other values that were changed.
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        
                        $sqlStockUpdt = 
                        "UPDATE TBL_STOCK 
                        SET
                        EBQ_CODE = '$stock_code',
                        PURCHASE_COST = $totalValueOfLargeItem,
                        AVG_COST = $newAvgHere,
                        DESCRIPTION = $description,
                        WASTAGE = $wastage,
                        MARKUP = $markup,
                        MIN_REORDER = $minreorder,
                        LAST_COST = $costPreviouslyHere,
                        IS_ACTIVE = $isactive,
                        METRIC_ID = $metricID,
                        IS_BUILT = $isbuilt
                        WHERE
                        EBQ_CODE = '$entity_id';";
                                        
                        $this->db->query($sqlStockUpdt);
                                                                                                                                                         
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        // Update TBL_STOCK_COMBINATION
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                        // loop through the stock object to insert subsidiary stock items into TBL_STOCK_COMBINATION
                        foreach ($subStockQuantity as $stockCode => $stockQuantityValue) : {

                            // create new entries in the table     
                            $sqlQueryUpdtSC = 
                            "UPDATE TBL_STOCK_COMBINATION 
                            SET QUANTITY =  $stockQuantityValue 
                            WHERE EBQ_CODE_LG = '$stock_code' 
                            AND EBQ_CODE_SUB = '$stockCode';";                            

                            $this->db->query($sqlQueryUpdtSC);

                        }
                        endforeach;                                
                    }
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                }
            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            return redirect()->to('/stock/search');
            exit();
            } 
        }
        echo view('stock/update', $data);
    }
}
