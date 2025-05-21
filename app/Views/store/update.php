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
              <span class="text-muted font-weight-light">Store /</span> Update
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Store Details</h6>
                <div class="card-body">      
                    <?php require_once(APPPATH.'Views/store/form_base.php')?>
                </div>
                
            </div>


    </div>
    <!-- Layout content -->

</div>
    

    
<?php echo view('_general/footer_javascript'); ?> 

<script>
   
 


$(document).ready(function() {


// validation for form details
$("#btn-add-store" ).click(function(e) {    

    // get the date value for opening
    var x = document.getElementById("openingdate").value;

    var y = document.getElementById("maintenancedate").value;

    var maintenanceDate = new Date(y);
    var openingDate = new Date(x)

    console.log(maintenanceDate);
    console.log(openingDate);

    if(maintenanceDate < openingDate)    
    {
        Swal.fire('Error!','Please select a maintenance date after the opening date of the store.','error');
        console.log(x);
        e.preventDefault();  
        $('#maintenancedate').val('');
        return;  
    }    

 // Adds regex validation method
 $.validator.addMethod(
    "regex",
    function(value, element, regexp) {
        return this.optional(element) || regexp.test(value);
    },
    "Please check your input."
    );

var validator = $("#form_store").validate({
    rules: {  
        store_id: {
            required: true
        },
        storename: {
            required: true
        },
        contact:{
            required: true
        },
        storemanager:{
            required: true
        },
        latitude:{
            required: true,
            "regex":/^[N|S|n|s](?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,})?))$/
        },
        longitude:{
            required: true,
            "regex":/^[E|W|e|w](?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,})?))$/
        },
        ffcode:{
            required: true
        },
        openingdate:{
            required: true
        },
        maintenancedate:{
            required: true
        },
        branchsize:{
            required: true
        },
        tradingsize:{
            required: true
        }
    },
    messages: {
        store_id: "Please enter a unique store ID.",
        storename: "Please enter a store name.",
        contact: "Please enter a contact number.",
        storemanager: "Please enter the store manager's name.",
        latitude: { 
            required: "Please enter the latitude of the co-ordinates.",
            regex: 'Must start with N or S and be a valid latitude co-ordinate which is accurate to at least 4 decimal places'
        },
        longitude: {
           required: "Please enter the longitude of the co-ordinates.",
           regex: 'Must start with E or W and be a valid longitude co-ordinate which is accurate to at least 4 decimal places'
        },
        ffcode: "Please enter a FF Code.",
        openingdate: "Please select the opening date.",
        maintenancedate: "Please select the maintenance date.",
        branchsize: "Please enter the square meters of the branch.",
        tradingsize: "Please enter the square meters of the trading size."
    }      
});
    if (validator.form()) {            
        //if (result == 0) {
            $( "#form_store" ).submit();
        //}                
    }
    });   
   
});
</script>

<?php echo view('_general/footer');