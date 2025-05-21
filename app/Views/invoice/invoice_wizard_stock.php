<div id="invoice-wizard-step-3" class=" animated fadeIn tab-pane step-content">
    <div id=source-card-container>
        <!-- JS Loads all quotes and voc's as individual cards here -->
    </div>
        <div class="card mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8 pb-4">
                <div class="form-group row">
                    <label class="col-form-label col-sm-3">Discount percentage:</label>
                    <div class="col-sm-2 text-left">
                        <input type="number" onchange="handleDiscountChange();" name="discount" class="form-control"   value="<?php if (isset($discount)) echo $discount; else echo '0.00'; ?>" min="0" max="100" step=".01">
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
                                <td class="pr-3">Discount @ <span id='discount-percentage'>0.00%</span></td>
                                <td class="pr-3" id="discount-total">0.00</td>
                            </tr>
                            <tr>
                                <td class="pr-3">Amount Excl Tax</td>
                                <td class="pr-3" id="tax-excl-amount">0.00</td>
                            </tr>
                            <tr>
                                <td class="pr-3">Tax @ <span id="tax-percentage">15</span>%</td>
                                <td class="pr-3" id="tax-amount">0.00</td>
                            </tr>
                            <tr>
                                <td class="pr-3">Total</td>
                                <td class="pr-3" ><strong class="font-weight-semibold" id="total-amount">0.00</strong></td>
                                <input type="hidden" name="total" value="0.00"/>
                                <input type="hidden" id="sub_total" value="0.00"/>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
