<?php echo view('_general/header'); ?>

<!-- css style to turn text color of validation messages red -->
<style>
.error {
    color: red;

}

.autocomplete-items {
    max-height: 200px; 
    overflow-y: scroll;
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
                <span class="text-muted font-weight-light">Delivery Notes /</span> Create
            </h4>
            <div class="card mb-4">
              
              <h6 class="card-header">Delivery Note Details</h6>
                 <div class="card-body">      
              
                 <?php require_once(APPPATH.'Views/delivery/delivery_form_base.php')?>                                                                          
                 </div>
          </div>
   </div>
   <!-- / Content -->
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

        // declare a variable for the packing bill date
        var packingBillDate;

        // declare a list for added stock
        var addedOrder = 0;                    

        //handle on add item button click
        $('#btn-order-add').click(function() {

            addedOrder = addedOrder + 1;
            
            var input = $('#orderSearch').val();                                          

            var splitInput = input.split(' : ');
            var order = splitInput[0];
            var date = splitInput[1];    

            console.log(date);

            // assign he packing bill date variable
            packingBillDate = date;                    

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

            // check if the user has added an order to the delivery note
            x = document.getElementById("tbl_order").rows.length;
            console.log(x);
            if(x == 1)
            {                            
                Swal.fire('Error!','Please select and add a packing bill for the Delivery Note using "Packing Bill Search *".','error');
                console.log(x);
                e.preventDefault();  
                return;                     
            }


            // get the date value for the delivery note
            var x = document.getElementById("maintenancedate").value;

            var packingBill = new Date(packingBillDate);
            var deliveryNote = new Date(x)

            console.log(packingBill);
            console.log(deliveryNote);

            if(deliveryNote < packingBill)    
            {
                Swal.fire('Error!','Please select a delivery date after the packing bill date.','error');
                console.log(x);
                e.preventDefault();  
                $('#maintenancedate').val('');
                return;  
            }

            // validate the form before submission
            var validator = $("#form_delivery").validate({
                rules: {                          
                    maintenancedate:{
                        required: true
                    },
                    deliverymethod:{
                        required: true
                    },
                    waybill_number:{
                        required: true
                    }
                },
                messages: {                      
                    maintenancedate: "Please enter the delivery date for the delivery note.",
                    deliverymethod: "Please enter the delivery method for the delivery note.",
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