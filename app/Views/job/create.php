

<?php echo view('_general/header'); ?>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-2">
    <div class="layout-inner">
        <?php echo view('_general/navigation'); ?>
        <!-- Layout container -->
        <div class="layout-container">
            <?php echo view('_general/navigation_top'); ?>
            <!-- Layout content -->
            <div class="layout-content">
                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y pt-0">
                    <h4 class="font-weight-bold py-3 mb-4">
                        
                    </h4>

                    <form id="form_create_job" autocomplete="off" method="POST" action="/job/create" novalidate="novalidate" class="sw-main sw-theme-default">
                        <input type="hidden" name="form_create" value="true"/>
                
                        <div id="smartwizard-2" class="smartwizard-example">
                            <ul>
                                <li>
                                    <a href="#smartwizard-2-step-1" class="mb-3">
                                        <span class="sw-done-icon ion ion-md-checkmark"></span>
                                        <span class="sw-icon ion ion-ios-keypad"></span>
                                        Order Selection
                                        <div class="text-muted small">Select An Order</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#smartwizard-2-step-2" class="mb-3">
                                        <span class="sw-done-icon ion ion-md-checkmark"></span>
                                        <span class="sw-icon ion ion-ios-color-wand"></span>
                                        Order Details
                                        <div class="text-muted small">Confirm Order Details & Stock</div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#smartwizard-2-step-3" class="mb-3">
                                        <span class="sw-done-icon ion ion-md-checkmark"></span>
                                        <span class="sw-icon ion ion-md-copy"></span>
                                        Job Details
                                        <div class="text-muted small">Enter Job Details</div>
                                    </a>
                                </li>
                            </ul>
                            <div class="mb-3">
                                <div id="smartwizard-2-step-1" class="card animated fadeIn">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <h4> Select Order </h4>
                                                <br>
                                                <select class="custom-select btn-block" name="order_no" id="order_no">
                                                <?php 
                                                    $sql = "select o.ORDER_NO as value, o.ORDER_NO as description from TBL_ORDER o
                                                    where o.STATUS = 'APPROVED' AND (o.ORDER_NO NOT IN (select j.ORDER_NO from TBL_JOB j));
                                                            "; 
                                                    gen_select_dropdown($db,$sql,0);
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <h4> Job Type</h4>
                                                <br>
                                                <select class="custom-select btn-block" name="job_type_id" id="job_type_id">
                                                <?php 
                                                    $sql = "select JOB_TYPE_ID as value, JOB_TYPE_DESCRIPTION as description from TBL_JOB_TYPE where ACTIVE = 1";
                                                    gen_select_dropdown($db,$sql,0);
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="smartwizard-2-step-2" class="card animated fadeIn">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-4">
                                                <h4> Order Details </h4>
                                                <br>
                                                <div class="form-group">
                                                    <label class="col form-label">Order Notes</label>
                                                    <div class="col"><textarea type="text" readonly class="form-control" id="order_notes" required="" value=""></textarea></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col form-label">Order Date Created</label>
                                                    <div class="col"><input type="text" readonly class="form-control" id="order_date_created" required="" value=""/></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col form-label">Order Created By</label>
                                                    <div class="col"><input type="text" readonly class="form-control" id="order_created_by" required="" value=""/></div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h4>Order Stock</h4>
                                                <br>
                                                <div class="table-responsive mb-4">
                                                    <table class="table m-0" id = "tbl_stock">
                                                        <thead>
                                                            <tr>
                                                                <th class="py-3">
                                                                    Code
                                                                </th>
                                                                <th class="py-3">
                                                                    Description
                                                                </th>
                                                                <th class="py-3">
                                                                    Price
                                                                </th>
                                                                <th class="py-3">
                                                                    Quantity
                                                                </th>
                                                                <th class="py-3">
                                                                    Total
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody  id = "tbl_stock_body">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="smartwizard-2-step-3" class="card animated fadeIn">
                                    <div class="card-body">
                                        <h4> Job Details </h4>
                                        <hr/>
                                        <div class="form-group">
                                            <label class="col form-label">Job Notes</label>
                                            <div class="col"><textarea type="text" class="form-control" name="job_notes" required="" value=""></textarea><small><i>Any additional job notes.</i></small></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col form-label">Job Level</label>
                                            <div class="col">
                                                <select name="job_level" id="job_level">
                                                    <!-- TODO: TEMPORARY OPTIONS UNTIL WE GET CLARIFICATION ON LEVELS -->
                                                    <option value="1">Level 1 - 24 Hour Response</option>
                                                    <option value="2">Level 2 - 2 Week Response</option>
                                                    <option value="3">Level 3 - Under A Month Response</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button class="float-center btn btn-outline-primary" type="submit">Create Job</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                    </div>
                </div>
                <!-- / Content -->
            </div>
            <!-- Layout content -->
        </div>
        <!-- / Layout container -->
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-sidenav-toggle"></div>
</div>
<!-- / Layout wrapper -->
<?php echo view('_general/footer_javascript'); ?>
<script>
    function get_order_details(){
        let order_no = $('#order_no').val();
    
    	$.post("/job/create/ajax/get_order_details/", {order_no : order_no},
    		function(result){
    			var obj = JSON.parse(result);
    			
    			$('#order_notes').val(obj[0].ORDER_NOTES);
                $('#order_date_created').val(obj[0].ORDER_DATE_CREATED);
                $('#order_created_by').val(obj[0].first_name + ' '+obj[0].last_name);
    			
    	})
        get_order_stock();
    }
    
    function get_order_stock(){
       
    	let order_no = $('#order_no').val()
    	$.post("/job/create/ajax/get_order_stock/", {order_no:order_no},
    	function(result){
    		var quoteStock = JSON.parse(result);
    		
    		Object.entries(quoteStock).forEach(entry => {
    			const [key, value] = entry;
    
    			row = document.createElement('tr');
    			var itemCat = value.STOCK_CATEGORY;
    			var stockrow = $(`#stock-category_${itemCat}`);
    			var tbl_stock_body = document.querySelector('#tbl_stock_body');
    			row.innerHTML = `
    				<td class="py-3">${value.EBQ_CODE}</td>
    				<td class="py-3">${value.DESCRIPTION}</td>
    				<td class="py-3">R${value.AVG_COST}</td>
    				<td class="py-3">
    				<div class="input-group" style = "width: 170px;">
    						
    				<input type="text" name="stock[${value.EBQ_CODE}][quantity]" class="form-control quant input-number" readonly value="${value.QUANTITY}" min="1" max="5">
    						
    					</div>
    				</td>
    				<td id='#${value.EBQ_CODE}'class="py-3">R${value.QUANTITY * parseFloat(value.AVG_COST)}</td>
    
    				
    			`;
    				
    				
    				tbl_stock_body.append(row);
    				
    		})
    		
    
    	})
    	
    
    }
    $(document).ready(function() {
        $('#order_no').select2();
        $("textarea").not(".allowemoji").keyup(function(){

        var strng = $(this).val();

        var cleanStr = removeEmojis(strng);

        $(this).val(cleanStr);

        });


        $("input").not(".allowemoji").keyup(function(){

        var strng = $(this).val();

        var cleanStr = removeEmojis(strng);

        $(this).val(cleanStr);

        });

    	$('.smartwizard-example').smartWizard({
    		autoAdjustHeight: false,
    		backButtonSupport: false,
    		useURLhash: false,
    		showStepURLhash: false,
    		toolbarSettings : {toolbarPosition: 'both'},
    	});
    
        $('#job_level').select2();
    	$('.smartwizard-example').on("leaveStep", function(e, anchorObject, stepNumber, stepDirection){
    	
    
    		if(stepNumber == 0 && stepDirection == "forward" ){
    			get_order_details();
    			
    		} else if (stepNumber == 1 && stepDirection == 'forward'){
    			
    		}
    	});
    
    
    	
    
    	$('#content_wrapper_loading').hide();
    	$('#content_wrapper').show();      
    	
    
    	
    
    });
</script>
<?php echo view('_general/footer'); ?>

