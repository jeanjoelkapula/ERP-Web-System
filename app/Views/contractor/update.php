<?php echo view('_general/header'); ?>

<!-- css style to turn text color of validation messages red -->
<style>
.error {
    color: red;

}
</style>

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
              <span class="text-muted font-weight-light">Contractor /</span> Update
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Contractor Details</h6>
                <div class="card-body">      
                    <?php require_once(APPPATH.'Views/contractor/form_base.php')?>
                </div>
                
            </div>


    </div>
    <!-- Layout content -->

</div>
    

    
<?php echo view('_general/footer_javascript'); ?> 

<script>
   
 


$(document).ready(function() {

// validation for form details
$( "#btn-add-contractor" ).click(function() {


var validator = $("#form_contractor").validate({
    rules: {  
        name: {
            required: true
        },
 
        email:{
            required: true
        }
    },
    messages: {
        name: "Please enter a name.",
        email: "Please enter an email address."
    }      
});
if (validator.form()) {                
        $("#form_contractor").submit();       
}
});  
   
});
</script>

<?php echo view('_general/footer');