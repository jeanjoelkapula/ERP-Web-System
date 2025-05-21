<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<?php echo view('_general/navigation'); ?>
<!-- Layout content -->
<div class="layout-content">
   <!-- Content -->
   <div class="container-fluid flex-grow-1 container-p-y">
      <h4 class="font-weight-bold py-3 mb-4">
         Invoice
      </h4>
      <div class="card">
        <?php require_once(APPPATH.'Views/purchase/purchase_order_print_base.php')?>
        <div class="card-footer text-right">
            <a href="/purchase/printer/<?php echo $purchase_order_id; ?>" target="_blank" class="btn btn-default"><i class="ion ion-md-print"></i>&nbsp; Print</a>
            <button type="button" class="btn btn-primary ml-2"><i class="ion ion-ios-paper-plane"></i>&nbsp; Send</button>
        </div>
      </div>
   </div>
   <!-- / Content -->
</div>
<!-- Layout content -->
<?php echo view('_general/footer_javascript'); ?> 
<?php echo view('_general/footer');