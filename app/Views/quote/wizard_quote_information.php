<div id="quote-stock-wizard-step-1" class="card animated fadeIn tab-pane step-content" style="display: block;">
<h6 class="card-header">Quote Details</h6>
    <div class="card-body">

        <div class = "form-group">
            <label class="col form-label">Quote Type</label>
            <div class = "col">
                <select class="custom-select btn-block" name="quote_type">
                <?php 
                        $sql = " SELECT TYPE_ID AS value, TYPE_NAME AS description FROM TBL_QUOTE_TYPE;";

                        if (isset($quote_type_id)){
                            gen_select_dropdown($db, $sql, $quote_type_id);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class = "form-group">
            <label class="col form-label">Store</label>
            <div class = "col">
                <select id="store-selector" class="custom-select btn-block" name="store_id" onblur="updateStoreID(event)">
                <?php 
                         $sql = "SELECT STORE_ID AS value, STORE_NAME AS description FROM TBL_STORE s WHERE s.IS_OPEN = 1;";

                        if (isset($store_id)){
                            gen_select_dropdown($db, $sql, $store_id);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class = "form-group">
            <label class="col form-label">Contractor</label>
            <div class = "col">
                <select class="custom-select btn-block" name="contractor_id" id="contractor-selector">
                <?php 
                         $sql = "SELECT CONTRACTOR_ID AS value, CONTRACTOR_NAME AS description FROM TBL_CONTRACTOR c WHERE c.IN_BUSINESS = 1;";

                        if (isset($contractor_id)){
                            gen_select_dropdown($db, $sql, $store_id);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class = "form-group">
            <label class="col form-label">Action Type</label>
            <div class = "col">
                <select class="custom-select btn-block" name="action_type">
                <?php 
                         $sql = "SELECT ACTION_ID AS value, ACTION_NAME AS description FROM TBL_ACTION_TYPE;";

                        if (isset($action_id)){
                            gen_select_dropdown($db, $sql, $action_id);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col form-label">Delivery Date <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control" id="datepicker" name="delivery_date" value="<?php if(isset($delivery_date)) echo $delivery_date;?>" required></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Ship Via <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control required" name="ship_via" required value="<?php if(isset($ship_via)) echo $ship_via;?>"></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Additional Notes</label>
                <div class="col">
                    <textarea class="form-control" rows="3" name="note"><?php if (isset($note)) {echo $note;} ?></textarea>
                </div>
            </div>
        <div class="hr-line-dashed"></div>
        <br>  
        <br>
    </div>
</div>
