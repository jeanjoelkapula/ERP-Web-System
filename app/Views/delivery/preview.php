<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<?php echo view('_general/navigation'); ?>

<div class="layout-container">

<?php echo view('_general/navigation_top'); ?>


<!-- Layout content -->
<div class="layout-content">
   <!-- Content -->
   <div class="container-fluid flex-grow-1 container-p-y">
      <h4 class="font-weight-bold py-3 mb-4">
      <span class="text-muted font-weight-light">Delivery Note /</span> Preview
      </h4>
      <div class="card">
        <?php require_once(APPPATH.'Views/delivery/delivery_base.php')?>
        <div class="card-footer text-right">

            <a href="/delivery/printer/<?php echo $entity_id; ?>" target="_blank" class="btn btn-default"><i class="ion ion-md-print"></i>&nbsp; Print</a>
            
        </div>
      </div>
   </div>
   <!-- / Content -->
</div>
<!-- Layout content -->
</div>
<?php echo view('_general/footer_javascript'); ?> 
<?php echo view('_general/footer');