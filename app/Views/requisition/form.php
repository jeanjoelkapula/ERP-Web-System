<div class="card mb-4">
    <h6 class="card-header">Requisition Details</h6>
    <div class="card-body">      

    <form id = "form-requisition" method="post" action="<?php echo $url; ?>" class="form-horizontal" autocomplete="off">
        <input type="hidden" id="form_requisition" value="true" name="form_requisition">
        <div class="panel-body">
            <div class="form-group">
                <label class="col form-label">Expected Completion Date</label>
                <div class="col"><input type="text" class="form-control" id="datepicker" name="expected_date" required value = "<?php if (isset($completion_date)) {echo $completion_date;} ?>"
                placeholder=""></div>
            </div>
            
            <div class = "form-group">
                <label class="col form-label">Hub</label>
                <div class = "col">
                            
                <?php  
                    echo "<select class='custom-select btn-block' name='hub'>";   
                    $sql = " SELECT HUB_ID AS value, HUB_NAME AS description FROM TBL_HUB";      
                    if (isset($hub_id)) {
                        gen_select_dropdown($db, $sql, $hub_id);
                    }
                    else {
                        gen_select_dropdown($db, $sql, 0);
                    }
                    echo "</select>";
                ?>   
                </div>
            </div>
            <div class="form-group">
                <label class="col form-label">Notes</label>
                <div class="col">
                    <textarea class="form-control" rows="3" name="requisition_notes"><?php if (isset($notes)) {echo $notes;} ?></textarea>
                </div>
                </div>

                <div class="col">
                    <div class = "row">
                        <div class="form-group">
                            <label class="col form-label">Stock Item Search <span class="text-danger">*</span></label>
                            <div class="col">
                                <div class="autocomplete" style="width:500px;">
                                    <input id="stock-search" type="text" class="form-control" placeholder="LEBQ00103 : WIRE 2.5MM TWIN & EARTH /M"  >
                                </div>
                                <div class="text-light small font-weight-semibold mb-3">You may scan the barcode of the item to add it to the list</div>
                            </div>
                        </div>
                        <div class="form-group" style= "margin-left: 15px; margin-top: 23px;">
                            <button id="btn-stock-add"  class="btn btn-primary btn-stock-add" type="button"  ><i class="fas fa-plus"></i>&nbsp; Add Item </a>
                        </div>
                    </div>
                </div>

            <div class = "form-group">
                <label class="col form-label">Stock Items</label>
                <div class = "col">
                    <table id = "tbl_stock" class="table m-0 font-sm" style="">
                        <thead id = "tbl_stock_head">
                            <tr>
                                <th>Product Code</th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                            </tr>
                            <?php
                                if (isset($ebq_code)) {

                            ?>
                                    <tr id="<?php echo $ebq_code; ?>" class="thead-light larger-item">
                                        <th colspan="4"><strong><?php echo $ebq_code.":".$item_description ?></strong></th>
                                    </tr>
                            <?php
                                }
                            ?>
                        </thead>
                        <tbody id="tbl_stock_body">
                            <input type="text" id="requisition_item" name="requisition_item" hidden value="<?php if (isset($ebq_code)) echo $ebq_code; ?>"/>
                            <input type="text" id="req_no" name="req_no" hidden value="<?php if (isset($req_no)) echo $req_no; ?>"/>
                            <?php 
                            if (isset($sub_items)) {
                                foreach ($sub_items as $item) {
                            ?>
                                <td class="py-3"><?php echo $item['EBQ_CODE']; ?></td>
                                <td class="py-3"><?php echo $item['DESCRIPTION']; ?></td>
                                <td class="py-3"><?php echo $item['METRIC_DESCRIPTION']; ?></td>
                                <td class="py-3"><?php echo $item['QUANTITY']; ?></td>
                                </tr>
                            <?php 
                                };
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <br/>  
            <button type="submit" id="form-submit" class="btn btn-primary"><i class="fas fa-check"></i> <?php if ($action_type == 'create') echo 'Create'; else echo 'Update'; ?> Requisition</button>
        </div>
    </form>
</div>