<?php 
    echo view('_general/header'); 

    $sql = "SELECT o.*,q.QUOTE_ID,q.QUOTE_TYPE_ID,q.NOTE AS QUOTE_NOTE,q.APPROVED_DATE as QUOTE_APPROVED_DTM,
    q.TOTAL,qt.TYPE_NAME,c.CONTRACTOR_NAME,c.CONTACT_NUMBER,c.EMAIL as CONTRACTOR_EMAIL, v.*,q.CREATED_DATE as QUOTE_CREATED_DATE, q.STATUS as QUOTE_STATUS
    FROM TBL_ORDER o
    INNER JOIN TBL_QUOTE q ON q.QUOTE_ID = o.QUOTE_ID
    INNER JOIN TBL_QUOTE_TYPE qt ON qt.TYPE_ID = q.QUOTE_TYPE_ID
    INNER JOIN TBL_CONTRACTOR c ON c.CONTRACTOR_ID = q.CONTRACTOR_ID
    INNER JOIN TBL_VOC v ON v.ORDER_NO = o.ORDER_NO
    WHERE v.VOC_ID = $entity_id";
    $query = $db->query($sql);

    $data = $query->getRow();
?>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-2">
	<div class="layout-inner">

		<?php echo view('_general/navigation'); ?>

		<!-- Layout container -->
		<div class="layout-container">

			<?php echo view('_general/navigation_top'); ?>

			<!-- Layout content -->
			<div class="layout-content">
            <form id="frm_create_voc" autocomplete="off" method="POST" action="/voc/update/<?php echo $voc_id; ?>" novalidate="novalidate" class="sw-main sw-theme-default">
                <input type="hidden" name="frm_update_voc" value="true"></input>
                <input type="hidden" name="voc_id" value="<?php echo $voc_id; ?>"></input>
				<!-- Content -->
				<div class="container-fluid flex-grow-1 container-p-y pt-0">
                <div id="smartwizard-2" class="smartwizard-example mt-5">
                    <ul>
                    <li>
                    <a href="#smartwizard-2-step-1" class="mb-3">
                        <span class="sw-done-icon ion ion-md-checkmark"></span>
                        <span class="sw-icon ion ion-ios-keypad"></span>
                        Order
                        <div class="text-muted small">Select the original Order</div>
                    </a>
                    </li>
                    <li>
                    <a href="#smartwizard-2-step-2" class="mb-3">
                        <span class="sw-done-icon ion ion-md-checkmark"></span>
                        <span class="sw-icon ion ion-ios-color-wand"></span>
                        Order Details
                        <div class="text-muted small">View & Confirm Order</div>
                    </a>
                    </li>
                    <li>
                    <a href="#smartwizard-2-step-3" class="mb-3" id = "stock-selection-step">
                        <span class="sw-done-icon ion ion-md-checkmark"></span>
                        <span class="sw-icon ion ion-md-copy"></span>
                        Stock
                        <div class="text-muted small">Add Stock</div>
                    </a>
                    </li>
                    
                </ul>
                
                <div class="mb-3">
                
                    <div id="smartwizard-2-step-1" class="card animated fadeIn">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <h4>Order Selection</h4>
                                    <br/>
                                </div>
                                <div class="col-6">
                                    <select class="custom-select btn-block select2" name="order_no" id="order_no" disabled>
                                        <?php 
                                            $sql = "select o.ORDER_NO as value,concat(s.STORE_NAME, ' - ','#', o.ORDER_NO, ' - ', o.ORDER_DATE_CREATED) AS description
                                            from TBL_ORDER o
                                            INNER JOIN TBL_QUOTE q ON q.QUOTE_ID = o.QUOTE_ID
                                            INNER JOIN TBL_STORE s ON s.STORE_ID = q.STORE_ID
                                            WHERE o.STATUS = 'APPROVED' AND o.ORDER_NO = '$order_no'";
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
                                    <h4>Contractor Details </h4>
                                    <br/>
                                    <div class="form-group">
                                        <label class="col form-label">Contractor</label>
                                        <div class="col"><input type="text" readonly class="form-control" id="contractor_name" required="" value="<?php echo $data->CONTRACTOR_NAME; ?>"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col form-label">Contractor Email</label>
                                        <div class="col"><input type="text" readonly class="form-control" id="contractor_email" required="" value="<?php echo $data->CONTRACTOR_EMAIL; ?>"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col form-label">Contractor Contact Number</label>
                                        <div class="col"><input type="text" readonly class="form-control" id="contractor_contact_number" required="" value="<?php echo $data->CONTACT_NUMBER; ?>"></div>
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
                                
                                </div>
                                <!-- TODO: Add map here -->
                            </div>
                        </div>
                    </div>
                    <div id="smartwizard-2-step-3" class="card animated fadeIn">
                    
                    <div class="card-body">
                        <div class="row">
                            <div class = "form-group">

                            <label class="col form-label">Source Hub</label>
                                <div class = "col">
                                    <select id="hub-selector" name='hub_id' class="custom-select btn-block" onchange="handleHubSelection(event)">
                                        <?php 
                                            $sql = "SELECT HUB_ID as value,HUB_NAME as description FROM TBL_HUB;";
                                            if(isset($hub_id)){
                                                gen_select_dropdown($db, $sql, $hub_id);
                                            }else{
                                                gen_select_dropdown($db, $sql, 0);
                                            }
                                            ?>
                                    </select>
                                </div>
                            </div>
                        </div>
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
                            <div class = "form-group">
                            <label class="col form-label">Stock Category</label>
                            <div class = "col">
                                <select id="stock-category-selector" class="custom-select btn-block">
                                    <?php 
                                        $sql = "SELECT ID as value,NAME as description FROM TBL_QUOTE_STOCK_CATEGORY;";
                                        gen_select_dropdown($db, $sql, 0);
                                        ?>
                                </select>
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
                                            <th class="py-3">
                                                Price
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
                                                            <th colspan="7"><strong><?php echo $row['NAME']?></strong></th>
                                                        </tr>
                                                    </thead>
                                                </tr>
                                                
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary float-right mt-5">Update</button>

                    </div>
                </div>
                </form>
            </div>
                    
        </div>
                
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
	var stockItems =  [];
	var store_id = <?php if(isset($store_id)) echo $store_id; else echo 0; ?>;
	var hub_id = <?php if(isset($hub_id)) echo $hub_id; else echo 0; ?>;
    var selectedStock = <?php if(isset($voc_stock)){ 
			echo json_encode($voc_stock);
		} else{
			echo json_encode([]);
		};
	?>;
	var EBQ_added = [];
	var barcodeOld = '';

	var formatter = new Intl.NumberFormat('en-US', {
					style: 'currency',
					currency: 'ZAR' ,
					minimumFractionDigits: 2,
					maximumFractionDigits: 2,
	});

	function handleHubSelection(){
		$("#stock-search").val('');

		let itemRemoved = 0;
		hub_id = $("#hub-selector").val()
		$.post("/quote/create/ajax/precheck_hubstock/", {hub_id : hub_id},
			function(result){
			stockItems = JSON.parse(result);
			var stockItemNames = [];
			try {
				if(stockItems && stockItems.length){
					stockItems.forEach(function(item, index) {
					stockItemNames[index] = item.EBQ_CODE + ' : ' +  item.DESCRIPTION;
					});
				}
				//set autocomplete suggestion to search input
				autocomplete(document.getElementById("stock-search"), stockItemNames);
			} catch (err) {
				console.error(err)
			}
			EBQ_added.forEach(EBQ => {
			var foundEBQ = stockItems.find(item=>item.EBQ_CODE === EBQ);
				if(!foundEBQ){
					itemRemoved += 1;
					handleRemoveStock(EBQ);
					$(`#${EBQ}`).closest('tr').remove();
				}
			});

			if(itemRemoved > 0){
			Swal.fire('Error!',`${itemRemoved} Stock items have been removed as they aren't available at the selected hub!`,'error');
		}
		});		

	}

	function calculateTotals(){
		let sub_total = 0;
		EBQ_added.forEach(ebq => {
			sub_total += parseFloat(document.querySelector(`#${ebq}_hidden`)?.value); 
		})
		$pki_fee = parseFloat($("input[name='pki_fee']").val()); 
		$("#sub-total").text(formatter.format(sub_total));
		$("#pki-fee-percentage").text($pki_fee+"%");
		$("#pki-fee-total").text(formatter.format(sub_total*($pki_fee/100)));
		$("#total-amount").text(formatter.format(sub_total*(1+($pki_fee/100))));
	}

	function handleRemoveStock(ebq){
		if(ebq.id){
			ebq = ebq.id;
		}
		EBQ_added = EBQ_added.filter(item=> item !== ebq);
		calculateTotals();
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

	
$(document).ready(function() {	

    //prepopulate table with selecte items
    if(selectedStock.length){
        selectedStock.forEach(item => { 
            EBQ_added.push(item.EBQ_CODE);
            itemCategory = item.STOCK_CATEGORY;
            stockrow = $(`#stock-category_${itemCategory}`);

            row = document.createElement('tr');
          
            row.innerHTML = `
            <input type='hidden' name='stock[${item.EBQ_CODE}][category]' value='${itemCategory}'/>
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
            <td class="py-3">${formatter.format(item.AVG_COST*(1+(item.MARKUP/100)))}</td>
            <td id='${item.EBQ_CODE}_total' class="py-3">${formatter.format(1 * item.AVG_COST*(1+(item.MARKUP/100)))}</td>
            <input id='${item.EBQ_CODE}_hidden' type='hidden' name='total[]' value='${1 * item.AVG_COST*(1+(item.MARKUP/100))}' />
            <input type='hidden' name='stock[${item.EBQ_CODE}][hub]' value='${hub_id}' />

            <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(${item.EBQ_CODE})" class="btn btn-circle btn-default btn-remove">X</span></a></td>
            `;

            console.log(stockrow);
            $(row).insertAfter(stockrow);
            $('#stock-search').val('');
            calculateTotals();
        });
	}


    $('.smartwizard-example').smartWizard({
        autoAdjustHeight: false,
        backButtonSupport: false,
        useURLhash: false,
        showStepURLhash: false,
        toolbarSettings : {toolbarPosition: 'both'},
    });

    $('#order_no').select2();


    $('.smartwizard-example').on("leaveStep", function(e, anchorObject, stepNumber, stepDirection){


        if(stepNumber == 0 && stepDirection == "forward" ){
            voc_get_quote_details();
            
        } else if (stepNumber == 1 && stepDirection == 'forward'){
                


        }
    });	
	
	store_id = 0;
	handleHubSelection();
	var tbl_stock_body = document.querySelector('#tbl_stock_body');
		$('#btn-stock-add').click(function() {
            itemName = $('#stock-search').val();
            itemCategory = $('#stock-category-selector').val();
            stockrow = $(`#stock-category_${itemCategory}`);
            item = stockItems.find(item=>item.EBQ_CODE+' : '+item.DESCRIPTION === itemName);
            row = document.createElement('tr');
            var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
            if (item && !existingEBQ) {
                EBQ_added.push(item.EBQ_CODE);
                row.innerHTML = `
                <input type='hidden' name='stock[${item.EBQ_CODE}][category]' value='${itemCategory}'/>
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
                <td class="py-3">${formatter.format(item.AVG_COST*(1+(item.MARKUP/100)))}</td>
                <td id='${item.EBQ_CODE}_total' class="py-3">${formatter.format(1 * item.AVG_COST*(1+(item.MARKUP/100)))}</td>
                <input id='${item.EBQ_CODE}_hidden' type='hidden' name='total[]' value='${1 * item.AVG_COST*(1+(item.MARKUP/100))}' />
                <input type='hidden' name='stock[${item.EBQ_CODE}][hub]' value='${hub_id}' />

                <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(${item.EBQ_CODE})" class="btn btn-circle btn-default btn-remove">X</span></a></td>
                `;
                $(row).insertAfter(stockrow);
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
        });

        // Initialize with options
	onScan.attachTo(document, {
		suffixKeyCodes: [13], // enter-key expected at the end of a scan
		reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
		onScan: function(sCode, iQty) {
			var selectedItemId = $('li.nav-item.active > a')[0].id;
            
			if (selectedItemId == "stock-selection-step") {
				itemCategory = $('#stock-category-selector').val();
				stockrow = $(`#stock-category_${itemCategory}`);
				item = stockItems.find(item=>item.EBQ_CODE === sCode.replace(/\*/g, ''));
				row = document.createElement('tr');
                var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
                if (item && !existingEBQ) {
                    EBQ_added.push(item.EBQ_CODE);
                    row.innerHTML = `
                    <input type='hidden' name='stock[${item.EBQ_CODE}][category]' value='${itemCategory}'/>
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
                    <td class="py-3">${formatter.format(item.AVG_COST*(1+(item.MARKUP/100)))}</td>
                    <td id='${item.EBQ_CODE}_total' class="py-3">${formatter.format(1 * item.AVG_COST*(1+(item.MARKUP/100)))}</td>
                    <input id='${item.EBQ_CODE}_hidden' type='hidden' name='total[]' value='${1 * item.AVG_COST*(1+(item.MARKUP/100))}' />
                    <input type='hidden' name='stock[${item.EBQ_CODE}][hub]' value='${hub_id}' />

                    <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(${item.EBQ_CODE})" class="btn btn-circle btn-default btn-remove">X</span></a></td>
                    `;
                    $(row).insertAfter(stockrow);
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


	});

    
function voc_get_quote_details(){
	let order_no = $('#order_no').val();

	$.post("/voc/create/ajax/voc_get_quote_details/", {order_no : order_no},
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
			$('#store_contact_number').val(obj[0].STORE_CONTACT);
			$('#store_area').val(obj[0].AREA_NAME);
			$('#store_region').val(obj[0].REGION_NAME);
	})

}



</script>

<?php echo view('_general/footer');
