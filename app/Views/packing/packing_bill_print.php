
<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<!-- Layout content -->
    <?php require_once(APPPATH.'Views/packing/packing_bill_preview_base.php')?>

<!-- Layout content -->
<script>
var selectedStock = <?php if(isset($stock_to_pack)){ 
	echo json_encode($stock_to_pack);
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
			<td id='${stock.EBQ_CODE}' class="py-3">${stock.EBQ_CODE}</td>
			<td class="py-3">${stock.DESCRIPTION}</td>
			<td class="py-3">${stock.QUANTITY}</td>
			<td> <input type="text" name="stock[${stock.EBQ_CODE}][quantity]" class="form-control input-number" style="width:55px" value="" min="1" max="99">  </td>
			<td/>
			<label class='custom-control custom-checkbox'>
				<input type='checkbox' class='custom-control-input'> 
				<span class='custom-control-label'></span>
			</label> 
			</td>
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
}	
hideEmptyCategories()
  // -------------------------------------------------------------------------
  // Print on window load
$(function () {
    window.print();
  });

});
</script>
<?php echo view('_general/footer_javascript'); ?> 
<?php echo view('_general/footer');