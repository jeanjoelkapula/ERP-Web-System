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
						<span class="text-muted font-weight-light">Quote /</span> Update
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
	var stockItems = [];
	var store_id = <?php if(isset($store_id)) echo $store_id; else echo 0; ?>;
	var hub_id = <?php if(isset($hub_id)) echo $hub_id; else echo 0; ?>;
	EBQ_added = [];

	var formatter = new Intl.NumberFormat('en-US', {
					style: 'currency',
					currency: 'ZAR' ,
					minimumFractionDigits: 2,
					maximumFractionDigits: 2,
	});
	var selectedStock = <?php if(isset($selected_stock)){ 
			echo json_encode($selected_stock);
		} else{
			echo json_encode([]);
		};
	?>;

async function handleHubSelection(){
		$("#stock-search").val('');

		let itemRemoved = 0;
		hub_id = $("#hub-selector").val()
		return $.post("/quote/create/ajax/precheck_hubstock/", {hub_id : hub_id},
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


	function updateStoreID(e){
		store_id = e.target.value;
	}

	function handleRemoveStock(ebq){
		EBQ_added = EBQ_added.filter(item=> item !== ebq.id);
	}

	function updateTotal(e,ebq) {
		var totalText = $(`#${ebq}_total`);
		var input = $(`#${ebq}_hidden`);
		var updatedItem = stockItems.find(item=>item.EBQ_CODE === ebq);

		totalText.text(formatter.format((updatedItem.AVG_COST*(1+(updatedItem.MARKUP/100)))*parseInt(e.value)));
		input.val((updatedItem.AVG_COST*(1+(updatedItem.MARKUP/100)))*parseInt(e.value));
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

	async function assignSelectedStock(stockArr){
		await handleHubSelection();
        stockArr.forEach(stock => {
            // Check stock in stockItems
			foundItem = stockItems.find(item=>item.EBQ_CODE === stock.EBQ_CODE);

            //Find category row
            var stockCategoryRow = document.querySelector(`#stock-category_${stock.STOCK_CATEGORY}`);
			var existingEBQ = document.querySelector(`#${foundItem?.EBQ_CODE}`);
			var disabledMinus = "";
			if(stock.QUANTITY == 1) disabledMinus = "disabled";

            //Build stock row
			if(foundItem && !existingEBQ ){
				EBQ_added.push(foundItem.EBQ_CODE)
				row = document.createElement('tr');
				row.innerHTML = `
				<input type='hidden' name='stock[${foundItem.EBQ_CODE}][category]' value='${stock.STOCK_CATEGORY}'/>
				<input type='hidden' name='stock[${foundItem.EBQ_CODE}][hub]' value='${stock.HUB_ID}'/>
				<input type='hidden' name='stock[${foundItem.EBQ_CODE}][markup]' value='${foundItem.MARKUP}'/>
				<input type='hidden' name='stock[${foundItem.EBQ_CODE}][avg_cost]' value='${foundItem.AVG_COST}'/>
				<td id='${stock.EBQ_CODE}' class="py-3">${stock.EBQ_CODE}</td>
                <td class="py-3">${foundItem.DESCRIPTION}</td>
            	<td class="py-3">
            	<div class="input-group" style = "width: 170px;">
            			<span class="input-group-btn">
            				<button type="button" class="btn btn-default btn-number" ${disabledMinus} data-type="minus" data-field="stock[${stock.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
            				-
            				</button>
            			</span>
            			<input type="text" name="stock[${stock.EBQ_CODE}][quantity]" class="form-control input-number" value="${stock.QUANTITY}" min="1" max="${foundItem.QUANTITY}" onchange="handleInputChange(event.target);updateTotal(this,'${foundItem.EBQ_CODE}');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
            			<span class="input-group-btn">
            				<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${stock.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
            				+
            				</button>
            			</span>
            		</div>
				</td>
				<td class="py-3">${foundItem.METRIC_DESCRIPTION}</td>
                <td class="py-3">${formatter.format(foundItem.AVG_COST*(1+(foundItem.MARKUP/100)))}</td>
				<td id='${stock.EBQ_CODE}_total' class="py-3">${formatter.format(stock.QUANTITY * (parseFloat(foundItem.AVG_COST*(1+(foundItem.MARKUP/100)))))}</td>
				<input id='${stock.EBQ_CODE}_hidden' type='hidden' name='total[]' value='${stock.QUANTITY * (parseFloat(foundItem.AVG_COST*(1+(foundItem.MARKUP/100))))}' />
                <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick=handleRemoveStock(${stock.EBQ_CODE}) class="btn btn-circle btn-default btn-remove">X</span></a></td>
            `;

            //Insert Stock row
            	$(row).insertAfter(stockCategoryRow);
				$('#stock-search').val('');
			}
			else{
				if(existingEBQ){
					Swal.fire('Error!','The specified stock item has already been added!','error');
				}
				else{
					Swal.fire('Error!','The specified stock item does not exist!','error');
				}
			}
           
        });

		calculateTotals();

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
	
	$(document).ready(function() {	
		$('#store-selector').select2();
		$('#contractor-selector').select2();
		var $form = $('#quote-stock-wizard');
		var data = $form.serializeArray();
		var form_enable = data.find(item=> item.name ==='form_enable');

		if(selectedStock.length){
			assignSelectedStock(selectedStock);
		}	

		if (form_enable.value === 'false') {
			Swal.fire('Error!','This quote has already been approved or declined and cannot modified','error');
			$("#quote-stock-wizard :input").prop("disabled", true);
			$("#quote-stock-wizard :input[name='form_enable']").prop("disabled", false);
			$("#quote-stock-wizard :button").prop("disabled", false);
			$("#quote-stock-wizard :button.btn-number").prop("disabled", true);
			$("#quote-stock-wizard :a.btn-remove").prop("disabled", true);		
		}

		store_id = $('#store-selector').val();

	
		var tbl_stock_body = document.querySelector('#tbl_stock_body');
		
		$('#btn-stock-add').click(function() {
		itemName = $('#stock-search').val();
		itemCategory = $('#stock-category-selector').val();
		stockrow = $(`#stock-category_${itemCategory}`);
		item = stockItems.find(item=>item.EBQ_CODE+' : '+item.DESCRIPTION === itemName);
		row = document.createElement('tr');
		var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
        if (item && !existingEBQ) {
			EBQ_added.push(item.EBQ_CODE)
			row.innerHTML = `
			<input type='hidden' name='stock[${item.EBQ_CODE}][category]' value='${itemCategory}'/>
			<input type='hidden' name='stock[${item.EBQ_CODE}][hub]' value='${hub_id}' />
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
					<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control input-number" value="1" min="1" max="${item.QUANTITY}" onchange="handleInputChange(event.target);updateTotal(this,'${item.EBQ_CODE}');calculateTotals();" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handlePlusMinusClick(event)'>
						+
						</button>
					</span>
				</div>
			</td>
			<td class="py-3">${foundItem.METRIC_DESCRIPTION}</td>
			<td class="py-3">${formatter.format(item.AVG_COST)}</td>
			<td id='${item.EBQ_CODE}_total' class="py-3">${formatter.format(1 * parseFloat(item.AVG_COST))}</td>
			<input id='${item.EBQ_CODE}_hidden' type='hidden' name='total[]' value='${1 * parseFloat(item.AVG_COST)}' />

            <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove">X</span></a></td>
        	`;
			$(row).insertAfter(stockrow);
			$('#stock-search').val('');
			calculateTotals()
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