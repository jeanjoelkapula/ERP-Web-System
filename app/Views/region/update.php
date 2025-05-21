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
              <span class="text-muted font-weight-light">Region /</span> Update
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Region Details</h6>
                   <div class="card-body">      
                
                <form id = "form-region-update"method="post" action="/region/update/<?php echo $region_id?>" class="form-horizontal" autocomplete="off">
                    <input type="hidden" name="form_update_region" value="true" />
                    <input type="hidden" name="region_id_var" value=<?php echo $region_id?> />
                    <div class="panel-body">
                        
                        <div class="form-group">
                            <label class="col form-label">Region Name</label>
                            <div class="col"><input type="text" class="form-control" name="region_name" required="" value="<?php if(isset($region_name)) echo $region_name;?>"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col form-label">Region Manager</label>
                            <div class="col"><input type="text" class="form-control" name="region_manager" required="" value="<?php if(isset($region_manager))echo $region_manager;?>"></div>
                        </div>


                        <div class="form-group">
                            <label class="col form-label">Region Email</label>
                            <div class="col"><input type="text" class="form-control" name="region_email" value="<?php if(isset($region_email))echo $region_email;?>"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col form-label">Region Contact Number</label>
                            <div class="col"><input type="text" class="form-control" name="region_number" value="<?php if(isset($region_number))echo $region_number;?>"></div>
                        </div>

                        <div class = "form-group">
                            <label class="col form-label">Division</label>
                            <div class = "col">  
                                <?php 
                                    echo "<select class='custom-select btn-block' name='division_id'>"; 
                                        $sql = " SELECT DIVISION_ID AS value, DIVISION_NAME AS description FROM TBL_DIVISION";
                                        gen_select_dropdown($db, $sql, $division_id);
                                    echo "</select>";
                                ?>
                        </div>
                        <div class="hr-line-dashed"></div>
                        
                        <br/>  
                        
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i>Update</button>
                    
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

    var $form = $('#form-region-update');
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
            region_number:{
                "regex":/^[\d ()+-]+$/, // Allows decimals, brackets, plus, dash, spaces
                minlength: 10
            },

            region_email:{
                "regex":/^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i,
                minlength: 5
            }
        },
        messages: {
            region_number: {
                regex: 'Please enter a valid phone number',
                minlength: 'Please enter a valid phone number'
            },
            
            region_email:{
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