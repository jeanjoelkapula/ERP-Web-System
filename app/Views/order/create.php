

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
				<div id="content_wrapper_loading" style="text-align:center; padding:150px;">
					<i class="far fa-5x fa-spinner-third fa-spin"></i>
					<h4 class="mt-5">loading...</h4>
				</div>
				<form id="order-stock-wizzard" autocomplete="off" method="POST" action="/order/create" novalidate="novalidate" class="sw-main sw-theme-default">
				<input type="hidden" name="form_create_order" id="form_create_order" value="true" />
				
				<div class="card-body">
					<div class="demo-vertical-spacing" id="content_wrapper" style="display:none">
						<div id="smartwizard-2" class="smartwizard-example">
							<ul>
								<li>
									<a href="#smartwizard-2-step-1" class="mb-3">
										<span class="sw-done-icon ion ion-md-checkmark"></span>
										<span class="sw-icon ion ion-ios-keypad"></span>
										Quote
										<div class="text-muted small">Select a quote</div>
									</a>
								</li>
								<li>
									<a href="#smartwizard-2-step-2" class="mb-3">
										<span class="sw-done-icon ion ion-md-checkmark"></span>
										<span class="sw-icon ion ion-ios-color-wand"></span>
										Details
										<div class="text-muted small">Quote Details</div>
									</a>
								</li>
								<li>
									<a href="#smartwizard-2-step-3" class="mb-3">
										<span class="sw-done-icon ion ion-md-checkmark"></span>
										<span class="sw-icon ion ion-md-copy"></span>
										Stock
										<div class="text-muted small">Modify stock</div>
									</a>
								</li>
								
							</ul>
							<div class="mb-3">
								<div id="smartwizard-2-step-1" class="card animated fadeIn">
									<div class="card-body">
										<div class="row">
                                            <div class="col-4">
                                                <h4>Quote Selection</h4>
                                                <br/>
                                            </div>
										</div>
										<div class="row">
                                            <div class="col-6">
												<label class="col form-label">Select the type of order</label>
												<select class="custom-select btn-block " name="selected_order_type" id="selected_order_type">
													<option value="quote">Quote</option>
													<option value="voc">VOC</option>
												</select>
                                            </div>
                                        </div>
										<br/>
										<div class="row" id = "quote_selection">
                                            <div class="col-6">
												<label class="col form-label">Select the quote</label>
                                                <select class="custom-select btn-block select2" name="quote_id" id="quote_id">
                                                    <?php 
														$sql = "select q.QUOTE_ID as value, concat('#',q.QUOTE_ID, ' - ', s.STORE_NAME, ' - ', q.CREATED_DATE) as description from TBL_QUOTE q
														inner join TBL_STORE s on s.STORE_ID = q.STORE_ID
														LEFT JOIN (SELECT QUOTE_ID FROM TBL_ORDER) R ON R.QUOTE_ID =q.QUOTE_ID
														where (q.STATUS = 'APPROVED')
														AND (R.QUOTE_ID IS NULL);";
                                                    gen_select_dropdown($db,$sql,0);
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										<br/>
										<div class="row" id = "voc_selection">
                                            <div class="col-6">
												<label class="col form-label">Select the VOC</label>
                                                <select class="custom-select btn-block select2" name="voc_id" id="voc_id">
                                                    <?php 
														$sql = " SELECT V.VOC_ID AS value, CONCAT('#', V.VOC_ID, ' - ', CREATED_DTM) AS description FROM TBL_VOC V
															LEFT JOIN TBL_ORDER O ON V.VOC_ID = O.VOC_ID
															WHERE (V.VOC_STATUS = 'APPROVED') AND (O.ORDER_NO IS NULL);	";
														gen_select_dropdown($db,$sql,0);
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
									</div>
								</div>
								<div id="smartwizard-2-step-2" class="card animated fadeIn">
									<div class="card-body" id="step_details">
										<div class="row">
											<div class="col-4">
												<h4>Contractor Details </h4>
												<br/>
												<div class="form-group">
													<label class="col form-label">Contractor</label>
													<div class="col"><input type="text" readonly class="form-control" id="contractor_name" required="" value=""></div>
												</div>
												<div class="form-group">
													<label class="col form-label">Contractor Email</label>
													<div class="col"><input type="text" readonly class="form-control" id="contractor_email" required="" value=""></div>
												</div>
												<div class="form-group">
													<label class="col form-label">Contractor Contact Number</label>
													<div class="col"><input type="text" readonly class="form-control" id="contractor_contact_number" required="" value=""></div>
												</div>
												<hr>
												<h4>Quote Details </h4>
												<br/>
												<div class="form-group">
													<label class="col form-label">Quote Type</label>
													<div class="col"><input type="text" readonly class="form-control" id="quote_type" required="" value=""></div>
												</div>
												<div class="form-group">
													<label class="col form-label">Quote Total</label>
													<div class="col"><input type="text" readonly class="form-control" id="quote_total" required="" value=""></div>
												</div>
												<div class="form-group">
													<label class="col form-label">Quote Created Date</label>
													<div class="col"><input type="text" readonly class="form-control" id="quote_created_dtm" required="" value=""></div>
												</div>
												<div class="form-group">
													<label class="col form-label">Quote Approved Date</label>
													<div class="col"><input type="text" readonly class="form-control" id="quote_approved_dtm" required="" value=""></div>
												</div>
												<div class="form-group">
													<label class="col form-label">Quote Status</label>
													<div class="col"><input type="text" readonly class="form-control" id="quote_status" required="" value=""></div>
												</div>

											</div>
										<div class="col-6">
											<h4> Order Details </h4>
											<br>
											<div class="form-group">
												<label class="col form-label">Order Number</label>
												<div class="col"><input type="text" class="form-control" name="order_no" id="order_no" required value=""/><small><i style="color:red;">Order number is required!</i></small></div>
												
											</div>
											<div class="form-group">
												<label class="col form-label">Order Notes</label>
												<div class="col"><textarea type="text" class="form-control" name="order_notes" required="" value=""></textarea><small><i>Any additional order notes.</i></small></div>
											</div>

											<hr>
											<h4>Store Details</h4>
											<br>
											<div class="form-group">
												<label class="col form-label">Store Name</label>
												<div class="col"><input type="text" readonly class="form-control" id="store_name" required="" value=""></div>
											</div>
											<div class="form-group">
												<label class="col form-label">Store FF Code</label>
												<div class="col"><input type="text" readonly class="form-control" id="store_ff" required="" value=""></div>
											</div>
											<div class="form-group">
												<label class="col form-label">Store Contact Number</label>
												<div class="col"><input type="text" readonly class="form-control" id="store_contact_number" required="" value=""></div>
											</div>
											<div class="form-group">
												<label class="col form-label">Store Area</label>
												<div class="col"><input type="text" readonly class="form-control" id="store_area" required="" value=""></div>
											</div>
											<div class="form-group">
												<label class="col form-label">Store Region</label>
												<div class="col"><input type="text" readonly class="form-control" id="store_region" required="" value=""></div>
											</div>
											
										
											<!-- TODO: Add map here -->
										</div>
									</div>
									</div>
								</div>
								<div id="smartwizard-2-step-3" class="card animated fadeIn">
									<div class="card-body">
										<div class="row">
										</div>
										<div class="row">
											<div class="table-responsive mb-4">
												<table class="table m-0 font-sm" id = "tbl_stock">
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
														<?php
															$sql = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY;";
															$result = $db->query($sql);
															foreach ($result->getResult('array') as $row) { ?>
																<tr>
																	<thead class="thead-light">
																		<tr  id="stock-category_<?php echo $row['ID']?>">
																			<th colspan="5"><strong><?php echo $row['NAME']?></strong></th>
																		</tr>
																	</thead>
																</tr>
																
														<?php } ?>
													</tbody>
												</table>
												<br>
												<br> 
												<button class="float-right btn btn-outline-primary" onclick="check_order_no()" type="button">Create Order</button>
											</div>
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</form>
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
var stockItems = <?php echo json_encode($stock); ?>;
var selectedEBQs = [];
var populated = 0;
var formatter = new Intl.NumberFormat('en-US', {
					style: 'currency',
					currency: 'ZAR' ,
					minimumFractionDigits: 2,
					maximumFractionDigits: 2,
	});
function get_stock_list(){
	var tbl_stock_body = document.querySelector('#tbl_stock_body');

		var items = stockItems.map(item => item.DESCRIPTION);

		//initialize the autocomplete suggestions for search as follows
		//items are the result data from ajax query
		autocomplete(document.getElementById("myInput"), items);
		
		$('#btn-stock-add').click(function() {
			itemName = $('#myInput').val();
			itemCategory = $('#stock-category-selector').val();
			stockrow = $(`#stock-category_${itemCategory}`);
			item = stockItems.find(item=>item.DESCRIPTION === itemName);
			// index = items.indexOf(itemName);
			
			var found = false;
			
			if (item) {
				
				selectedEBQs.forEach(function(ebq){
					if(item.EBQ_CODE == ebq){
						found = true;
					}
				});
				
				if(found == false){
					row = document.createElement('tr');
					selectedEBQs.push(item.EBQ_CODE);
					row.className += "item";
					row.innerHTML = `
							<input type='hidden' name='stock[${item.EBQ_CODE}][category]' value='${itemCategory}'/>
							<td class="py-3 ebq">${item.EBQ_CODE}</td>
							<td class="py-3">${item.DESCRIPTION}</td>
							<td class="py-3">${item.AVG_COST}</td>
							<td class="py-3">
							<div class="input-group" style = "width: 170px;">
									<span class="input-group-btn">
										<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
										-
										</button>
									</span>
									<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control quant input-number" value="1" min="1" max="${item.QUANTITY}" onchange='handleInputChange(event.target);updateTotal(this,'${item.EBQ_CODE}')' onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
									<span class="input-group-btn">
										<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
										+
										</button>
									</span>
								</div>
							</td>
							<td id='#${item.EBQ_CODE}'class="py-3">${1 * parseFloat(item.AVG_COST)}</td>

							<td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove" onclick='removeStockItem("${item.EBQ_CODE}")'>X</span></a></td>
						`;
							$(row).insertAfter(stockrow);
							$('#myInput').val('');
				} else {
					Swal.fire('Error!','Stock already added to order!','error');
				}
			
				
			}
			else {
				Swal.fire('Error!','The specified stock item does not exist!','error');
			}
		});
}
function removeStockItem(ebq){
	var index = selectedEBQs.indexOf(ebq);
	if(index > -1){
		selectedEBQs.splice(index,1);
	}

}

function order_get_quote_stock(){
	let quote_id = $('#quote_id').val()
	$.post("/order/create/ajax/order_get_quote_stock/", {quote_id:quote_id},
	function(result){
		var quoteStock = JSON.parse(result);
		
		Object.entries(quoteStock).forEach(entry => {
			const [key, value] = entry;
			selectedEBQs.push(value.EBQ_CODE);
			console.log(key, value);
			row = document.createElement('tr');
			var itemCat = value.STOCK_CATEGORY;
			var stockrow = $(`#stock-category_${itemCat}`);
			var tbl_stock_body = document.querySelector('#tbl_stock_body');
			row.className += "item";
			row.innerHTML = `
				<input type='hidden' name='stock[${value.EBQ_CODE}][category]' value='${itemCat}'/>
				<td class="py-3 ebq">${value.EBQ_CODE}</td>
				<td class="py-3">${value.DESCRIPTION}</td>
				<td class="py-3">${formatter.format(value.AVG_COST*(1+(value.MARKUP/100)))}</td>
				<td class="py-3">
				<div class="input-group" style = "width: 170px;">
						
						<input type="text" name="stock[${value.EBQ_CODE}][quantity]" readonly class="form-control quant input-number" value="${value.QUANTITY}" min="1" max="5" onchange='handleInputChange(event.target);updateTotal(this,'${value.EBQ_CODE}')' onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
						
					</div>
				</td>
				<td id='#${value.EBQ_CODE}'class="py-3">${formatter.format(value.AVG_COST*(1+(value.MARKUP/100)) * value.QUANTITY )}</td>

				
			`;
				
				$(row).insertAfter(stockrow);
				// tbl_stock_body.append(row);
				$('#myInput').val('');
				
		})
		

	})
	//get_stock_list();
}

function order_get_voc_stock(){
	let voc_id = $('#voc_id').val()
	$.post("/order/create/ajax/order_get_voc_stock/", {voc_id:voc_id},
	function(result){
		var quoteStock = JSON.parse(result);
		
		Object.entries(quoteStock).forEach(entry => {
			const [key, value] = entry;
			selectedEBQs.push(value.EBQ_CODE);
			console.log(key, value);
			row = document.createElement('tr');
			var itemCat = value.STOCK_CATEGORY;
			var stockrow = $(`#stock-category_${itemCat}`);
			var tbl_stock_body = document.querySelector('#tbl_stock_body');
			row.className += "item";
			row.innerHTML = `
				<input type='hidden' name='stock[${value.EBQ_CODE}][category]' value='${itemCat}'/>
				<td class="py-3 ebq">${value.EBQ_CODE}</td>
				<td class="py-3">${value.DESCRIPTION}</td>
				<td class="py-3">${formatter.format(value.AVG_COST*(1+(value.MARKUP/100)))}</td>
				<td class="py-3">
				<div class="input-group" style = "width: 170px;">
						
						<input type="text" name="stock[${value.EBQ_CODE}][quantity]" readonly class="form-control quant input-number" value="${value.QUANTITY}" min="1" max="5" onchange='handleInputChange(event.target);updateTotal(this,'${value.EBQ_CODE}')' onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
						
					</div>
				</td>
				<td id='#${value.EBQ_CODE}'class="py-3">${formatter.format(value.AVG_COST*(1+(value.MARKUP/100)) * value.QUANTITY )}</td>

				
			`;
				
				$(row).insertAfter(stockrow);
				// tbl_stock_body.append(row);
				$('#myInput').val('');
				
		})
		

	})
	//get_stock_list();
}

function updateTotal(e,ebq) {
	console.log(e);
	var newVal = e.value;

	var total = document.querySelector(`#${ebq}`);
	console.log(total);

}

function order_get_quote_details(){
	let quote_id = $('#quote_id').val();

	$.post("/order/create/ajax/order_get_quote_details/", {quote_id : quote_id},
		function(result){
			var obj = JSON.parse(result);
			//order_get_contractors(obj[0].CONTRACTOR_ID);
			$('#contractor_name').val(obj[0].CONTRACTOR_NAME);
			$('#contractor_email').val(obj[0].CONTRACTOR_EMAIL);
			$('#contractor_contact_number').val(obj[0].CONTRACTOR_CONTACT);
			$('#quote_type').val(obj[0].TYPE_NAME);
			$('#quote_total').val('R'+obj[0].TOTAL);
			$('#quote_created_dtm').val(obj[0].CREATED_DATE);
			$('#quote_approved_dtm').val(obj[0].APPROVED_DATE);
			$('#quote_status').val(obj[0].STATUS);
			$('#store_name').val(obj[0].STORE_NAME);
			$('#store_ff').val(obj[0].FF_CODE);
			$('#store_contact_number').val(obj[0].CONTACT_NUMBER);
			$('#store_area').val(obj[0].AREA_NAME);
			$('#store_region').val(obj[0].REGION_NAME);
	})

}

function order_get_voc_details(){
	let voc_id = $('#voc_id').val();

	$.ajax({
		type: "POST",
		url: window.location.origin + '/order/create/ajax/order_get_voc_details/',
		data: {
			'voc_id': voc_id,
		},
		success: function(result) {
			var obj = JSON.parse(result);
			//order_get_contractors(obj[0].CONTRACTOR_ID);
			$('#contractor_name').val(obj[0].CONTRACTOR_NAME);
			$('#contractor_email').val(obj[0].CONTRACTOR_EMAIL);
			$('#contractor_contact_number').val(obj[0].CONTRACTOR_CONTACT);
			$('#quote_type').val(obj[0].TYPE_NAME);
			$('#quote_total').val('R'+obj[0].TOTAL);
			$('#quote_created_dtm').val(obj[0].CREATED_DATE);
			$('#quote_approved_dtm').val(obj[0].APPROVED_DATE);
			$('#quote_status').val(obj[0].STATUS);
			$('#store_name').val(obj[0].STORE_NAME);
			$('#store_ff').val(obj[0].FF_CODE);
			$('#store_contact_number').val(obj[0].CONTACT_NUMBER);
			$('#store_area').val(obj[0].AREA_NAME);
			$('#store_region').val(obj[0].REGION_NAME);

		},
		error: function(data) {
			console.log(data);
		}
	});

}

function check_order_no(){
	var order_no = $('#order_no').val();
	$.post("/order/create/check_order_no/", {order_no : order_no},
	function(result){
		if (result == 0) {
			$('#order-stock-wizzard').submit();
		} else {
			Swal.fire('Duplicate order number!','Order number has already been used! Please enter a unique order number!','error');
		}
	});
}
// NB: TEMP DISABLED! MIGHT USE AGAIN LATER! DO NOT DELETE!!!!!!
// NB: TEMP DISABLED! MIGHT USE AGAIN LATER! DO NOT DELETE!!!!!!
// function order_get_contractors(selected){
// 	let quote_id = $('#quote_id').val();
// 	$.post("/order/create/ajax/get_contractors/", {},
// 		function(result){
// 			var obj = JSON.parse(result);
// 			const contractor_select = document.getElementById("contractor_select");
// 			while(contractor_select.firstChild){
// 				contractor_select.removeChild(contractor_select.lastChild);
// 			}
			
// 			for (var i = 0; i < obj.length; i++){
// 					var opt = document.createElement('option');
					
// 					opt.value = obj[i].CONTRACTOR_ID;
// 					if(obj[i].CONTRACTOR_ID == selected){
// 						opt.setAttribute('selected','true');
// 					}
// 					opt.appendChild(document.createTextNode(obj[i].CONTRACTOR_NAME));
// 					contractor_select.appendChild(opt);
// 			}
			
// 	})
// }

$(document).ready(function() {

	$('#voc_selection').hide();

	$("#selected_order_type").on('change', function(){
		if (this.value == "quote") {
			$('#voc_selection').hide();
			$('#quote_selection').show();
		}
		else {
			$('#voc_selection').show();
			$('#quote_selection').hide();
		}
	});

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
		toolbarSettings : {
			toolbarPosition: 'bottom'
		}
		
	});
	$('#quote_id').select2();
	$('#voc_id').select2();
	$('#stock-category-selector').select2();
	
	$('.smartwizard-example').on("leaveStep", function(e, anchorObject, stepNumber, stepDirection){


		if(stepNumber == 0 && stepDirection == "forward" ){
			if ($("#selected_order_type").val()=="quote") {
				order_get_quote_details();
			}
			else {
				order_get_voc_details();
			}
			
		} else if (stepNumber == 1 && stepDirection == 'forward'){
			var order_no = $('#order_no').val();
			
			if(order_no === null || order_no === '' || order_no.replace(/\s/g, '').length == 0){
				e.preventDefault();
				Swal.fire('Incomplete!','Please enter a unique order number!','error');
			}else{
				if(populated == 0){
					console.log($("#selected_order_type").val());
					if ($("#selected_order_type").val()=="quote") {
						order_get_quote_stock();
						
					}
					else {
						order_get_voc_stock();
					}
					populated = 1;
				}
				return true;
			}
		}
	});

	$('#order_no').on('change', function() {
		var table = document.querySelector('#tbl_stock_body');
		table.innerHTML = "";
		populated = 0;
	});
	
	$('#duallistbox-example').bootstrapDualListbox({
		nonSelectedListLabel: 'Non-selected Stock',
		selectedListLabel: 'Selected Stock',
		preserveSelectionOnMove: 'moved',
		moveOnSelect: false
	});
	$('#content_wrapper_loading').hide();
	$('#content_wrapper').show();      
	

	$("#tbl_stock").on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
    });
	

});
	
	    
		
	
</script>
<?php echo view('_general/footer');

