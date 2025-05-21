<div class="card mb-4">
    <h6 class="card-header">Journal Details</h6>
    <div class="card-body">      

    <form id = "form-journal" method="post" action="<?php echo $url; ?>" class="form-horizontal" autocomplete="off">
        <input type="hidden" name="form_<?php echo $action_type;?>_journal" value="true" />
        <div class="panel-body">
            <div class="col form-group">
                <label class="form-label">Hub <span class="text-danger">*</span></label>
                        <select id="hub-selector" name='hub_id' class="custom-select btn-block" onchange="handleHubSelection(event)">
                            <?php 
                                $sql = "SELECT HUB_ID as value,HUB_NAME as description FROM TBL_HUB;";
                                if(isset($hub_id)){
                                    gen_select_dropdown($db, $sql,$hub_id);
                                }else{
                                    gen_select_dropdown($db, $sql,0);
                                }
                                ?>
                        </select>
                </div>
            
                <div class="col">
                    <div class = "row">
                        <div class="form-group">
                            <label class="col form-label">Stock Item Search <span class="text-danger">*</span></label>
                            <div class="col">
                                <div class="autocomplete" style="width:500px;">
                                    <input id="stock-search" type="text" class="form-control" placeholder="EBQ00006 : WIRE 2.5MM TWIN & EARTH /M"  >
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
                    <table id = "tbl_stock" class="table m-0 font-sm" >
                        <thead id = "tbl_stock_head">
                            <tr>
                                <th>EBQ Code</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_stock_body">
                            <?php 
                            if (isset($ebq_code)) {
                            ?>
                                <tr>
                                    <td id='<?php echo $ebq_code;?>' class="py-3"><?php echo $ebq_code; ?></td>
                                    <td class="py-3"><?php echo $description; ?></td>
                                    <td class="py-3">
                                        <div class="input-group" style = "width: 170px;">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default" data-type="minus" data-field="stock[<?php echo $ebq_code; ?>][quantity]" onclick="handleUnrestrictedPlusMinusClick(event)">
                                                -
                                                </button>
                                            </span>
                                            <input type="text" id="stock-quantity" name="stock[<?php echo $ebq_code;?>][quantity]" class="form-control"   value="<?php echo $quantity; ?>" min="1" max="100000000000000000000" >
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default" data-type="plus" data-field="stock[<?php echo $ebq_code; ?>][quantity]"  onclick="handleUnrestrictedPlusMinusClick(event)" >
                                                +
                                                </button>
                                            </span>
                                        </div>
                                    </td>

                                    <td id='<?php echo $ebq_code;?>_total' > ZAR <?php echo number_format($cost,2,'.',''); ?> </td>

                                    <input type="hidden" id="<?php echo $ebq_code;?>_hidden" name="stock[<?php echo $ebq_code;?>][total]"  value="<?php echo $cost; ?>">
                                    <input type="hidden" name="stock[<?php echo $ebq_code; ?>][current_quantity]" class="form-control" value="<?php if(isset($current_quantity)) echo $current_quantity;?>">
                                   
                                    <td class="py-3"><button data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove"  >X</span></button></td>
                                </tr>
                            <?php 
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col">
                    <label class="form-label">Notes</label>
                        <textarea class="form-control" rows="3" name="journal_notes"><?php if (isset($notes)) {echo $notes;} ?></textarea>
                </div>   
            </div>

            <br/>  
            <button type="submit" id="form-submit" class="btn btn-primary"><i class="fas fa-check"></i> <?php if ($action_type == 'create') echo 'Create'; else echo 'Update'; ?> Journal Entry</button>
        </div>
    </form>
</div>