<div id="quote-stock-wizard-step-2" class="card animated fadeIn tab-pane step-content">
<h6 class="card-header">Quote Stock</h6>
    <div class="card-body">
        <div class="row">
            <div class = "form-group">

            <label class="col form-label">Source Hub</label>
                <div class = "col">
                    <select id="hub-selector" name='hub_id' class="custom-select btn-block" onchange="handleHubSelection(event)">
                        <?php 
                            $sql = "SELECT HUB_ID as value,HUB_NAME as description FROM TBL_HUB;";
                            if(isset($hub_id)){
                                gen_select_dropdown($db, $sql, $hub_id);
                            }else{
                                gen_select_dropdown($db, $sql, 0);
                            }
                            ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label class="col form-label">Stock Item Search <span class="text-danger">*</span></label>
                <div class="col">
                    <div class="autocomplete" style="width:500px;">
                        <input id="stock-search" type="text" class="form-control" placeholder="EBQ00006 : WIRE 2.5MM TWIN & EARTH /M " >
                    </div>
                    <div class="text-light small font-weight-semibold mb-3">You may scan the barcode of the item to add it to the list</div>
                </div>
            </div>
            <div class = "form-group">
            <label class="col form-label">Stock Category</label>
            <div class = "col">
                <select id="stock-category-selector" class="custom-select btn-block">
                    <?php 
                        $sql = "SELECT ID as value,NAME as description FROM TBL_QUOTE_STOCK_CATEGORY;";
                        gen_select_dropdown($db, $sql, 0);
                        ?>
                </select>
            </div>
        </div>
            <div class="form-group" style= "margin-left: 15px; margin-top: 23px;">
                <button id="btn-stock-add" class="btn btn-primary" type="button"><i class="fas fa-plus"></i>&nbsp; Add Item </a>
            </div>
        </div>
        <div class="row">
        
            <div class="table-responsive mb-4">
                <table class="table m-0 font-sm" id = "tbl_stock">
                    <thead>
                        <tr>
                            <th class="py-3">
                                Code
                            </th>
                            <th class="py-3">
                                Description
                            </th>
                            <th class="py-3">
                                Quantity
                            </th>
                            <th class="py-3">
                                Unit
                            </th>
                            <th class="py-3">
                                Price
                            </th>
                            <th class="py-3">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody  id = "tbl_stock_body">
                        <?php
                            $sql = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY;";
                            $result = $db->query($sql);
                            foreach ($result->getResult('array') as $row) { ?>
                                <tr>
                                    <thead class="thead-light">
                                        <tr  id="stock-category_<?php echo $row['ID']?>">
                                            <th colspan="7"><strong><?php echo $row['NAME']?></strong></th>
                                        </tr>
                                    </thead>
                                </tr>
                                
                           <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="row">
                <div class="col-sm-8 pb-4">
                <div class="form-group row">
                    <label class="col-form-label col-sm-3">PKI Management Fee:</label>
                    <div class="col-sm-2 text-left">
                        <input type="number" onchange="calculateTotals();" name="pki_fee" class="form-control"   value="<?php if (isset($pki_percentage)) echo $pki_percentage; else echo '0.00'; ?>" min="0" max="100" step=".01">
                    </div>
                    <div class="col-sm-7">
                    </div>
                </div>
            </div>        
                <div class="col-sm-4 text-right pb-4">
                    <table class = "table table-borderless">
                        <tbody>
                            <tr>
                                <td class="pr-3">Sub Total</td>
                                <td class="pr-3" id="sub-total">0.00</td>
                            </tr>
                            <tr>
                                <td class="pr-3">PKI Management Fee @ <span id='pki-fee-percentage'>0.00%</span></td>
                                <td class="pr-3" id="pki-fee-total">0.00</td>
                            </tr>
                            <tr>
                                <td class="pr-3">Total</td>
                                <td class="pr-3" ><strong class="font-weight-semibold" id="total-amount">0.00</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
    </div>
</div>
