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
						<span class="text-muted font-weight-light">Invoice /</span> Create
					</h4>
					<?php require_once(APPPATH.'Views/invoice/invoice_wizard_base.php')?>
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
const taxPercentage = <?php if(isset($tax['percentage'])) echo $tax['percentage']; else echo 0.00?>;
function hideEmptyCategories(stockSource){
	emptyCategories = document.querySelectorAll(`#tbl_stock_table_${stockSource} > thead.thead-light > tr:nth-child(1):last-child`);
	emptyCategories.forEach(category=> category.classList.add('hidden'))
}

var formatter = new Intl.NumberFormat('en-US', {
	style: 'currency',
	currency: 'ZAR' ,
	minimumFractionDigits: 2,
	maximumFractionDigits: 2,
});
var sub_total = 0;


function assignSelectedStock(stockArr,stockSource){
	var total = 0;
        stockArr.forEach(stock => {
            //Find category row
            var stockCategoryRow = document.querySelector(`#tbl_stock_table_${stockSource}`).querySelector(`tr.stock-category_${stock.STOCK_CATEGORY}`);
			var existingEBQ = document.querySelector(`#tbl_stock_table_${stockSource}`).querySelector(`#${stock?.EBQ_CODE}`);
			var disabledMinus = "";

            //Build stock row
			if(!existingEBQ ){
				row = document.createElement('tr');
				var marked_up_cost = parseFloat(stock.AVG_COST)+(stock.AVG_COST*(stock.MARKUP/100));
				total = total+(marked_up_cost*stock.QUANTITY);
				row.innerHTML = `
				<input type='hidden' name='stock[${stockSource}][${stock.EBQ_CODE}][origin]' value='${stockSource}'/>
				<input type='hidden' name='stock[${stockSource}][${stock.EBQ_CODE}][category]' value='${stock.STOCK_CATEGORY}'/>
				<input type='hidden' name='stock[${stockSource}][${stock.EBQ_CODE}][quantity]' value='${stock.QUANTITY}' />	
				<input type='hidden' name='stock[${stockSource}][${stock.EBQ_CODE}][avg_cost]' value='${stock.AVG_COST}' />	
				<input type='hidden' name='stock[${stockSource}][${stock.EBQ_CODE}][markup]' value='${stock.MARKUP}' />	
				<input type='hidden' name='stock[${stockSource}][${stock.EBQ_CODE}][hub_id]' value='${stock.HUB_ID}' />	

				<td id='${stock.EBQ_CODE}' class="py-3">${stock.EBQ_CODE}</td>
                <td class="py-3">${stock.DESCRIPTION}</td>
            	<td class="py-3">${stock.QUANTITY}</td>
            	<td class="py-3">${formatter.format(marked_up_cost)}</td>
            	<td class="py-3">${formatter.format(stock.QUANTITY*marked_up_cost)}</td>
			`;

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
		var $discount = $('#discount-amount').text()
		sub_total += total;
		calculateTotals(sub_total,$discount);
    }

function calculateTotals(subTotal, discount){
	$('#sub_total').val(subTotal);
	var discountAmount = subTotal * (discount/100);
	$('#sub-total').text(formatter.format(subTotal));
	$('#discount-total').text(formatter.format(discountAmount));
	$('#discount-percentage').text(discount+"%");
	$('#tax-excl-amount').text(formatter.format(subTotal-discountAmount));
	$('#tax-amount').text(formatter.format((subTotal-discountAmount)*(taxPercentage/100)));
	$('#total-amount').text(formatter.format((subTotal-discountAmount)+((subTotal-discountAmount)*(taxPercentage/100))));
	$("input[name='total']").val((subTotal-discountAmount)+((subTotal-discountAmount)*(taxPercentage/100)));
}

function handleDiscountChange(){
	var $discountPercentage = $("input[name='discount']").val();
	var $subTotal = $('#sub_total').val();
	calculateTotals($subTotal,$discountPercentage);
}

function renderStockSourceCards(stockSources){
	stockSources.forEach(source =>{
		var card = `
		<div class="card mt-4" id="stock_card_${source}">
            <div class="card-body">
                <h6 class="card-header">${source} Stock Items</h6>
                <table class="table m-0 font-sm" id="tbl_stock_table_${source}" >
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
                                Unit Price
                            </th>
                            <th class="py-3">
                                Total
                            </th>   

                        </tr>
                    </thead>
                    <tbody >
                        <?php
                            $sql = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY;";
                            $result = $db->query($sql);
                            foreach ($result->getResult('array') as $row) { ?>
                                <tr>
                                    <thead class="thead-light">
                                        <tr  class="stock-category_<?php echo $row['ID']?>">
                                            <th colspan="6"><strong><?php echo $row['NAME']?></strong></th>
                                        </tr>
                                    </thead>
                                </tr>
                                
                           <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
		`;
		$('#source-card-container').append(card);
	})
}

function getOrderQuoteStock(){
	let order_id = $('#order_id').val();

	$.post("/invoice/create/ajax/order_get_quote_stock/", {order_no : order_id},
		function(result){
			var stock = JSON.parse(result);
			renderStockSourceCards(Object.keys(stock));
			Object.keys(stock).forEach(source =>{
				assignSelectedStock(stock[source],source);
				hideEmptyCategories(source);
		})
	})
}

function getOrderQuoteDetails(){
	let order_id = $('#order_id').val();

	$.post("/invoice/create/ajax/order_get_quote_details/", {order_no : order_id},
		function(result){
			var obj = JSON.parse(result);
			$("input[name='store'").val(obj[0].STORE_ID+" - "+obj[0].STORE_NAME);
			$("input[name='job_type'").val(obj[0].JOB_TYPE_DESCRIPTION);
			$("input[name='job_id'").val(obj[0].JOB_ID);
		})
}


$(document).ready(function() {
	var $btnFinish = $('<button class="btn-finish btn btn-primary hidden mr-2" type="button">Finish</button>');
	var $form = $('#invoice-wizard');

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
	$('#order_id').select2();
	// $('#stock-category-selector').select2();

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
         
        },
        messages: {
         }
    });

	$form.on('leaveStep', function(e, anchorObject, stepNumber, stepDirection) {
      // stepDirection === 'forward' :- this condition allows to do the form validation
      // only on forward navigation, that makes easy navigation on backwards still do the validation when going next

		if(stepNumber == 0 && stepDirection == "forward" ){
			getOrderQuoteDetails();
			getOrderQuoteStock();
		} else if (stepNumber == 1 && stepDirection == 'forward'){

		}else if (stepNumber == 2 && stepDirection == 'forward'){
		}
		else if (stepNumber == 3 && stepDirection == 'forward'){
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