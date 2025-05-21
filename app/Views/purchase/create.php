<?php echo view('_general/header');?>

<div class="layout-wrapper layout-2">
	<div class="layout-inner">
		<?php echo view('_general/navigation'); ?>
		<div class="layout-container">
			<?php echo view('_general/navigation_top'); ?>
			<!-- Layout content -->
			<div class="layout-content">
				<!-- Content -->
				<div class="container-fluid flex-grow-1 container-p-y" id="outer_wrapper">
					<h4 class="font-weight-bold py-3 mb-4">
						<span class="text-muted font-weight-light">Purchase Order /</span> Create
					</h4>
					<?php require_once(APPPATH.'Views/purchase/order_wizard_base.php')?>
				</div>
				<!-- Content -->
			</div>
			<!-- Layout content -->
		</div>
		<!-- Layout content -->
	</div>
</div>
<?php echo view('_general/footer_javascript'); ?> 
<script src="/assets/vendor/libs/datatables/datatables.js"></script>
<script src="/assets/js/forms_wizard_purchase_order.js"></script>
<script>
    var stockItems = <?php echo json_encode($stock); ?>;
    var tbl_stock_body = document.querySelector('#tbl_stock_body');
    //set autocomplete suggestion to search input
    stockItemNames = [];
    try {
        if(stockItems.length){
            stockItems.forEach(function(item, index) {
            stockItemNames[index] = item.EBQ_CODE + ' : ' +  item.DESCRIPTION;
            });
        }
        //set autocomplete suggestion to search input
        autocomplete(document.getElementById("stock-search"), stockItemNames);
    } catch (err) {
        console.error(err)
    }

    autocomplete(document.getElementById("stock-search"), stockItemNames);

	var formatter = new Intl.NumberFormat('en-US', {
					style: 'currency',
					currency: 'ZAR' ,
					minimumFractionDigits: 2,
					maximumFractionDigits: 2,
	});

	function handleHubSelection(e){
		$("#stock-search").val('');
		autocomplete(document.getElementById("stock-search"), []);
		hub_id = e.target.value;
	}

	function updateStoreID(e){
		store_id = e.target.value;
	}

	function updateAmount(ebq) {
		var totalText = $(`#${ebq}_amount`);
		var input = document.getElementById(`stock[${ebq}][amount]`);
        var quantityInputValue = $(`input[name ='stock[${ebq}][quantity]']`).val();
        var unitPriceValue = $(`input[name ='stock[${ebq}][price]']`).val();

        totalText.text(quantityInputValue * parseFloat(unitPriceValue));
        input.value = quantityInputValue * parseFloat(unitPriceValue);

        updateOrderTotal();
		
    }
    
    function updateOrderTotal() {
        var totalText = $(`#order_total`);
        var miscCharges = $('#misc_charges').val();
        var freightCharges = $('#freight_charges').val();

        var input = $(`#po_amount`);
        var amountTotal = 0;
        var amountNodes =  document.getElementsByName('item_amount');
        for(i=0; i < amountNodes.length; ++i) {
            amountTotal += parseFloat(amountNodes[i].innerText);
        }

        amountTotal += (parseFloat(miscCharges) + parseFloat(freightCharges));
        
        totalText.text(formatter.format(amountTotal));
		input.val(amountTotal);
	}
	
	// Initialize with options
	onScan.attachTo(document, {
		suffixKeyCodes: [13], // enter-key expected at the end of a scan
		reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
		onScan: function(sCode, iQty) {
			
			if ($($('li.nav-item.active')[0]).find('span.sw-number')[0].innerText == "2") {
				item = stockItems.find(item=>item.EBQ_CODE === sCode.replace(/\*/g, ''));
				row = document.createElement('tr');
				var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
				if (item && !existingEBQ) {
					row.innerHTML = `
					<td id='${item.EBQ_CODE}' class="py-3">${item.EBQ_CODE}</td>
					<td class="py-3">${item.DESCRIPTION}</td>
					<td class="py-3">${item.METRIC_DESCRIPTION}</td>
					<td class="py-3">
						<div class="input-group" style = "width: 170px;">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
								-
								</button>
							</span>
							<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control input-number" value="1" min="1" max="100000000000000000000" onchange="handleInputChange(event.target);updateAmount('${item.EBQ_CODE}');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
							<span class="input-group-btn">
								<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick="handlePlusMinusClick(event);">
								+
								</button>
							</span>
						</div>
					</td>
					<td>
						<div class="col">
							<div class="form-group">
								<div class="">
									<input type="number" name="stock[${item.EBQ_CODE}][price]" onchange="updateAmount('${item.EBQ_CODE}');" class="form-control" value="1" min="1" step=".01">
								</div>
							</div>
						</div>
					</td>
					<td id = "${item.EBQ_CODE}_amount" name="item_amount">
						1
					</td>
					<input type="number" id="stock[${item.EBQ_CODE}][amount]" name="stock[${item.EBQ_CODE}][amount]" class="form-control" hidden>
					<td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove">X</span></a></td>
				`;
				tbl_stock_body.append(row);
				updateOrderTotal();
					$('#stock-search').val('');
				}
				else {
					if(existingEBQ){
						Swal.fire('Error!','The specified stock item has already been added!','error');
						$('#stock-search').val('');
					}
					else{
						Swal.fire('Error!','The stock item does not exist!','error');
						$('#stock-search').val('');
					}
				}
			}
			
		},
		onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
		}
	});

	$(document).ready(function() {		
	
	store_id = $('#store-selector').val();
	hub_id = $('#hub-selector').val();
    
	var tbl_stock_body = document.querySelector('#tbl_stock_body');
		
		$('#btn-stock-add').click(function() {
		itemName = $('#stock-search').val();
		item = stockItems.find(item=>item.EBQ_CODE+' : '+item.DESCRIPTION === itemName);
		row = document.createElement('tr');
		var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
        if (item && !existingEBQ) {
			row.innerHTML = `
            <td id='${item.EBQ_CODE}' class="py-3">${item.EBQ_CODE}</td>
            <td class="py-3">${item.DESCRIPTION}</td>
            <td class="py-3">${item.METRIC_DESCRIPTION}</td>
			<td class="py-3">
			    <div class="input-group" style = "width: 170px;">
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
						-
						</button>
					</span>
					<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control input-number" value="1" min="1" max="100000000000000000000" onchange="handleInputChange(event.target);updateAmount('${item.EBQ_CODE}');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick="handlePlusMinusClick(event);">
						+
						</button>
					</span>
				</div>
			</td>
            <td>
                <div class="col">
                    <div class="form-group">
                        <div class="">
                            <input type="number" name="stock[${item.EBQ_CODE}][price]" onchange="updateAmount('${item.EBQ_CODE}');" class="form-control" value="1" min="1" step=".01">
                        </div>
                    </div>
                </div>
            </td>
            <td id = "${item.EBQ_CODE}_amount" name="item_amount">
                1
            </td>
            <input type="number" id="stock[${item.EBQ_CODE}][amount]" name="stock[${item.EBQ_CODE}][amount]" class="form-control" value="1" hidden>
            <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove">X</span></a></td>
        `;
        tbl_stock_body.append(row);
        updateOrderTotal();
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

    $("#tbl_stock").on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
        updateOrderTotal();
	});

	$(".sw-btn-next").on('click', function (e) {
		e.preventDefault();
		console.log('sup');
	});


});
	
	
</script>
<?php echo view('_general/footer');