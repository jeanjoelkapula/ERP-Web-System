

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
				<form id="order-stock-wizzard-internal" autocomplete="off" method="POST" action="/order/update/update_internal" novalidate="novalidate" class="sw-main sw-theme-default">
				<input type="hidden" name="form_update_order_internal" id="form_update_order_internal" value="true" />
				<input type="hidden" name="order_no" value="<?php echo $this->data['entity_id']; ?>" />
				<div class="card-body">
					<div class="demo-vertical-spacing" id="content_wrapper" style="display:none">
						<div id="smartwizard-2" class="smartwizard-example">

                        <?php
                            $sql = "SELECT * FROM TBL_ORDER_INTERNAL WHERE ORDER_NO = '".$this->data['entity_id']."';";
                            $query = $db->query($sql)->getResultArray();

                            $source_hub = $query[0]['SOURCE_HUB_ID'];
                            $destination_hub = $query[0]['DESTINATION_HUB_ID'];

                            $sql = "SELECT S.*, M.* FROM  TBL_HUB_STOCK HS
                                INNER JOIN TBL_STOCK S ON HS.EBQ_CODE = S.EBQ_CODE
                                INNER JOIN TBL_METRIC M ON S.METRIC_ID = M.METRIC_ID
                                WHERE HS.HUB_ID =  $source_hub;";
                            $source_hub_stock = $db->query($sql)->getResultArray();

                        ?>
							<ul>
								<li>
									<a href="#smartwizard-2-step-1" class="mb-3">
										<span class="sw-done-icon ion ion-md-checkmark"></span>
										<span class="sw-icon ion ion-ios-keypad"></span>
										Hub Selection
										<div class="text-muted small">Select Hubs</div>
									</a>
								</li>
								<li>
									<a href="#smartwizard-2-step-2" class="mb-3">
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
                                            <div class="col-5">
                                                <h4> Source Hub </h4>
                                                <br>
                                                <select class="custom-select btn-block select2" name="s_hub_id" id="s_hub_id" disabled>
                                                    <?php 
													$sql = "select HUB_ID as value, HUB_NAME as description from TBL_HUB";
                                                    gen_select_dropdown($db,$sql,$source_hub);
                                                    ?>
                                                </select>

                                            </div>
                                            <div class="col-5">
                                            <h4> Destination Hub </h4>
                                                <br>
                                                <select class="custom-select btn-block select2" name="d_hub_id" id="d_hub_id" disabled>
                                                    <?php 
													$sql = "select HUB_ID as value, HUB_NAME as description from TBL_HUB";
                                                    gen_select_dropdown($db,$sql,$destination_hub);
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
									</div>
								</div>
								<div id="smartwizard-2-step-2" class="card animated fadeIn">
									<div class="card-body">

                                        <div class="row">
                                            <div class="form-group">
                                                <label class="col form-label">Stock Item Search <span class="text-danger">*</span></label>
                                                <div class="col">
                                                    <div class="autocomplete" style="width:500px;">
                                                        <input id="stock-search" type="text" class="form-control" placeholder="EBQ00006 : WIRE 2.5MM TWIN & EARTH /M " >
                                                    </div>
                                                    <div class="text-light small font-weight-semibold mb-3">You may scan the barcode of the item to add it to the list</div>
                                                </div>
                                            </div>
                                            <div class="form-group" style= "margin-left: 15px; margin-top: 23px;">
                                                <button id="btn-stock-add" class="btn btn-primary" type="button"><i class="fas fa-plus"></i>&nbsp; Add Item </a>
                                            </div>
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
                                                                Quantity
                                                            </th>
                                                            <th class="py-3">
                                                                Unit
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody  id = "tbl_stock_body">
                                                    <?php
                                                        $sql = "SELECT S.*, M.METRIC_DESCRIPTION, IOS.QUANTITY FROM TBL_ORDER_INTERNAL OI
                                                            INNER JOIN TBL_INTERNAL_ORDER_STOCK IOS ON IOS.ORDER_NO = OI.ORDER_NO
                                                            INNER JOIN TBL_STOCK S ON S.EBQ_CODE = IOS.EBQ_CODE
                                                            INNER JOIN TBL_METRIC M ON S.METRIC_ID = M.METRIC_ID
                                                            WHERE OI.ORDER_NO = '".$this->data['entity_id']."';";
                                                        $query = $db->query($sql);
                                                        $stock_items = $query->getResult('array');
                                                        
                                                        foreach ($stock_items as $row) { 
                                                    ?>
                                                        <tr>
                                                            <td id='<?php echo $row['EBQ_CODE']; ?>' class="py-3"><?php echo $row['EBQ_CODE']; ?></td>
                                                            <td class="py-3"><?php echo $row['DESCRIPTION']; ?></td>
                                                            <td class="py-3">
                                                            <div class="input-group" style = "width: 170px;">
                                                                    <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="stock[<?php echo $row['EBQ_CODE']; ?>][quantity]" onclick='handlePlusMinusClick(event)'>
                                                                        -
                                                                        </button>
                                                                    </span>
                                                                    <input type="text" name="stock[<?php echo $row['EBQ_CODE']; ?>][quantity]" class="form-control input-number" value="1" min="1" max="<?php echo $row['QUANTITY']; ?>" onchange="handleInputChange(event.target);updateTotal(this,'<?php echo $row['EBQ_CODE']; ?>');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
                                                                    <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[<?php echo $row['EBQ_CODE']; ?>][quantity]" onclick='handlePlusMinusClick(event)'>
                                                                        +
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="py-3"><?php echo $row['METRIC_DESCRIPTION']; ?></td>                                                            
                                                            <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(<?php echo $row['EBQ_CODE']; ?>)" class="btn btn-circle btn-default btn-remove">X</span></a></td>

                                                        </tr>
                                                    <?php
                                                        }
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button class="float-right btn btn-outline-primary"  type="submit">Update Order</button>
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
//TODO: Use markup with avg_cost to get price
    var stockItems =   JSON.parse('<?php echo json_encode($source_hub_stock); ?>');
	var store_id = <?php if(isset($store_id)) echo $store_id; else echo 0; ?>;
	var hub_id = $("#s_hub_id").val();
	var EBQ_added = JSON.parse('<?php echo json_encode($stock_items); ?>');
	var barcodeOld = '';

	var formatter = new Intl.NumberFormat('en-US', {
					style: 'currency',
					currency: 'ZAR' ,
					minimumFractionDigits: 2,
					maximumFractionDigits: 2,
	});

	function handleHubSelection(){
        var stockItemNames = [];

		stockItems.forEach(function(item, index) {
            stockItemNames[index] = item.EBQ_CODE + ' : ' +  item.DESCRIPTION;
        });

        //set autocomplete suggestion to search input
        autocomplete(document.getElementById("stock-search"), stockItemNames);	

	}

	function handleRemoveStock(ebq){
		if(ebq.id){
			ebq = ebq.id;
		}
		EBQ_added = EBQ_added.filter(item=> item !== ebq);
	}

	function updateTotal(e,ebq) {
		var totalText = $(`#${ebq}_total`);
		var input = $(`#${ebq}_hidden`);
		totalText.text(formatter.format((item.AVG_COST*(1+(item.MARKUP/100)))*parseInt(e.value)));
		input.val((item.AVG_COST*(1+(item.MARKUP/100)))*parseInt(e.value));
	}

	function updateStoreID(e){
		store_id = e.target.value;
	}

function check_order_no(){
	var order_no = $('#order_no').val();
	$.post("/order/create/check_order_no/", {order_no : order_no},
	function(result){
		if (result == 0) {
			$('#order-stock-wizzard-internal').submit();
		} else {
			Swal.fire('Duplicate order number!','Order number has already been used! Please enter a unique order number!','error');
		}
	});
}

$(document).ready(function() {


	store_id = 0;
	handleHubSelection();
	var tbl_stock_body = document.querySelector('#tbl_stock_body');
		$('#btn-stock-add').click(function() {
            itemName = $('#stock-search').val();
            item = stockItems.find(item=>item.EBQ_CODE+' : '+item.DESCRIPTION === itemName);
            row = document.createElement('tr');
            var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
            if (item && !existingEBQ) {
                EBQ_added.push(item.EBQ_CODE);
                row.innerHTML = `
                <input type='hidden' name='stock[${item.EBQ_CODE}][markup]' value='${item.MARKUP}'/>
                <input type='hidden' name='stock[${item.EBQ_CODE}][avg_cost]' value='${item.AVG_COST}'/>
                <td id='${item.EBQ_CODE}' class="py-3">${item.EBQ_CODE}</td>
                <td class="py-3">${item.DESCRIPTION}</td>
                <td class="py-3">
                <div class="input-group" style = "width: 170px;">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
                            -
                            </button>
                        </span>
                        <input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control input-number" value="1" min="1" max="${item.QUANTITY}" onchange="handleInputChange(event.target);updateTotal(this,'${item.EBQ_CODE}');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
                            +
                            </button>
                        </span>
                    </div>
                </td>
                <td class="py-3">${item.METRIC_DESCRIPTION}</td>
                <input id='${item.EBQ_CODE}_hidden' type='hidden' name='total[]' value='${1 * item.AVG_COST*(1+(item.MARKUP/100))}' />
                <input type='hidden' name='stock[${item.EBQ_CODE}][hub]' value='${hub_id}' />

                <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(${item.EBQ_CODE})" class="btn btn-circle btn-default btn-remove">X</span></a></td>
            `;
                $(tbl_stock_body).append(row);
                $('#stock-search').val('');
        }
        else {
			if(existingEBQ){
				Swal.fire('Error!','The specified stock item has already been added!','error');
				$('#stock-search').val('');
			}
			else{
				Swal.fire('Error!','The specified stock item does not exist or isnt available at the selected hub!','error');
				$('#stock-search').val('');
			}
        }
    });

// Initialize with options
onScan.attachTo(document, {
		suffixKeyCodes: [13], // enter-key expected at the end of a scan
		reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
		onScan: function(sCode, iQty) {
			var selectedItemId = $('li.nav-item.active > a')[0].id;
			
			if (selectedItemId == "stock-selection-step") {
				item = stockItems.find(item=>item.EBQ_CODE === sCode.replace(/\*/g, ''));
				row = document.createElement('tr');
				var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
				if (item && !existingEBQ) {
					EBQ_added.push(item.EBQ_CODE);
					row.innerHTML = `
					<input type='hidden' name='stock[${item.EBQ_CODE}][markup]' value='${item.MARKUP}'/>
					<input type='hidden' name='stock[${item.EBQ_CODE}][avg_cost]' value='${item.AVG_COST}'/>
					<td id='${item.EBQ_CODE}' class="py-3">${item.EBQ_CODE}</td>
					<td class="py-3">${item.DESCRIPTION}</td>
					<td class="py-3">
					<div class="input-group" style = "width: 170px;">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
								-
								</button>
							</span>
							<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control input-number" value="1" min="1" max="${item.QUANTITY}" onchange="handleInputChange(event.target);updateTotal(this,'${item.EBQ_CODE}');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
							<span class="input-group-btn">
								<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
								+
								</button>
							</span>
						</div>
					</td>
					<td class="py-3">${item.METRIC_DESCRIPTION}</td>
					<input id='${item.EBQ_CODE}_hidden' type='hidden' name='total[]' value='${1 * item.AVG_COST*(1+(item.MARKUP/100))}' />
					<input type='hidden' name='stock[${item.EBQ_CODE}][hub]' value='${hub_id}' />

					<td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(${item.EBQ_CODE})" class="btn btn-circle btn-default btn-remove">X</span></a></td>
					`;
					$(tbl_stock_body).append(row);
					$('#stock-search').val('');
					calculateTotals();
				}
				else {
					if(existingEBQ){
						Swal.fire('Error!','The specified stock item has already been added!','error');
						$('#stock-search').val('');
					}
					else{
						Swal.fire('Error!','The specified stock item does not exist or isnt available at the selected hub!','error');
						$('#stock-search').val('');
					}
				}
			}
			
		},
		onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
		}
	});
	
    $("#tbl_stock").on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
	});

    $('.smartwizard-example').on("leaveStep", function(e, anchorObject, stepNumber, stepDirection){
	

    if(stepNumber == 0 && stepDirection == "forward" ){
        
    } else if (stepNumber == 1 && stepDirection == 'forward'){
       
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
		toolbarSettings : {toolbarPosition: 'both'},
	});
	$('.select2').select2();

    $('#content_wrapper_loading').hide();
	$('#content_wrapper').show();   
	
})

</script>