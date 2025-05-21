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
              <span class="text-muted font-weight-light">Division /</span> Update
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Division Details</h6>
                   <div class="card-body">      
                
                   <form id = "form-division-update" method="post" action="/division/update/<?php if (isset($division_id)) { echo $division_id; }?>" class="form-horizontal" autocomplete="off">
                    <input type="hidden" name="division_update" value="true" />
                    <input type="hidden" name="division_id_var" value="<?php if (isset($division_id)) { echo $division_id; }?>" />
                    <div class="panel-body">
                        
                        <div class="form-group">
                            <label class="col form-label">Division Name</label>
                            <div class="col"><input type="text" class="form-control" name="division_name_var" required="" value="<?php if (isset($division_name)) { echo $division_name; }?>"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col form-label">Division Manager</label>
                            <div class="col"><input type="text" class="form-control" name="division_manager_var" required="" value="<?php if (isset($division_manager)) {echo $division_manager;}?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="col form-label">Division Email</label>
                            <div class="col"><input type="text" class="form-control" name="division_email_var" required="" value="<?php if (isset($division_email)) {echo $division_email;}?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="col form-label">Division Contact Number</label>
                            <div class="col"><input type="text" class="form-control" name="division_number_var" required="" value="<?php if (isset($division_number)) {echo $division_number;}?>"></div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        
                        <br/>  
                        
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Update</button>
                    
                    </div>
                    
                </form>
            
                
                
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

var $form = $('#form-division-update');
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
        division_number_var:{
            "regex":/^[\d ()+-]+$/, // Allows decimals, brackets, plus, dash, spaces
            minlength: 10
        },

        division_email_var:{
            "regex":/^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i,
            minlength: 5
        }
    },
    messages: {
        division_number_var: {
            regex: 'Please enter a valid phone number',
            minlength: 'Please enter a valid phone number'
        },
        
        division_email_var:{
            regex: 'Please use a valid email format',
            minlength: 'Please use valid email'
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