<div id="purchase-order-wizard-step-2" class="card animated fadeIn tab-pane step-content">
    <div class="card-body">
        <div class="col">
            <div class = "row">
                <div class="form-group">
                    <label class="col form-label">Stock Item Search <span class="text-danger">*</span></label>
                    <div class="col">
                        <div class="autocomplete" style="width:500px;">
                            <input id="stock-search" type="text" class="form-control" placeholder="EBQ00006 : WIRE 2.5MM TWIN & EARTH /M " >
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
                <table id = "tbl_stock" class="table table-striped font-sm" style="display:">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Description</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="tbl_stock_body">
                        <?php 
                        if (isset($order_stock)) {
                            foreach ($order_stock as $item) {
                        ?>
                            <tr>
                                <td id='<?php echo $item['EBQ_CODE']; ?>' class="py-3"><?php echo $item['EBQ_CODE']; ?></td>
                                <td class="py-3"><?php echo $item['DESCRIPTION']; ?></td>
                                <td class="py-3"><?php echo $item['METRIC_DESCRIPTION']; ?></td>
                                <td class="py-3">
                                    <div class="input-group" style = "width: 170px;">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="stock[<?php echo $item['EBQ_CODE']; ?>][quantity]">
                                            -
                                            </button>
                                        </span>
                                        <input type="text" name="stock[<?php echo $item['EBQ_CODE']; ?>][quantity]" class="form-control input-number"   value="<?php echo $item['QUANTITY']; ?>" min="1" max="100000000000000000000" onchange="updateAmount('<?php echo $item['EBQ_CODE']; ?>');">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[<?php echo $item['EBQ_CODE']; ?>][quantity]"  >
                                            +
                                            </button>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="">
                                                <input type="number" name="stock[<?php echo $item['EBQ_CODE']; ?>][price]" onchange="updateAmount('<?php echo $item['EBQ_CODE']; ?>');" class="form-control"   value="<?php echo $item['UNIT_PRICE']; ?>" min="1" step=".01">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td id = "<?php echo $item['EBQ_CODE']; ?>_amount" name="item_amount">
                                    <?php echo $item['AMOUNT']; ?>
                                </td>
                                <input type="number" id="stock[<?php echo $item['EBQ_CODE']; ?>][amount]" name="stock[<?php echo $item['EBQ_CODE']; ?>][amount]" class="form-control" value="<?php echo $item['AMOUNT']; ?>" hidden>
                                <td class="py-3"><button data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove"  >X</span></button></td>
                            </tr>
                        <?php 
                            };
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col">
                <div class="form-group row">
                    <label class="col-form-label col-sm-10 text-sm-right">Misc Chargers:</label>
                    <div class="col-sm-2">
                        <input type="number" onchange="updateOrderTotal();" id="misc_charges" name="misc_charges" class="form-control"   value="<?php if (isset($misc_charges)) echo $misc_charges; else echo '0'; ?>" min="0" step=".01">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group row">
                    <label class="col-form-label col-sm-10 text-sm-right">Freight Chargers:</label>
                    <div class="col-sm-2">
                        <input type="number" onchange="updateOrderTotal();" id = "freight_charges" name="freight_charges" class="form-control"   value="<?php if (isset($freight_Charges)) echo $freight_Charges; else echo '0'; ?>" min="0" step=".01">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group row">
                    <label class="col-form-label col-sm-10 text-sm-right">Order Total:</label>
                    <div class="col-sm-2">
                        <label id="order_total" class="" style="padding-top: 8px;">ZAR <?php if (isset($po_amount)) echo $po_amount; else echo '0'; ?></label>
                    </div>
                    <input type="number"id = "po_amount" name="po_amount" class="form-control" value="<?php if (isset($po_amount)) echo $po_amount; else echo '0'; ?>" min="0" hidden>
                </div>
            </div>
        </div>
    </div>
</div>
