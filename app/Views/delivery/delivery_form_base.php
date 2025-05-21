<form method="post" id="form_delivery" action="<?php echo $url ?>" class="form-horizontal" autocomplete="off">
                <input type="hidden" name="form_create_delivery" value="<?php echo $form_create; ?>" />
                <input type="hidden" name="form_update_delivery" value="<?php echo $form_update; ?>" />                
                                         

                <div class="form-group">
                    <label class="col form-label">Date</label>
                    <div class="col"><input type="text" class="form-control" id="maintenancedate" name="maintenancedate" required value="<?php if (isset($maintenancedate)) {echo $maintenancedate;} ?>"
                    placeholder="eg. 2020/11/01"></div>
                </div>



                <div class = "form-group">
                    <label class="col form-label">Waybill Number</label>
                    <div class="col"><input type="text" class="form-control" id="waybill_number" name="waybill_number" required="" value="<?php if (isset($waybill)) {echo $waybill;} ?>"
                    placeholder="Enter the Waybill Number"></div>
                </div>
                
                <div class="form-group">
                    <div class="col">
                        <div <?php if ((isset($action_type) && ($action_type == "update"))) echo "style='display:none !important;' ddddd"; ?>>
                            
                            <div class="row" >
                                <div class="form-group">
                                    <label class="col form-label"> Packing Bill Search <span class="text-danger">*</span></label>
                                    <div class="col">
                                        <div class="autocomplete" style="width:500px;">																	
                                            <input id="orderSearch" type="text" class="form-control" name="order_search" 
                                            placeholder="Search for a Packing Bill ID and date. eg 123 : 2020/01/01">															
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-left: 15px; margin-top: 23px;">
                                    <Button type="button" class="btn btn-primary" id="btn-order-add"><i class="fas fa-plus"></i>&nbsp; Add Packing Bill </Button>
                                </div>
                            </div>
                           
                            <br>
                            <div class="row">
                                <div class="table-responsive mb-4">
                                    <table class="table table-striped font-sm" id="tbl_order">
                                        <thead>
                                            <tr>
                                                <th class="py-3">
                                                    Packing Bill ID
                                                </th>						
                                                <th class="py-3">
                                                    Date Created
                                                </th>
                                                <th class="py-3">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_order_body">
                            

                                     <?php   if (isset($entity_id)) {				                        
                                        // get the order number
                                        $sqlOrder = 
                                        "SELECT TDN.PACKING_BILL_ID, DATE(TPB.CREATED_DATE) AS ORDER_DATE 
                                        FROM TBL_DELIVERY_NOTE TDN
                                        INNER JOIN TBL_PACKING_BILL TPB
                                        ON TDN.PACKING_BILL_ID = TPB.PACKING_BILL_ID
                                        WHERE TDN.DELIVERY_ID = $entity_id;";

                                        $orderResult = $db->query($sqlOrder);
                                
                                        // loop through the result to get the average cost of the item
                                        foreach ($orderResult->getResult('array') as $row) : { 
                                            $pbid = $row['PACKING_BILL_ID'];                         
                                            $ord_date = $row['ORDER_DATE'];                        
                                        }
                                        endforeach;
                                    ?>
                                    <tr>
                                                <td class="py-3"><?php echo $pbid; ?></td>
                                                <input type="hidden" name="create-delivery" value="<?php echo $pbid; ?>">
                                                <td class="py-3"><?php echo $ord_date; ?></td>	
                                                <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" 
                                                class="btn btn-circle btn-default btn-remove" <?php if ((isset($action_type) && ($action_type == "update"))) echo "disabled style='display:none;'"; ?>>X</span></a></td>
                                        </tr>
                                        <?php
                                    
                                        ?>
                                    <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class = "form-group">
                    <label class="col form-label">Additional Notes</label>
                    <div class="col">
                                                                
                    <textarea class="form-control" rows="3" name="notes"
                    placeholder="Enter any additional notes for the delivery note"><?php if (isset($notes)) {echo $notes;} ?></textarea>
                    </div>           
                    <br/>                                          

                <div class = "form-group">
                    <label class="col form-label">Delivery Method</label>
                    <div class="col"><input type="text" class="form-control" id="deliverymethod" name="deliverymethod" value="<?php if (isset($deliverymethod)) {echo $deliverymethod;} ?>"
                    placeholder="Enter the delivery method for the delivery note"></div>
                </div>

                <?php if ($form_update == true && $action_type == 'update') { ?>
                <div class="form-group">
                    <label class="col form-label">Has it been signed?</label>
                    <div class="col">

                        <?php
                                if (isset($is_signed) && $is_signed != 1) {
                        ?>
                                    <label class="switcher">                        
                                        <input type="checkbox" name='is_signed' class="switcher-input">
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
                                        <input type="checkbox" checked='checked' name='is_signed' class="switcher-input">
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
<?php
                            }
                            ?>

                               
		<div class="hr-line-dashed"></div>
		<br/>  
		<button type="button" class="btn btn-primary" id="btn-add-all"><i class="fas fa-check"></i> 
            <?php
                if ($action_type == 'create') {

            ?>
                    Create Delivery Note
            <?php
                }
                else {
            ?>
                    Update Delivery Note       
            <?php
                }
            ?>
        </button>
	</div>
</form>