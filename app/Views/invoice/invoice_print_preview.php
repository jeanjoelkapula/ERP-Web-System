<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<?php echo view('_general/navigation'); ?>
<!-- Layout content -->
<div class="layout-content">
   <!-- Content -->
   <div class="container-fluid flex-grow-1 container-p-y">
      <h4 class="font-weight-bold py-3 mb-4">
         Invoice
      </h4>
      <div class="card">
        <?php require_once(APPPATH.'Views/invoice/invoice_base.php')?>
        <div class="card-footer text-right">
            <a href="/invoice/printer/<?php if(isset($invoice['id'])) echo $invoice['id'];?>" target="_blank" class="btn btn-default"><i class="ion ion-md-print"></i>&nbsp; Print</a>
        </div>
      </div>
   </div>
   <!-- / Content -->
</div>
<!-- Layout content -->
<?php echo view('_general/footer_javascript'); ?> 
<script>

const taxPercentage = <?php if(isset($tax['percentage'])) echo $tax['percentage']; else echo 0.00?>;
const invoiced_stock = <?php if(isset($invoice_stock)){ 
	echo json_encode($invoice_stock);
} else{
	echo json_encode([]);
};
?>;
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


function assignSelectedStock(stockArr,stockSource){
        stockArr.forEach(stock => {
            //Find category row
            var stockCategoryRow = document.querySelector(`#tbl_stock_table_${stockSource}`).querySelector(`tr.stock-category_${stock.STOCK_CATEGORY}`);
			var existingEBQ = document.querySelector(`#tbl_stock_table_${stockSource}`).querySelector(`#${stock?.EBQ_CODE}`);
			var disabledMinus = "";

            //Build stock row
			if(!existingEBQ ){
				row = document.createElement('tr');
				var marked_up_cost = parseFloat(stock.AVG_COST)+(stock.AVG_COST*(stock.MARKUP/100));
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

$(document).ready(function() {
	renderStockSourceCards(Object.keys(invoiced_stock));
	Object.keys(invoiced_stock).forEach(source =>{
		assignSelectedStock(invoiced_stock[source],source);
		hideEmptyCategories(source);
	})

});

</script>
<?php echo view('_general/footer');