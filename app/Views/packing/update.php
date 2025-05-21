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
						<span class="text-muted font-weight-light">Packing Bill /</span> Create
					</h4>
					<?php require_once(APPPATH.'Views/packing/packing_wizard_base.php')?>
				</div>
				<!-- Content -->
			</div>
			<!-- Layout content -->
		</div>
		<!-- Layout content -->
	</div>
</div>
<?php echo view('_general/footer_javascript'); ?> 
<script>

function handleDeliverToSite(){
	checked = $("input[name='site-delivery']").is(':checked') 
	
	if(checked){
		$("select[name='destination_hub']").attr('disabled', 'disabled');
	}
	else{
		$("select[name='destination_hub']").removeAttr('disabled');

	}
}

function handleCheckboxClicked(){
	let checkboxesChecked = true;
	var all = $(".check-packed").map(function() {
		if(!this.checked){
			checkboxesChecked = false;
		}
    return this.checked;
	}).get();

	if(checkboxesChecked){
		$('.btn-finish').removeClass('disabled');
	}
	else{
		$('.btn-finish').addClass('disabled');
	}
}

function hideEmptyCategories(){
	emptyCategories = document.querySelectorAll('thead.thead-light > tr:nth-child(1):last-child');
	emptyCategories.forEach(category=> category.classList.add('hidden'))
}

function assignSelectedStock(stockArr){
	$('.thead-light').show();
	$('.table-row').remove();
        stockArr.forEach(stock => {
            // Check stock in stockItems
			// stock = stockItems.find(item=>item.EBQ_CODE === stock.EBQ_CODE);

            //Find category row
            var stockCategoryRow = document.querySelector(`#stock-category_${stock.STOCK_CATEGORY}`);
			var existingEBQ = document.querySelector(`#${stock?.EBQ_CODE}`);
			var disabledMinus = "";
			// if(stock.QUANTITY == 1) disabledMinus = "disabled";

            //Build stock row
			if(!existingEBQ ){
				row = document.createElement('tr');
				row.className = "table-row";
				row.innerHTML = `
				<input type='hidden' name='stock[${stock.EBQ_CODE}][category]' value='${stock.STOCK_CATEGORY}'/>
				<input type='hidden' name='stock[${stock.EBQ_CODE}][quantity]' value='${stock.QUANTITY}' />	

				<td id='${stock.EBQ_CODE}' class="py-3">${stock.EBQ_CODE}</td>
                <td class="py-3">${stock.DESCRIPTION}</td>
            	<td class="py-3">${stock.QUANTITY}</td>
            	<td class="py-3">${stock.METRIC_DESCRIPTION}</td>
			`;
			//		<td class="py-3"> <input class='check-packed' type="checkbox" name='stock[${stock.EBQ_CODE}][packed]' value=false onclick='handleCheckboxClicked()' /> </td>


            //Insert Stock row
            	$(row).insertAfter(stockCategoryRow);
                $('#stock-search').val('');
			}
			else{
				if(!existingEBQ){
					Swal.fire('Error!',`${stock?.EBQ_CODE} does not exist!`,'error');
				}
			}
           
        });

    }
function getOrderQuoteDetails(){
	let order_id = $('#order_id').val();

	$.post("/packing/create/ajax/order_get_quote_details/", {order_no : order_id},
		function(result){
			var obj = JSON.parse(result);
			$("input[name='store_id'").val(obj[0].STORE_ID);
			$("input[name='ship_via'").val(obj[0].SHIP_VIA);
			$("input[name='delivery_date'").val(obj[0].DELIVERY_DATE);
		})
}

function getOrderQuoteStock(source_hub){
	let order_id = $('#order_id').val();

	$.post("/packing/create/ajax/order_get_quote_stock/", {order_no : order_id,hub_id:source_hub},
		function(result){
			var stock = JSON.parse(result);
			if(stock && stock.length){
				assignSelectedStock(stock);
				hideEmptyCategories();
			}
	})
}

function getSourceHubs(){
	let order_id = $('#order_id').val();
	var select = document.querySelector("select[name='source_hub']")
	if(select){
		select.options.length=0;
	}
	$.post("/packing/create/ajax/get_source_hubs/", {order_no : order_id},
		function(result){
			var hubs = JSON.parse(result);
			if(hubs){
				hubs.forEach(hub => {
					select.add(new Option(hub.HUB_NAME, hub.HUB_ID));
					$("input[name='source_hub_id']").val(hub.HUB_ID);
				});
			}
	})
}

function getInternalOrderDetails() {
	let order_id = $('#order_id').val();

	$.post("/packing/create/ajax/get_internal_order_details/", {order_no : order_id},function(result){
		var obj = JSON.parse(result);
		var sourceHub = $('#source_hub');
		var destinationHub = $('#destination_hub');
		var storeFormGroup = $('#store_form_group');

		sourceHub.val(obj.SOURCE_HUB_ID);
		destinationHub.val(obj.DESTINATION_HUB_ID);
		destinationHub.attr('disabled', 'disabled');
		$("input[name='source_hub_id']").val(obj.SOURCE_HUB_ID);
		$("input[name='destination_hub']").val(obj.DESTINATION_HUB_ID);
		storeFormGroup.hide();

		
	})
}

function getInternalOrderStock() {
	let order_id = $('#order_id').val();
	var tbl_stock_body = $('#tbl_stock_body');
	$('.thead-light').hide();
	$('.table-row').remove();
	$.post("/packing/create/ajax/get_internal_order_stock/", {order_no : order_id},function(result){
		var stock = JSON.parse(result);
		if(stock){
			stock.forEach(item => {
				row = document.createElement('tr');
				row.className = "table-row";
				row.innerHTML = `
					<input type='hidden' name='stock[${item.EBQ_CODE}][category]' value='${item.STOCK_CATEGORY}'/>
					<input type='hidden' name='stock[${item.EBQ_CODE}][quantity]' value='${item.QUANTITY}' />	

					<td id='${item.EBQ_CODE}' class="py-3">${item.EBQ_CODE}</td>
					<td class="py-3">${item.DESCRIPTION}</td>
					<td class="py-3">${item.QUANTITY}</td>
					<td class="py-3">${item.METRIC_DESCRIPTION}</td>
				`;
				$(tbl_stock_body).append(row);
			});
		}
	})
}

$(document).ready(function() {
	var $btnFinish = $('<button class="btn-finish btn btn-primary hidden mr-2" type="button">Finish</button>');
	var $form = $('#packing-bill-wizard');
	$.validator.addMethod(
		'valid-pack-date',
		function(value, element) {
			console.log('value',value);
		return value <= $("input[name='delivery_date']").val();
		}
 	);
	$form.validate({
        errorPlacement: function errorPlacement(error, element) {
        $(element).parents('.form-group').append(
            error.addClass('invalid-feedback small d-block')
        )
        },
        highlight: function(element) {
        $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
        $(element).removeClass('is-invalid');
        },
        rules: {
          packing_date:{
			  "valid-pack-date":true
		  }
        },
        messages: {
			packing_date:{
			  "valid-pack-date": "Suggested Pack date must be before the Delivery date"
		  }
        }
	});
	
	$form.smartWizard({
		autoAdjustHeight: false,
		backButtonSupport: false,
		useURLhash: false,
		showStepURLhash: false,
		toolbarSettings: {
		toolbarExtraButtons: [$btnFinish],
		toolbarPosition: 'bottom'
      }
    });
    <?php if(!isset($order_no)) echo "$('#order_id').select2();"?>
	// $('#stock-category-selector').select2();

	$form.on('leaveStep', function(e, anchorObject, stepNumber, stepDirection) {
      // stepDirection === 'forward' :- this condition allows to do the form validation
      // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
	  	if(stepNumber == 0 && stepDirection == "forward" ){
			var order_no = $('#order_id').val();
			if(order_no === null || order_no === '' || order_no=== undefined || order_no?.replace(/\s/g, '').length == 0){
				e.preventDefault();
				Swal.fire('Incomplete!','Please select an order number!','error');
			}else{
				if (order_no.includes('INT')) {
					getInternalOrderDetails();
				}
				else {
					getOrderQuoteDetails();
					getSourceHubs();

				}
			}

			
		} else if (stepNumber == 1 && stepDirection == 'forward'){
			var sourceHub = $("select[name='source_hub']").val();
			var order_no = $('#order_id').val();

			if (order_no.includes('INT')) {
				getInternalOrderStock();
			}
			else {
				getOrderQuoteStock(sourceHub);
			}
		}else if (stepNumber == 2 && stepDirection == 'backward'){

		}
		if (stepDirection === 'forward'){ return $form.valid(); }
      return true;
    })
    .on('showStep', function(e, anchorObject, stepNumber, stepDirection) {
		var btn = $form.find('.btn-finish');
	  // Enable finish button only on last step
		if(stepNumber === 2){
			btn.removeClass('hidden');
			// btn.addClass('disabled');
		}
		else{
			btn.addClass('hidden');
			// btn.removeClass('disabled');
		}
	});
	
  // Click on finish button
  $form.find('.btn-finish').on('click', function(e){
	if(e.target.classList.contains('disabled')){
		e.preventDefault();
		return;
	}
    if (!$form.valid()){ return; }
     
    $form.submit();
    return false;

  });
});
	
	
</script>
<?php echo view('_general/footer');