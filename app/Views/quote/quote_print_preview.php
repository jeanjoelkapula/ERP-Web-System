<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<?php echo view('_general/navigation'); ?>

<!-- Layout content -->
<div class="layout-content">

	<!-- Content -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-4">
			Quote / Preview
		</h4>
		<div class="card">
			<?php require_once(APPPATH.'Views/quote/quote_print_base.php')?>
			<div class="card-footer text-right">
				<a href="/quote/printer/<?php if(isset($quote_id)) echo $quote_id;?>" target="_blank" class="btn btn-default"><i class="ion ion-md-print"></i>&nbsp; Print</a>
			</div>
		</div>
	</div>
    <!-- / Content -->
    
</div>
<!-- Layout content -->

<?php echo view('_general/footer_javascript'); ?> 
<script>
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

	function hideEmptyCategories(){
		emptyCategories = document.querySelectorAll('thead.thead-light > tr:nth-child(1):last-child');
		emptyCategories.forEach(category=> category.classList.add('hidden'))
	}

	function assignSelectedStock(stockArr){
        stockArr.forEach(stock => {
            // Check stock in stockItems
            //Find category row
            var stockCategoryRow = document.querySelector(`#stock-category_${stock.STOCK_CATEGORY}`);
			var existingEBQ = document.querySelector(`#${stock.EBQ_CODE}`);

            //Build stock row
			if(!existingEBQ ){
				row = document.createElement('tr');
				row.innerHTML = `
            	<input type='hidden' name='stock[${stock.EBQ_CODE}][category]' value='${stock.STOCK_CATEGORY}'/>
				<td id='${stock.EBQ_CODE}' class="py-3">${stock.EBQ_CODE}</td>
                <td class="py-3">${stock.DESCRIPTION}</td>
                <td class="py-3">${formatter.format(stock.AVG_COST*(1+(stock.MARKUP/100)))}</td>
            	<td class="py-3">${stock.QUANTITY}</td>
				<td id='${stock.EBQ_CODE}_total' class="py-3">${formatter.format(stock.QUANTITY * (stock.AVG_COST*(1+(stock.MARKUP/100))))}</td>
            `;

            //Insert Stock row
            	$(row).insertAfter(stockCategoryRow);
                $('#myInput').val('');
			}
			else{
				if(existingEBQ){
					swal('Stock already exists','The stock item has already been added','error');
					$('#myInput').val('');
				}
				else{
					Swal.fire('Error!','The specified stock item does not exist!','error');

					$('#myInput').val('');
				}
			}
           
        });

    }
	$(document).ready(function() {	
		if(selectedStock.length){
			assignSelectedStock(selectedStock);
			hideEmptyCategories();
		}	
	});
 </script>
<?php echo view('_general/footer');

