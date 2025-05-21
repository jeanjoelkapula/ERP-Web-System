<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<!-- Layout content -->
    <?php require_once(APPPATH.'Views/delivery/delivery_base.php')?>

<!-- Layout content -->
<script>
  // -------------------------------------------------------------------------
  // Print on window load

  $(function () {
    window.print();
  });
</script>
<?php echo view('_general/footer_javascript'); ?> 
<?php echo view('_general/footer');