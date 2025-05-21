<div id="purchase-order-wizard-step-1" class="card animated fadeIn tab-pane step-content" style="display: block;">
    <div class="card-body">
        <input type='text' name='purchase_order_no'  value = "<?php if(isset($purchase_order_no)) echo $purchase_order_id;?>" hidden/>
        <input type='text' id="action_type"  value = "<?php if(isset($form_update)) echo "update"; else echo "create";?>" hidden/>
        <div class="form-group">
            <label class="col form-label">Order Number <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control"  id="purchase_order_no" name="purchase_order_no"  value="<?php if(isset($purchase_order_no)) echo $purchase_order_no;?>" <?php if(isset($purchase_order_no)) echo 'disabled';?> required></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Order Date <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control" id="purchase-order-date" name="order_date"  value="<?php if(isset($order_date)) echo $order_date;?>" required></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Date Required<span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control" id="purchase-date-required" name="date_required"  value="<?php if(isset($date_required)) echo $date_required;?>" required></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Ship Via <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control required" name="ship_via" required  value="<?php if(isset($ship_via)) echo $ship_via;?>"></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Vendor Name <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control required" name="vendor_name" required  value="<?php if(isset($vendor_name)) echo $vendor_name;?>"></div>
        </div>
        <div class="form-group">
            <div class = "col">
                <label class="form-label">Vendor Address <span class="text-danger"></span></label>
                <div class="row">
                    <div class="w-100">
                        <div class="col-md">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name='vendor_address' class="form-control" placeholder="21 Jefferson St"  value="<?php if(isset($vendor_address)) echo $vendor_address;?>">
                                </div>
                            </div>
                        </div>
                        <div class='col'>
                            <div class="row">
                                <div class="col-md">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name='vendor_po_box' class="form-control" placeholder="PO Box 863"  value="<?php if(isset($vendor_po_box)) echo $vendor_po_box;?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name='vendor_zip_code' class="form-control" placeholder="7405"  value="<?php if(isset($vendor_zip_code)) echo $vendor_zip_code;?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <br>  
        <br>
    </div>
</div>
