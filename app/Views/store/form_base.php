<form method="post" id="form_store" action="<?php echo $url ?>" class="form-horizontal" autocomplete="off">
                <input type="hidden" name="form_create_store" value="<?php echo $form_create; ?>" />
                <input type="hidden" name="form_update_store" value="<?php echo $form_update; ?>" />
                <input type="hidden" name="store" value="<?php if (isset($store_id)) {echo $store_id;} ?>" />
                <div class="panel-body">
                    <div class="form-group">
                    <label class="col form-label">Store Number</label>
                    <div class="col">
                        

    

                    
                    <?php if (isset($found) && $found == true && $action_type == 'create') { ?>
               <input type="number" class="form-control is-invalid" name="store_id" required="" value="<?php if (isset($store_id)) {echo $store_id;} ?>" placeholder="Enter the unique store ID">
			<small class="invalid-feedback">The store ID already exists in the database, please enter a unique store ID.</small>
			<?php } else if (isset($found) && $found == true && $action_type == 'update') { ?>
                <input type="number" class="form-control is-invalid" name="store_id" required="" value="<?php if (isset($store_id)) {echo $store_id;} ?>" placeholder="Enter the unique store ID">
				<small class="invalid-feedback">The store ID already exists in the database, please enter a unique store ID.</small>
				<?php } else { ?>
                    
                    
                    <input type="number" class="form-control" name="store_id" required="" value="<?php if (isset($store_id)) {echo $store_id;} ?>" placeholder="Enter the unique store ID">
                
                    <?php } ?>
                
                </div>
                </div>


	            <div class="panel-body">
                    <div class="form-group">
                    <label class="col form-label">Store Name</label>
                    <div class="col"><input type="text" class="form-control" name="storename" required="" value="<?php if (isset($storename)) {echo $storename;} ?>" placeholder="Enter the store name"></div>
                </div>

                <div class = "form-group">
                    <label class="col form-label">Store Type</label>
                    <div class = "col">   

                <?php  
                    echo "<select class='custom-select btn-block' name='storedesc'>";         
                    
                    if (isset($storetype)) {
                        $sql = " SELECT STORE_TYPE_ID AS value, STORE_TYPE_DESCRIPTION AS description FROM TBL_STORE_TYPE";
                        gen_select_dropdown($db, $sql, $storetype);
                    }
                    if (isset($object)) {

                        foreach ($object->getResult('array') as $row): 
                        {         
                            echo "<option>{$row['STORE_TYPE_DESCRIPTION']}</option>";
                        } 
                        endforeach;
                    }

                    echo "</select>";
                ?>                               

                    </div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Contact Number</label>
                    <div class="col"><input type="number" class="form-control" name="contact" required="" value="<?php if (isset($contact)) {echo $contact;} ?>" placeholder="Enter a contact number"></div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Store Manager</label>
                    <div class="col"><input type="text" class="form-control" name="storemanager" required="" value="<?php if (isset($storemanager)) {echo $storemanager;} ?>" placeholder="Enter the store manager's name"></div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Location (GPS Coordinates)</label>
                    <div class="col"><input type="text" class="form-control" name="latitude" required=""
                    title="Must start with N or S and be a valid latitude co-ordinate which is accurate to at least 4 decimal places" value="<?php if (isset($latitude)) {echo assignDirectionChar($latitude, true);} ?>" 
                    placeholder="Enter the latitude: eg. S32.476824"></div>
                
                    <div class="col" style="padding-top:0.5em"><input type="text" class="form-control" name="longitude" required=""
                    
            title="Must start with E or W and be a valid longitude co-ordinate which is accurate to at least 4 decimal places" value="<?php if (isset($longitude)) {echo assignDirectionChar($longitude, false);} ?>" 
            placeholder="Enter the longitude: eg. E24.061344"></div>                     
                </div>

                

                <div class = "form-group">
                    <label class="col form-label">Fixture Fitting Code</label>
                    <div class="col"><input type="text" class="form-control" name="ffcode" required="" value="<?php if (isset($ffcode)) {echo $ffcode;} ?>"
                    placeholder="Enter the Fixture Fitting Code"></div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Opening Date</label>
                    <div class="col"><input type="text" class="form-control" id="openingdate" name="openingdate" required value = "<?php if (isset($openingdate)) {echo $openingdate;} ?>"
                    placeholder="Enter or select the date: eg. 2020/01/01"></div>
                </div>
                
                <div class="form-group">
                    <label class="col form-label">Maintenance Date</label>
                    <div class="col"><input type="text" class="form-control" id="maintenancedate" name="maintenancedate" required value="<?php if (isset($maintenancedate)) {echo $maintenancedate;} ?>"
                    placeholder="Enter or select the date: eg. 2020/11/01"></div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Is the store in a center?</label>
                    <div class="col">

                        <?php
                                if (isset($iscenter) && $iscenter != 1) {
                        ?>
                                    <label class="switcher">                        
                                        <input type="checkbox" name='iscenter' class="switcher-input">
                                        <span class="switcher-indicator">
                                            <span class="switcher-yes"></span>
                                            <span class="switcher-no"></span>
                                        </span>
                                        <span class="switcher-label">Yes</span>
                                    </label>
                        <?php
                                }
                                else {

                        ?>
                                    <label class="switcher">                        
                                        <input type="checkbox" checked='checked' name='iscenter' class="switcher-input">
                                        <span class="switcher-indicator">
                                            <span class="switcher-yes"></span>
                                            <span class="switcher-no"></span>
                                        </span>
                                        <span class="switcher-label">Yes</span>
                                    </label>
                        <?php
                                }
                        ?>   
                           
                    </div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Is the store open?</label>
                    <div class="col">
                    <?php
                            if (isset($isopen) && $isopen != 1) {
                        ?>
                                    <label class="switcher">                        
                                        <input type="checkbox" name='isopen' class="switcher-input">
                                        <span class="switcher-indicator">
                                            <span class="switcher-yes"></span>
                                            <span class="switcher-no"></span>
                                        </span>
                                        <span class="switcher-label">Yes</span>
                                    </label>
                        <?php
                                }
                                else {

                        ?>
                                    <label class="switcher">                        
                                        <input type="checkbox" checked='checked' name='isopen' class="switcher-input">
                                        <span class="switcher-indicator">
                                            <span class="switcher-yes"></span>
                                            <span class="switcher-no"></span>
                                        </span>
                                        <span class="switcher-label">Yes</span>
                                    </label>
                        <?php
                                }
                        ?>
                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Branch Size (sq m)</label>
                    <div class="col"><input type="number" class="form-control" name="branchsize" value="<?php if (isset($branchsize)) {echo $branchsize;} ?>"
                    placeholder="Enter the branch size in square meters"></div>
                </div>                

                <div class="form-group">
                    <label class="col form-label">Trading Size (sq m)</label>
                    <div class="col"><input type="number" class="form-control" name="tradingsize" value="<?php if (isset($tradingsize)) {echo $tradingsize;} ?>"
                    placeholder="Enter the trading size in square meters"></div>
                </div>

                <div class = "form-group">
                    <label class="col form-label">Hub</label>
                    <div class = "col">
                            
                <?php  
                    echo "<select class='custom-select btn-block' name='hub'>";         
                    if (isset($hubid)) {
                        $sql = " SELECT HUB_ID AS value, HUB_NAME AS description FROM TBL_HUB";
                        gen_select_dropdown($db, $sql, $hubid);
                    }
                    if (isset($object)) {

                    
                    foreach ($objectHub->getResult('array') as $row): 
                    {         
                        echo "<option>{$row['HUB_NAME']}</option>";
                    } 
                    endforeach;

                }
                    echo "</select>";
                ?>   
                    </div>
                </div>

                <div class = "form-group">
                    <label class="col form-label">Area</label>
                    <div class = "col">
                    <?php  
                    echo "<select class='custom-select btn-block' name='area'>";         
                    if (isset($areaid)) {
                        $sql = " SELECT AREA_NO AS value, AREA_NAME AS description FROM TBL_AREA";
                        gen_select_dropdown($db, $sql, $storetype);
                    }

                    if (isset($object)) {


                    foreach ($objectArea->getResult('array') as $row): 
                    {         
                        echo "<option>{$row['AREA_NAME']}</option>";
                    } 
                    endforeach;

                }
                    echo "</select>";
                ?>   
                    </div>
                </div>

                <div class = "form-group">
                    <label class="col form-label">Preferred Contractor</label>
                    <div class = "col">
                    <?php  
                    echo "<select class='custom-select btn-block' name='prefcontractor'>";         
                    if (isset($contractorid)) {
                        $sql = "SELECT DISTINCT CONTRACTOR_ID AS value, CONTRACTOR_NAME AS description FROM TBL_CONTRACTOR WHERE IN_BUSINESS=1";
                        gen_select_dropdown($db, $sql, $contractorid);
                    }

                    if (isset($object)) {

                    
                    foreach ($objectContractor->getResult('array') as $row): 
                    {         
                        echo "<option>{$row['CONTRACTOR_NAME']}</option>";
                    } 
                    endforeach;

                }
                    echo "</select>";
                ?>
                    </div>
                </div>

        
        
		<div class="hr-line-dashed"></div>
		<br/>  
		<button type="button" class="btn btn-primary" id="btn-add-store"><i class="fas fa-check"></i> 
            <?php
                if ($action_type == 'create') {

            ?>
                    Create Store
            <?php
                }
                else {
            ?>
                    Update       
            <?php
                }
            ?>
        </button>
	</div>
</form>