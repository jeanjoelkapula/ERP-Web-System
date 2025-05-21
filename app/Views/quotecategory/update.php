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
              <span class="text-muted font-weight-light">Quote Category /</span> Update
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Category Details</h6>
                <div class="card-body">                          
                    <?php require_once(APPPATH.'Views/quotecategory/formbase.php')?>
                </div>
                
        </div>


    </div>
    <!-- Layout content -->

</div>
    

    
<?php echo view('_general/footer_javascript'); ?> 

<script>
   
 


$(document).ready(function() {
    var $form = $('#quote_category_form');

    // Set up validator
    $form.validate({
        errorPlacement: function errorPlacement(error, element) {
        $(element).parents('.form-group').append(
            error.addClass('invalid-feedback small d-block')
        )
        },
        highlight: function(element) {
        $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
        $(element).removeClass('is-invalid');
        },
        rules: {
        
        }
    });

    $form.on('submit', function(e){
        if (!$form.valid()){ return; }
        $form.submit();
        return false;
    }); 
   
});
</script>

<?php echo view('_general/footer');