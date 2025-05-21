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
						<span class="text-muted font-weight-light"> Journal /</span> Create
					</h4>
					<?php require_once(APPPATH.'Views/journal/form.php')?>
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
<script>
	var stockItems = [];
	var hub_id = 0;
	var tbl_stock_body = document.querySelector('#tbl_stock_body');
	var EBQ_added = [];
    //set autocomplete suggestion to search input
    stockItemNames = [];
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
			stockItemNames = [];
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

	function handleRemoveStock(ebq){
		if(ebq.id){
			ebq = ebq.id;
		}
		EBQ_added = EBQ_added.filter(item=> item !== ebq);
	}

	function updateTotal(e,ebq) {
		var totalText = $(`#${ebq}_total`);
		var input = $(`#${ebq}_hidden`);
		var updatedItem = stockItems.find(item=>item.EBQ_CODE === ebq);
		var quantity_difference = parseInt(e.value) - updatedItem.QUANTITY;
		totalText.text(formatter.format(item.AVG_COST*quantity_difference));
		input.val(item.AVG_COST*quantity_difference);
	}
	
$(document).ready(function() {	

		// Initialize with options
		onScan.attachTo(document, {
			suffixKeyCodes: [13], // enter-key expected at the end of a scan
			reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
			onScan: function(sCode, iQty) {
				stockBody = $(`#tbl_stock_body`);
				item = stockItems.find(item=>item.EBQ_CODE === sCode.replace(/\*/g, ''));
				row = document.createElement('tr');
				var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
				if (item && !existingEBQ) {
					EBQ_added.push(item.EBQ_CODE);
					row.innerHTML = `
					<td id='${item.EBQ_CODE}' class="py-3">${item.EBQ_CODE}</td>
					<td class="py-3">${item.DESCRIPTION}</td>
					<td class="py-3">
					<div class="input-group" style = "width: 170px;">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handleUnrestrictedPlusMinusClick(event)'>
								-
								</button>
							</span>
							<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control" value="${item.QUANTITY}" min=0 max="1000" onchange="updateTotal(this,'${item.EBQ_CODE}');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
							<span class="input-group-btn">
								<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handleUnrestrictedPlusMinusClick(event)'>
								+
								</button>
							</span>
						</div>
					</td>
					<td id='${item.EBQ_CODE}_total' class="py-3">${formatter.format(0 * parseFloat(item.AVG_COST))}</td>
					<input id='${item.EBQ_CODE}_hidden' type='hidden' name='stock[${item.EBQ_CODE}][total]' value='${0 * parseFloat(item.AVG_COST)}' />
					<input type='hidden' name='stock[${item.EBQ_CODE}][current_quantity]' value='${item.QUANTITY}' />

					<td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(${item.EBQ_CODE})" class="btn btn-circle btn-default btn-remove">X</span></a></td>
				`;
					$(row).insertAfter(stockBody);
					$('#stock-search').val('');
				}
				// 			<td class="py-3">${formatter.format(item.AVG_COST*(1+(item.MARKUP/100)))}</td>
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
				
			},
			onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
			}
		});


		handleHubSelection();	
		
		$('#btn-stock-add').click(function() {
		itemName = $('#stock-search').val();
		stockBody = $(`#tbl_stock_body`);
		item = stockItems.find(item=>item.EBQ_CODE+' : '+item.DESCRIPTION === itemName);
		row = document.createElement('tr');
		var existingEBQ = document.querySelector(`#${item?.EBQ_CODE}`);
        if (item && !existingEBQ) {
			EBQ_added.push(item.EBQ_CODE);
			row.innerHTML = `
			<td id='${item.EBQ_CODE}' class="py-3">${item.EBQ_CODE}</td>
			<td class="py-3">${item.DESCRIPTION}</td>
			<td class="py-3">
			<div class="input-group" style = "width: 170px;">
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handleUnrestrictedPlusMinusClick(event)'>
						-
						</button>
					</span>
					<input type="text" name="stock[${item.EBQ_CODE}][quantity]" class="form-control" value="${item.QUANTITY}" min=0 max="1000" onchange="updateTotal(this,'${item.EBQ_CODE}');" onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="stock[${item.EBQ_CODE}][quantity]" onclick='handleUnrestrictedPlusMinusClick(event)'>
						+
						</button>
					</span>
				</div>
			</td>
			<td id='${item.EBQ_CODE}_total' class="py-3">${formatter.format(0 * parseFloat(item.AVG_COST))}</td>
			<input id='${item.EBQ_CODE}_hidden' type='hidden' name='stock[${item.EBQ_CODE}][total]' value='${0 * parseFloat(item.AVG_COST)}' />
			<input type='hidden' name='stock[${item.EBQ_CODE}][current_quantity]' value='${item.QUANTITY}' />

            <td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" onclick="handleRemoveStock(${item.EBQ_CODE})" class="btn btn-circle btn-default btn-remove">X</span></a></td>
        `;
			$(row).insertAfter(stockBody);
            $('#stock-search').val('');
		}
		// 			<td class="py-3">${formatter.format(item.AVG_COST*(1+(item.MARKUP/100)))}</td>
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