<?php

namespace App\Controllers\Stock;

use \App\Controllers\BaseController;

class Create extends BaseController
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


    public function index()
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;
        $data["action_type"] = "create";
        $data["url"] = "/stock/create";
        $data['form_create'] = "true";
        $data['form_update'] = "false";

        if ($this->request->getPost('form_create_stock') == "true") {

            $ebq_code = $this->db->escape(trim($this->request->getPost('ebq_code')));
            $purchase_cost = 0;
            
            $description = $this->db->escape(trim($this->request->getPost('stock_description')));
            if (($this->request->getPost('minreorder') != null) || ($this->request->getPost('minreorder') != '')) {
                $minreorder = $this->request->getPost('minreorder');
            }
            else {
                $minreorder = 0;
            }
            $metName = $this->db->escape(trim($this->request->getPost('metricdesc')));

            $markup = $this->request->getPost('markup');
            $wastage = $this->request->getPost('wastage');
            
            // boolean variables for indicating whether the EBQ or product description already exist in the database
            $found = false;
            $foundDescription = false;

            $data['ebq_code'] = $this->request->getPost('ebq_code');
            $data['purchase_cost'] = $this->request->getPost('purchase_cost');
            $data['stock_description'] = $this->request->getPost('stock_description');
            $data['minreorder'] = $this->request->getPost('minreorder');
            $data['metricdesc'] = $this->request->getPost('metricdesc');            
            $data['markup'] = $this->request->getPost('markup');
            $data['wastage'] = $this->request->getPost('wastage');

            $data['found'] = false;
            $data['foundDescription'] = false;

            // determine if the entered EBQ already exists
            $sqlEBQExists = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK WHERE EBQ_CODE = $ebq_code;";
            $ebqresult = $this->db->query($sqlEBQExists);
            
            foreach ($ebqresult->getResult('array') as $row) : {
                if ($row['COUNT'] > 0) {
                    $found = true;
                    $data['found'] = true;
                }
            }
            endforeach;

            // determine if the entered description already exists
            $sqlDescriptionExists = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK WHERE DESCRIPTION = $description;";
            $descriptionResult = $this->db->query($sqlDescriptionExists);
            
            foreach ($descriptionResult->getResult('array') as $row) : {
                if ($row['COUNT'] > 0) {
                    $foundDescription = true;
                    $data['foundDescription'] = true;
                }
            }
            endforeach;

            // if the stock item is active
            if ($this->request->getPost('is_active') == 'on') {
                $isactive = 1;
            } else {
                $isactive = 0;
            }

            // if the item is comprised of other items, save the data entered for a sticky form
            if ($this->request->getPost('is_built') == 'on') {
                // get the stock objects
                $stockQuantityAll = $_POST['create-stock'];
                $data['stockQuantityAll'] = $stockQuantityAll;     
                $data['foundItems'] = true;      
                $data['is_built'] = 1;      
                $isbuilt = 1;
            } else {
                $data['is_built'] = 0; 
                $isbuilt = 0;
            }

            // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

            // if the EBQ nor the description are found in the database, the rest of the form can be processed
            if (!$found && !$foundDescription) {

                // get metric ID for the entered metric type
                $sqlMetric = "SELECT METRIC_ID FROM TBL_METRIC WHERE METRIC_DESCRIPTION = $metName;";
                $metricResult = $this->db->query($sqlMetric);
                foreach ($metricResult->getResult('array') as $row) : {
                        $metricID = $row['METRIC_ID'];
                    }
                endforeach;              

                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                     
                // CALCULATION FOR AVERAGE STOCK PRICE
                // -------------------------------------------------------
                // this is to calculate the average cost of the item
                // OLD STOCK * AVERAGE = SUBTOTAL
                // NEW STOCK * INCOMING PRICE = SUBTOTAL
                // (SUBTOTAL + SUBTOTAL) / STOCK ITEMS = NEW AVERAGE
                // -------------------------------------------------------
                                         
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // ITEM WITH NO SUB ITEMS
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                
                // if the stock item is a single item with no subsidiary items, it can be inserted in the stock table and into the hub stock table 
                if ($this->request->getPost('is_built') != 'on') {

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // The stock item is a stock item WITHOUT subsidiary stock items
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    // the average cost of the new item is the same as the purchase cost, average costs are calculated on items with historical data
                
                    // insert statement for the new stock item into the stock table
                    $sql = "INSERT INTO TBL_STOCK (EBQ_CODE,PURCHASE_COST,AVG_COST,DESCRIPTION,WASTAGE,MARKUP,MIN_REORDER,LAST_COST,IS_ACTIVE,METRIC_ID,IS_BUILT) VALUES
                    ($ebq_code,
                    $purchase_cost,
                    $purchase_cost,
                    $description,
                    $wastage,
                    $markup,
                    $minreorder,
                    $purchase_cost,
                    $isactive,
                    $metricID,
                    $isbuilt);";                    
    
                    $this->db->query($sql);       

                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // LARGE ITEM WITH SUB ITEMS
                // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                } else {

                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    // The stock item is a LARGE stock item WITH subsidiary stock items
                    // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    // create a variable to hold the value of the new LARGE item
                    $largeItemValue = 0;
                                                                 
                    // loop through the chosen subsidiary stock items
                    if(isset($_POST['create-stock'])) {
                
                        // get the stock objects
                        $stockQuantity = $_POST['create-stock'];
    
                        // insert subsidiary stock items into TBL_STOCK_COMBINATION
                        foreach ($stockQuantity as $stockCode => $stockQuantityValue) : {                   

                        // a variable to hold the average cost of the subsidiary items
                        $averageValueOfSubItemsQuery = "SELECT AVG_COST FROM TBL_STOCK WHERE EBQ_CODE = '$stockCode';";                                               

                        $subResult = $this->db->query($averageValueOfSubItemsQuery);
                            
                        // loop through the result to get the average cost of the item
                        foreach ($subResult->getResult('array') as $row) : {                        
                            $averageValueOfSubItem = $row['AVG_COST'];                        
                        }
                        endforeach;

                        // determine the total value of the single subsidiary item by multiplying its average cost by its quantity
                        $valueMultipliedByQuantity = $averageValueOfSubItem * $stockQuantityValue;

                        // the LARGE ITEM value is equal the sum of the SUB ITEMS multiplied by the number of LARGE ITEMS in the hubs   
                        $largeItemValue = $largeItemValue + $valueMultipliedByQuantity;   
                                                                                                                                                                                                
                        }
                        endforeach;                                                            
                          
                        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                        
                        
                        // if the item is built from sub-items; the markup, wastage, min re-order are defaulted to zero
                        if($isbuilt = 1)
                        {
                            $sqlInsert = "INSERT INTO TBL_STOCK (EBQ_CODE,PURCHASE_COST,AVG_COST,DESCRIPTION,WASTAGE,MARKUP,MIN_REORDER,LAST_COST,IS_ACTIVE,METRIC_ID,IS_BUILT) 
                            VALUES ($ebq_code,$largeItemValue,$largeItemValue,$description,0,0,0,$largeItemValue,$isactive,$metricID,$isbuilt);";
                        }
                        else
                        {
                        // the LARGE ITEM can be inserted into the database with the new values accumulated from the SUB items
                        $sqlInsert = "INSERT INTO TBL_STOCK (EBQ_CODE,PURCHASE_COST,AVG_COST,DESCRIPTION,WASTAGE,MARKUP,MIN_REORDER,LAST_COST,IS_ACTIVE,METRIC_ID,IS_BUILT) 
                        VALUES ($ebq_code,$largeItemValue,$largeItemValue,$description,$wastage,$markup,$minreorder,$largeItemValue,$isactive,$metricID,$isbuilt);";
                        }
                                        
                        $this->db->query($sqlInsert);

                        // loop through the stock object to insert subsidiary stock items into TBL_STOCK_COMBINATION
                        foreach ($stockQuantity as $stockCode => $stockQuantityValue) : {

                            // create new entries in the table     
                            $sqlQuery = "INSERT INTO TBL_STOCK_COMBINATION (EBQ_CODE_LG, EBQ_CODE_SUB, QUANTITY) VALUES ($ebq_code, '$stockCode', $stockQuantityValue);";                            

                            $this->db->query($sqlQuery);
                        }
                        endforeach;                                                       
                    }                
                }    

            // the new stock has been added to the database and the user is returned to the search page         
            return redirect()->to('/stock/search');
            exit();

            } else {
                // the EBQ code or description already exists
                echo view('stock/create', $data);
            }
        } else {
            echo view('stock/create', $data);
        }
    }
}
