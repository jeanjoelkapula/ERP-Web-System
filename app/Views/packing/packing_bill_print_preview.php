<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<?php echo view('_general/navigation'); ?>

<!-- Layout content -->
<div class="layout-content">

	<!-- Content -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-4">
			Packing Bill
		</h4>
		<div class="card">
			<?php require_once(APPPATH.'Views/packing/packing_bill_preview_base.php')?>
			<div class="card-footer text-right">

				<a href="/packing/printer/<?php if(isset($packing_bill_id)) echo $packing_bill_id; ?>" target="_blank" class="btn btn-default"><i class="ion ion-md-print"></i>&nbsp; Print</a>

			</div>
		</div>
	</div>
    <!-- / Content -->
    
</div>
<!-- Layout content -->

<?php echo view('_general/footer_javascript'); ?> 
<script>
var selectedStock = <?php if(isset($stock_to_pack)){ 
	echo json_encode($stock_to_pack);
} else{
	echo json_encode([]);
};
?>;

function assignSelectedStock(stockArr){
	stockArr.forEach(stock => {
		// Check stock in stockItems
		//Find category row
		var tbl_stock_body = document.querySelector(`#tbl_stock_body`);
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
			$(tbl_stock_body).append(row);
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
console.log('selected stock is:',selectedStock);
if(selectedStock.length){
	assignSelectedStock(selectedStock);
}	
});
</script>
<?php echo view('_general/footer');

