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
                        <span class="text-muted font-weight-light">Stock /</span> Create
                    </h4>

                    <div class="card mb-4">

                        <h6 class="card-header">Stock Item Details</h6>
                        <div class="card-body">

                            <?php require_once(APPPATH . 'Views/stock/stock_form_base.php') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo view('_general/footer_javascript'); ?>

            <script>
                $(document).ready(function() {

                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    <?php if (isset($ebq_code)) { ?>
                        stockItemID = '<?php echo $ebq_code; ?>';
                    <?php } ?>
                    
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    var tbl_stock = document.querySelector('#tbl_stock_body');

                    //global variables storing json stock objects returned from autocomplete search
                    var stock = [];

                    //global variable storing json objects already in the stock table
                    var addedStock = [];

                    var addedEBQ = [];

                    var addedEBQandStock = [];

                    //add stock loaded from the db
                    <?php
                    // $sql = "SELECT DESCRIPTION FROM TBL_STOCK;";
                    $sql = "SELECT EBQ_CODE, DESCRIPTION FROM TBL_STOCK WHERE IS_ACTIVE = 1 AND IS_BUILT = 0;";
                    $stockResult = $db->query($sql);
                    foreach ($stockResult->getResult('array') as $row) : {
                    ?>
                            addedStock.push("<?php echo $row['DESCRIPTION']; ?>");

                            addedEBQ.push("<?php echo $row['EBQ_CODE']; ?>");

                            addedEBQandStock.push("<?php echo $row['EBQ_CODE'] . " : " . $row['DESCRIPTION']; ?>");
                    <?php
                        }
                    endforeach;
                    ?>                    

                    var items = addedStock;

                    // dump the data for searching
                    autocomplete(document.getElementById("stockSearch"), addedEBQandStock);

                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    // determine whether to toggle the sub item list, if it's checked, initiate with the sub item list
                    if (document.querySelector('#is_built').checked == true) {
                    toggleFieldGroup();                        
                    }
                                      
                    //function to hide and show built field group based on whether is built is checked
                    //if item is built, the item will default values for its field. e.g purchase will 0 because we count purchase cost of sub items
                    function toggleFieldGroup() {
                        if (document.querySelector('#is_built').checked == true) {
                            $(".built_stock_group").toggle();
                            $(".not_built_field_group").toggle();
                        } else {
                            $(".not_built_field_group").toggle();
                            $(".built_stock_group").toggle();
                        }
                    }

                    //toggle from group on checkbox value change
                    $('#is_built').change(function() {
                        toggleFieldGroup();
                    });

                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
                    // declare a list for added stock
                    var addedStocks = [];                    

                    //handle on add item button click
                    $('#btn-stock-add').click(function() {

                        var input = $('#stockSearch').val();

                        // determine if the stock item is already in the table                        
                        var isInStockListAlready = addedStocks.includes(input);                        
                        
                        // add to the array of added stock items
                        addedStocks.push(input);                        

                        var splitInput = input.split(' : ');
                        var ebq = splitInput[0];
                        var description = splitInput[1];                        

                        index = items.indexOf(description);                                                

                        row = document.createElement('tr');

                        row.innerHTML = `
                        <td class="py-3">${addedEBQ[index]}</td>
                        <td class="py-3">${addedStock[index]}</td>
                        <td class="py-3">
                        <div class="input-group" style = "width: 170px;">
	                        <span class="input-group-btn">
	                                <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="create-stock[${addedEBQ[index]}]" onclick='handlePlusMinusClick(event)'>
	                                -
	                                </button>
	                            </span>
	                            <input type="text" name="create-stock[${addedEBQ[index]}]" class="form-control" 
                                value="<?php if (isset($stockQuantity)) { echo $stockQuantity; } else { echo 1; } ?>" min="1" max="99999" onchange='handleInputChange(event.target)' 
                                onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>                            
	                            <span class="input-group-btn">
	                                <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="create-stock[${addedEBQ[index]}]" onclick='handlePlusMinusClick(event)'>
	                                +
	                                </button>
	                         </span>
	                    </div>
                        </td>
                        <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove">X</span></a></td>
                        `;
                        if (index > -1 && !isInStockListAlready) {                            
                            tbl_stock.append(row);
                            $('#stockSearch').val('');
                        } else if (isInStockListAlready) {
                            Swal.fire('Error!','The stock item entered has already been added to the stock list below.','error');
                            $('#stockSearch').val('');
                        }
                        else {
                            Swal.fire('Error!','The stock item entered does not exist.','error');
                        }
                    });

                    $("#tbl_stock").on('click', '.btn-remove', function() {
                        $(this).closest('tr').remove();
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

                        // check if the user has added sub stock items if they have toggled the sub stock item option
                        if (document.querySelector('#is_built').checked == true) {
                            x = document.getElementById("tbl_stock").rows.length;
                            console.log(x);
                            if(x == 1)
                            {                            
                                Swal.fire('Error!','Please select and add at least one sub stock item for the stock item using "Stock Item Search *" or click the slider "Is the item built out of other stock items?" to indicate that it is a single item.','error');
                                console.log(x);
                                e.preventDefault();  
                                return;                     
                            }
                        }

                        var validator = $("#form_stock").validate({
                            rules: {  
                                ebq_code: {
                                    required: true
                                },
                                stock_description: {
                                    required: true
                                },
                                purchase_cost:{
                                    required: true
                                },
                                markup:{
                                    required: true
                                },
                                wastage:{
                                    required: true
                                },
                                minreorder:{
                                    required: true
                                }
                            },
                            messages: {
                                ebq_code: "Please enter a valid code.",
                                stock_description: "Please enter a stock description.",
                                purchase_cost: "Please enter a purchase cost (minimum of zero).",
                                markup: "Please enter the markup percentage (0-100) for the stock item.",
                                wastage: "Please enter the wastage percentage (0-100) for the stock item.",
                                minreorder: "Please enter the minimum re-order amount for the stock item (minimum of zero)."
                            }      
                        });
                        if (validator.form()) {                                    
                            $("#form_stock").submit();                        
                        }
                    });        

                });
            </script>

            <?php echo view('_general/footer');
