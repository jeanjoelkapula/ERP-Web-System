<form id="purchase-order-wizard" autocomplete="off" method="POST" action="<?php echo $url?>" novalidate="novalidate" class="sw-main sw-theme-default" >
   <input type="hidden" name="form_<?php echo $action_type;?>" value="true"/>
   <input type="hidden" class="form_enable" name="form_enable" value="<?php if(isset($approval_status)) {if ($approved || $declined) {echo 'false';} else { echo 'true'; }}else {echo 'true'; }  ?>"/>
    <ul class="card px-4 pt-3 mb-3 nav nav-tabs step-anchor">
        <li class="nav-item active">
            <a href="#purchase-order-wizard-step-1" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">1</span>
                <div class="text-muted small">FIRST STEP</div>
                Purchase Order Information
            </a>
        </li>
        <li class="nav-item">
            <a href="#purchase-order-wizard-step-2" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">2</span>
                <div class="text-muted small">SECOND STEP</div>
                Purchase Order Stock 
            </a>
        </li>
    </ul>
    <div class="mb-3 tab-content">
        <?php require_once(APPPATH.'Views/purchase/wizard_order_information.php')?>
        <?php require_once(APPPATH.'Views/purchase/wizard_order_stock.php')?>
    </div>
</form>