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
						<span class="text-muted font-weight-light">Requisition /</span> Create
					</h4>
					<?php require_once(APPPATH.'Views/requisition/form.php')?>
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
<script src="/assets/js/forms_requisition_validation.js"></script>
<script>
    var stockItems = <?php echo json_encode($stock); ?>;
    var tbl_stock_body = document.querySelector('#tbl_stock_body');
    //set autocomplete suggestion to search input
    stockItemNames = [];
    try {
        if(stockItems.length){
            stockItems.forEach(function(item, index) {
                stockItemNames[index] = item.largerItem.EBQ_CODE + ' : ' +  item.largerItem.DESCRIPTION;
            });
        }

        //set autocomplete suggestion to search input
        autocomplete(document.getElementById("stock-search"), stockItemNames);
    } catch (err) {
        console.error(err)
    }

    autocomplete(document.getElementById("stock-search"), stockItemNames);
	
$(document).ready(function() {		
	
	store_id = $('#store-selector').val();
	hub_id = $('#hub-selector').val();
    
	var tbl_stock_body = document.querySelector('#tbl_stock_body');
		
		$('#btn-stock-add').click(function() {
		itemName = $('#stock-search').val();
		item = stockItems.find(item=>item.largerItem.EBQ_CODE+' : '+item.largerItem.DESCRIPTION === itemName);
		
		var existingEBQ = document.querySelector(`#${item?.largerItem.EBQ_CODE}`);
        if (item && !existingEBQ) {
            thead =  document.querySelector(`#tbl_stock_head`);
            input = document.querySelector('#requisition_item');

            input.value = item.largerItem.EBQ_CODE;
            $('.larger-item').remove();
            $(tbl_stock_body).empty();
            tr = document.createElement('tr');
            tr.id = item.largerItem.EBQ_CODE;
            tr.className='thead-light larger-item';
            tr.innerHTML= `
                <th colspan="4"><strong>${item.largerItem.EBQ_CODE + ' : ' + item.largerItem.DESCRIPTION}</strong></th>
            `;
            thead.append(tr);
            item.subItems.forEach(function(subItem, index) {
                
                row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-3">${subItem.EBQ_CODE}</td>
                    <td class="py-3">${subItem.DESCRIPTION}</td>
                    <td class="py-3">${subItem.METRIC_DESCRIPTION}</td>
                    <td class="py-3">${subItem.QUANTITY}</td>
                `;
                tbl_stock_body.append(row);
            });
        
            $('#stock-search').val('');
        }
        else {
			if(existingEBQ){
				Swal.fire('Error!','The specified stock item has already been added!','error');
				$('#stock-search').val('');
			}
			else{
				Swal.fire('Error!','The specified stock item does not exist or is not built out of other items','error');
				$('#stock-search').val('');
			}
        }
    });

    onScan.attachTo(document, {
			suffixKeyCodes: [13], // enter-key expected at the end of a scan
			reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
			onScan: function(sCode, iQty) {
                item = stockItems.find(item=>item.largerItem.EBQ_CODE === sCode.replace(/\*/g, ''));
		
                var existingEBQ = document.querySelector(`#${item?.largerItem.EBQ_CODE}`);
                if (item && !existingEBQ) {
                    thead =  document.querySelector(`#tbl_stock_head`);
                    input = document.querySelector('#requisition_item');

                    input.value = item.largerItem.EBQ_CODE;
                    $('.larger-item').remove();
                    $(tbl_stock_body).empty();
                    tr = document.createElement('tr');
                    tr.id = item.largerItem.EBQ_CODE;
                    tr.className='thead-light larger-item';
                    tr.innerHTML= `
                        <th colspan="4"><strong>${item.largerItem.EBQ_CODE + ' : ' + item.largerItem.DESCRIPTION}</strong></th>
                    `;
                    thead.append(tr);
                    item.subItems.forEach(function(subItem, index) {
                        
                        row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="py-3">${subItem.EBQ_CODE}</td>
                            <td class="py-3">${subItem.DESCRIPTION}</td>
                            <td class="py-3">${subItem.METRIC_DESCRIPTION}</td>
                            <td class="py-3">${subItem.QUANTITY}</td>
                        `;
                        tbl_stock_body.append(row);
                    });
                
                    $('#stock-search').val('');
                }
                else {
                    if(existingEBQ){
                        Swal.fire('Error!','The specified stock item has already been added!','error');
                        $('#stock-search').val('');
                    }
                    else{
                        Swal.fire('Error!','The specified stock item does not exist or is not built out of other items','error');
                        $('#stock-search').val('');
                    }
                }
				
			},
			onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
			}
		});

    $("#tbl_stock").on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
        updateOrderTotal();
	});


});
	
	
</script>
<?php echo view('_general/footer');