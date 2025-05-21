<form id="invoice-wizard" autocomplete="off" method="POST" action="<?php echo $url?>" novalidate="novalidate" class="sw-main sw-theme-default" >
   <input type="hidden" name="form_<?php echo $action_type;?>_invoice" value="true" />
    <ul class="card px-4 pt-3 mb-3 nav nav-tabs step-anchor">
        <li class="nav-item active">
            <a href="#invoice-wizard-step-1" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">1</span>
                <div class="text-muted small">FIRST STEP</div>
                 Order Details
            </a>
        </li>
        <li class="nav-item">
            <a href="#invoice-wizard-step-2" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">2</span>
                <div class="text-muted small">SECOND STEP</div>
                Account Details 
            </a>
        </li>
        <li class="nav-item">
            <a href="#invoice-wizard-step-3" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">3</span>
                <div class="text-muted small">THIRD STEP</div>
                Invoice Items
            </a>
        </li>
    </ul>
    <div class="mb-3 tab-content">
    <div id="invoice-wizard-step-1" class="card animated fadeIn">
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <h4>Order Selection</h4>
                    <br/>
                </div>
                <div class="col-6">
                    <select class="custom-select btn-block select2" name="order_no" id="order_id">
                        <?php 
                        $sql = "SELECT o.ORDER_NO AS value, o.ORDER_NO AS description from TBL_ORDER o
                        LEFT JOIN TBL_INVOICE i ON i.ORDER_NO = o.ORDER_NO
                        WHERE (i.ORDER_NO) IS NULL AND (o.STATUS = 'APPROVED')"; 
                        gen_select_dropdown($db,$sql,0);
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
        <?php require_once(APPPATH.'Views/invoice/invoice_wizard_info.php')?>
        <?php require_once(APPPATH.'Views/invoice/invoice_wizard_stock.php')?>
    </div>
</form>