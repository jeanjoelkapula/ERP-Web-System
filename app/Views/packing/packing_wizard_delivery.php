<div id="packing-bill-wizard-step-2" class="card animated fadeIn tab-pane step-content">
<h6 class="card-header">Delivery Details</h6>
    <div class="card-body">
        <input type='hidden' name="source_hub_id" value=""/>
        <input type='hidden' name="destination_hub" value=""/>
        <div class = "form-group" id="store_form_group">
            <label class="col form-label">Store</label>
            <div class = "col">
                <select id="store-selector" disabled class="custom-select btn-block" name="store_id">
                <?php 
                         $sql = "SELECT STORE_ID AS value, CONCAT(s.STORE_ID,' - ',s.STORE_NAME) AS description FROM TBL_STORE s WHERE s.IS_OPEN = 1;";

                        if (isset($store_id)){
                            gen_select_dropdown($db, $sql, $store_id);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    ?>
                </select>
            </div>
            <!-- <div class = "col">
                <input type="checkbox" name="site-delivery" onchange="handleDeliverToSite()"> Deliver to site </input>
            </div> -->

        </div>
        <div class = "form-group">
            <label class="col form-label">Source Hub</label>
            <div class = "col">
                <select class="custom-select btn-block" disabled name="source_hub" id="source_hub">
                <?php 
                         $sql = "SELECT HUB_ID AS value, HUB_NAME AS description FROM TBL_HUB";
                        if (isset($source_hub)){
                            gen_select_dropdown($db, $sql, $source_hub);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class = "form-group">
            <label class="col form-label">Destination Hub</label>
            <div class = "col">
                <select class="custom-select btn-block" name="destination_hub" id ="destination_hub">
                <?php 
                        $sql = "SELECT HUB_ID AS value, HUB_NAME AS description FROM TBL_HUB";
                        if (isset($destination_hub)){
                            gen_select_dropdown($db, $sql, $destination_hub);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col form-label">Ship Via <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control required" name="ship_via" required value="<?php if(isset($ship_via)) echo $ship_via;?>"></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Delivery Date <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control" id="delivery_date" name="delivery_date" value="<?php if(isset($delivery_date)) echo $delivery_date;?>" required></div>
        </div>
        
        <div class = "form-group">
        <label class="col form-label">Suggested Pack date <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control" id="packing_date" name="packing_date" value="<?php if(isset($pack_date)) echo $pack_date;?>" required></div>
        </div>
 

      
        <div class="hr-line-dashed"></div>
        <br>  
        <br>
    </div>
</div>
