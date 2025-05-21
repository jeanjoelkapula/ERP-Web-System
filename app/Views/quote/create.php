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
						<span class="text-muted font-weight-light">Quote /</span> Create
					</h4>
					<?php require_once(APPPATH.'Views/quote/quote_wizard_base.php')?>
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
<script src="/assets/js/forms_wizard_quote_stock.js"></script>
<script>
	var stockItems =  [];
	var store_id = <?php if(isset($store_id)) echo $store_id; else echo 0; ?>;
	var hub_id = <?php if(isset($hub_id)) echo $hub_id; else echo 0; ?>;
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

	// Initialize with options
	onScan.attachTo(document, {
		suffixKeyCodes: [13], // enter-key expected at the end of a scan
		reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
		onScan: function(sCode, iQty) {
			
			if ($($('li.nav-item.active')[0]).find('span.sw-number')[0].innerText == "2") {
				itemCategory = $('#stock-category-selector').val();
				stockrow = $(`#stock-category_${itemCategory}`);
				item = stockItems.find(item=>item.EBQ_CODE === sCode.replace(/\*/g, ''));
				row = document.createElement('tr');
				var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
				if (item && !existingEBQ) {
					EBQ_added.push(item.EBQ_CODE);
					calculateTotals();
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
							<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control input-number" value="1" min="1" max="1000" onchange="handleInputChange(event.target);updateTotal(this,'${item.EBQ_CODE}');calculateTotals();" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
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

	function updateStoreID(e){
		store_id = e.target.value;
		contractor_el = document.querySelector("select[name='contractor_id']");
		//fetch hub 
		$.post("/quote/create/ajax/get_pref_contractor/", {store_id : store_id},
			function(result){
			store_info = JSON.parse(result);
			try {
				if(store_info && store_info.length){
					contractor_el.value = store_info[0].CONTRACTOR_ID;
					document.querySelector("select[name='hub_id']").value = store_info[0].HUB_ID;


				}
			} catch (err) {
				console.error(err)
			}
	});
}

	
	$(document).ready(function() {		
		$('#store-selector').select2();
		$('#contractor-selector').select2();
	store_id = $('#store-selector').val();
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

    $("#tbl_stock").on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
	});


	});
	
	
</script>
<?php echo view('_general/footer');