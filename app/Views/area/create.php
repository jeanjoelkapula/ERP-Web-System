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
              <span class="text-muted font-weight-light">Area /</span> Create
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Area Details</h6>
                <div class="card-body">                          
                    <?php require_once(APPPATH.'Views/area/form_base.php')?>
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

    var $form = $('#form-area-base');
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
            contact_number:{
                "regex":/^[\d ()+-]+$/, // Allows decimals, brackets, plus, dash, spaces
                minlength: 10
            }
        },
        messages: {
            contact_number: {
                regex: 'Please enter a valid phone number',
                minlength: 'Please enter a valid phone number'
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