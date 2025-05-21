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
              <span class="text-muted font-weight-light">Hub /</span> Update
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Hub Details</h6>
                <div class="card-body">                          
                    <?php require_once(APPPATH.'Views/hub/form_base.php')?>
                </div>
                
        </div>


    </div>
    <!-- Layout content -->

</div>
    

    
<?php echo view('_general/footer_javascript'); ?> 
<script>
   
 


$(document).ready(function() {

    // Adds regex validation method
    $.validator.addMethod(
    "regex",
    function(value, element, regexp) {
        return this.optional(element) || regexp.test(value);
    },
    "Please check your input."
    );

    var $form = $('#form-hub-base');
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
            hub_longitude:{
                required:true,
                "regex":/^[E|W|e|w](?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,})?))$/
            },
            hub_latitude:{
                required:true,
                "regex":/^[N|S|n|s](?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,})?))$/
            }
        },
        messages: {
            hub_longitude: {
                required: 'This field is required',
                regex: 'Must start with E or W and be a valid longitude co-ordinate which is accurate to at least 4 decimal places'
            },
            hub_latitude: {
                required: 'This field is required',
                regex: 'Must start with N or S and be a valid latitude co-ordinate which is accurate to at least 4 decimal places'
            }
        }
    });

    $form.on('submit', function(e){
        if (!$form.valid()){ 
            e.preventDefault(); 
            return; 
        }
        return true;
    })
   
});
</script>

<?php echo view('_general/footer');