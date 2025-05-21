<form id="packing-bill-wizard" autocomplete="off" method="POST" action="<?php echo $url?>" novalidate="novalidate" class="sw-main sw-theme-default" >
   <input type="hidden" name="form_<?php echo $action_type;?>_packing" value="true" />
    <ul class="card px-4 pt-3 mb-3 nav nav-tabs step-anchor">
        <li class="nav-item active">
            <a href="#packing-bill-wizard-step-1" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">1</span>
                <div class="text-muted small">FIRST STEP</div>
                Order Details
            </a>
        </li>
        <li class="nav-item">
            <a href="#packing-bill-wizard-step-2" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">2</span>
                <div class="text-muted small">SECOND STEP</div>
                Delivery Details 
            </a>
        </li>
        <li class="nav-item">
            <a href="#packing-bill-wizard-step-3" class="mb-3 nav-link">
                <span class="sw-done-icon ion ion-md-checkmark"></span>
                <span class="sw-number">2</span>
                <div class="text-muted small">THIRD STEP</div>
                Stock Details 
            </a>
        </li>
    </ul>
    <div class="mb-3 tab-content">
    <div id="packing-bill-wizard-step-1" class="card animated fadeIn">
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <h4>Order Selection</h4>
                    <br/>
                </div>
                <div class="col-6">
                    <?php if(isset($order_no)){ ?>
                            <input type="text"  id="order_id" class="form-control required" name="order_no" readonly value="<?php if(isset($order_no)) echo $order_no;?>">
                   <?php }  
                    else { ?>
                    <select class="custom-select btn-block select2" name="order_no" id="order_id" <?php if(isset($order_no)) echo "readonly";?>>
                        <?php 
                            $sql = "SELECT o.ORDER_NO AS value, o.ORDER_NO AS description FROM TBL_ORDER_INTERNAL o
                            LEFT JOIN TBL_PACKING_BILL pb ON pb.INTERNAL_ORDER_NO = o.ORDER_NO
                            WHERE (pb.ORDER_NO) IS NULL AND (o.STATUS = 'APPROVED');";
                            gen_select_dropdown($db,$sql,0);

                            $sql = "SELECT o.ORDER_NO AS value, o.ORDER_NO AS description FROM TBL_ORDER o 
                            LEFT JOIN TBL_PACKING_BILL pb ON pb.ORDER_NO = o.ORDER_NO
                            WHERE (pb.ORDER_NO) IS NULL AND (o.STATUS = 'APPROVED');"; 
                            gen_select_dropdown($db,$sql,0);

                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
        <?php require_once(APPPATH.'Views/packing/packing_wizard_delivery.php')?>
        <?php require_once(APPPATH.'Views/packing/packing_wizard_stock.php')?>
    </div>
</form>