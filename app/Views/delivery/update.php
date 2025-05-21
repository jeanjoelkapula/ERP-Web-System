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
              <span class="text-muted font-weight-light">Delivery Note /</span> Update
            </h4>
         
            <div class="card mb-4">
              
                <h6 class="card-header">Delivery Note Details</h6>
                <div class="card-body">      
                <?php require_once(APPPATH.'Views/delivery/delivery_form_base.php')?>       
                </div>
                
            </div>


    </div>
    <!-- Layout content -->

</div>
    

    
<?php echo view('_general/footer_javascript'); ?> 

<script>
$(document).ready(function() {

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////               
    
    var tbl_order = document.querySelector('#tbl_order_body');

    //global variables storing json stock objects returned from autocomplete search
    var order = [];

    //global variable storing json objects already in the stock table
    var addedOrder = [];

    var addedDate = [];

    var addedOrderAndDate = [];

    //add stock loaded from the db
    <?php
    // $sql = "SELECT DESCRIPTION FROM TBL_STOCK;";
    $sql = "SELECT PACKING_BILL_ID,DATE(CREATED_DATE) AS ORDER_DATE FROM TBL_PACKING_BILL
    WHERE PACKING_BILL_ID NOT IN (SELECT PACKING_BILL_ID FROM TBL_DELIVERY_NOTE);";
    $stockResult = $db->query($sql);
    foreach ($stockResult->getResult('array') as $row) : {
    ?>
            addedOrder.push("<?php echo $row['PACKING_BILL_ID']; ?>");

            addedDate.push("<?php echo $row['ORDER_DATE']; ?>");

            addedOrderAndDate.push("<?php echo $row['PACKING_BILL_ID'] . " : " . $row['ORDER_DATE']; ?>");
    <?php
        }
    endforeach;
    ?>                    

    var items = addedOrder;

    // dump the data for searching
    autocomplete(document.getElementById("orderSearch"), addedOrderAndDate);

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // declare a list for added stock
    var addedOrder = 1;                    

    //handle on add item button click
    $('#btn-order-add').click(function() {

        addedOrder = addedOrder + 1;
        
        var input = $('#orderSearch').val();                                          

        var splitInput = input.split(' : ');
        var order = splitInput[0];
        var date = splitInput[1];                        

        index = items.indexOf(order);                                                

        row = document.createElement('tr');                        

        row.innerHTML = `
        <td class="py-3">${order}</td>
        <input type="hidden" name="create-delivery" value="${order}">
        <td class="py-3">${date}</td>                        
        <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove">X</span></a></td>
        `;
        if (index > -1 && addedOrder == 1) {                            
            tbl_order.append(row);
            $('#orderSearch').val('');
        } else if (addedOrder != 1) {                            
            Swal.fire('Error!','A packing bill has already been added, please remove the existing one before trying to add a different packing bill.','error');
            $('#orderSearch').val('');
        }
        else {                            
            Swal.fire('Error!','The packing bill entered does not exist.','error');
            addedOrder = 0;
        }
    });

    $("#tbl_order").on('click', '.btn-remove', function() {
        $(this).closest('tr').remove();
        addedOrder = 0;
        $('#orderSearch').val('');
    });

                        
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // ensure that there are no duplicates of select dropdowns
    var map = {};
    $('select option').each(function() {
        if (map[this.value]) {
            $(this).remove()
        }
        map[this.value] = true;
    });    

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // validation for form details
    $( "#btn-add-all" ).click(function(e) {

        // check if the user has added an order

            x = document.getElementById("tbl_order").rows.length;

        var validator = $("#form_delivery").validate({
            rules: {                          
                delivery_date:{
                    required: true
                },
                waybill_number:{
                    required: true
                }
            },
            messages: {                      
                delivery_date: "Please enter the delivery date for the delivery note.",
                waybill_number: "Please enter the waybill number / code for the delivery note."
            }      
        });
        if (validator.form()) {                                    
            $("#form_delivery").submit();                        
        }
    });        

});
</script>

<?php echo view('_general/footer');