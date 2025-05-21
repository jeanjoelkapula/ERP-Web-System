<form id="quote-stock-wizard" autocomplete="off" method="POST" action="<?php echo $url?>" novalidate="novalidate" class="sw-main sw-theme-default" >
   <input type="hidden" name="form_<?php echo $action_type;?>_quote" value="true" />
   <input type="hidden" name="form_enable" value="<?php if(isset($status) && $status != 'PENDING') {echo 'false';} else { echo 'true'; }  ?>"/>
    <ul class="card px-4 pt-3 mb-3 nav nav-tabs step-anchor">
        <li class="nav-item active">
            <a href="#quote-stock-wizard-step-1" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">1</span>
                <div class="text-muted small">FIRST STEP</div>
                Quote Details
            </a>
        </li>
        <li class="nav-item">
            <a href="#quote-stock-wizard-step-2" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">2</span>
                <div class="text-muted small">SECOND STEP</div>
                Quote Stock 
            </a>
        </li>
    </ul>
    <div class="mb-3 tab-content">
        <?php require_once(APPPATH.'Views/quote/wizard_quote_information.php')?>
        <?php require_once(APPPATH.'Views/quote/wizard_quote_stock_information.php')?>
    </div>
</form>