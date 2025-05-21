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
              <span class="text-muted font-weight-light">Region ID '<?php echo $ent_id?>' does not exist</span> 
            </h4>
        </div>

    </div>

</div>

<?php echo view('_general/footer_javascript'); ?> 

<?php echo view('_general/footer');   