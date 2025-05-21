
<?php 
$sql = "SELECT o.*,q.QUOTE_ID,q.QUOTE_TYPE_ID,q.NOTE AS QUOTE_NOTE,q.APPROVED_DATE as QUOTE_APPROVED_DTM,
q.TOTAL,qt.TYPE_NAME,c.CONTRACTOR_NAME,c.CONTACT_NUMBER,c.EMAIL as CONTRACTOR_EMAIL, v.*,q.CREATED_DATE as QUOTE_CREATED_DATE, q.STATUS as QUOTE_STATUS
FROM TBL_ORDER o
INNER JOIN TBL_QUOTE q ON q.QUOTE_ID = o.QUOTE_ID
INNER JOIN TBL_QUOTE_TYPE qt ON qt.TYPE_ID = q.QUOTE_TYPE_ID
INNER JOIN TBL_CONTRACTOR c ON c.CONTRACTOR_ID = q.CONTRACTOR_ID
INNER JOIN TBL_VOC v ON v.ORDER_NO = o.ORDER_NO
WHERE v.VOC_ID = '".$this->data['entity_id']."'";
$query = $db->query($sql);
$num_rows = count($query->getResult());

$data = $query->getRow();

if(!$data){  
    return redirect()->to('/order/search');
    exit();
}

?>

<?php echo view('_general/header'); ?>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-2">
    <div class="layout-inner">
        <?php echo view('_general/navigation'); ?>
        <!-- Layout container -->
        <div class="layout-container">
            <?php echo view('_general/navigation_top'); ?>
            <!-- Layout content -->
            <div class="layout-content">
                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y pt-0">
                    <div class="media align-items-center py-3 mb-3">
                        <div class="media-body ml-4">
                            <h4 class="font-weight-bold mb-0">VOC: <?php echo $data->VOC_ID?></h4>
                            <div class="text-muted mb-2">Details</div>
                        </div>
                    </div>
                
                    <div class="card pt-3 pl-3">
                        <div class="row">
                            <div class="col-4">
                                <h4>VOC Details </h4>
                                <br/>
                                <div class="form-group">
                                    <label class="col form-label">VOC ID</label>
                                    <div class="col"><input type="text" readonly class="form-control" id="voc_id" required="" value="<?php echo $data->VOC_ID?>"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col form-label">Date Created</label>
                                    <div class="col"><input type="text" readonly class="form-control" id="voc_created_dtm" required="" value="<?php echo $data->CREATED_DTM?>"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col form-label">Status</label>
                                    <div class="col"><input type="text" readonly class="form-control" id="voc_created_dtm" required="" value="<?php echo $data->VOC_STATUS?>"></div>
                                </div>
                                <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager') ){ ?>
                                    <div class="form-group pb-5">
                                        <button class="btn btn-outline-success" <?php if($data->VOC_STATUS == 'APPROVED') echo 'style="display:none;"';?> onclick="approve_voc('<?php echo $data->VOC_ID?>')">Approve VOC</button>
                                        <button class="btn btn-outline-danger float-right" <?php if($data->VOC_STATUS == 'DECLINED') echo 'style="display:none;"';?> onclick="decline_voc('<?php echo $data->VOC_ID?>')">Decline VOC</button>
                                    </div>
                                <?php } ?>
                                <hr>
                                <h4> Order Details </h4>
                                <br>
                                <div class="form-group">
                                    <label class="col form-label">Order Number</label>
                                    <div class="col"><input type="text" disabled class="form-control" name="order_no" id="order_no"  value="<?php echo $data->ORDER_NO?>"/></div>    
                                </div>
                                <div class="form-group">
                                    <label class="col form-label">Order Status</label>
                                    <div class="col"><input type="text" disabled class="form-control" name="order_status" id="order_status"  value="<?php echo $data->STATUS?>"/></div>    
                                </div>
                                <div class="form-group">
                                    <label class="col form-label">Order Notes</label>
                                    <div class="col"><textarea type="text" disabled class="form-control" name="order_notes" id="order_notes"  value=""><?php echo $data->ORDER_NOTES ?></textarea><small><i>Any additional order notes.</i></small></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4> VOC Stock </h4>
                                <br>
                                <div class="table-responsive mb-4">
                                    <table class="table m-0 font-sm" id = "tbl_stock">
                                        <thead>
                                            <tr>
                                                <th class="py-3">
                                                    Code
                                                </th>
                                                <th class="py-3">
                                                    Description
                                                </th>
                                                <th class="py-3">
                                                    Price
                                                </th>
                                                <th class="py-3">
                                                    Quantity
                                                </th>
                                                <th class="py-3">
                                                    Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody  id = "tbl_voc_stock_body">
                                            <?php
                                                $sql = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY;";
                                                $result = $db->query($sql);
                                                foreach ($result->getResult('array') as $row) { ?>
                                            <tr>
                                        <thead class="thead-light">
                                            <tr  id="stock-voc-category_<?php echo $row['ID']?>">
                                                <th colspan="5"><strong><?php echo $row['NAME']?></strong></th>
                                            </tr>
                                        </thead>
                                        </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Content -->
        </div>
        <!-- Layout content -->
    </div>
    <!-- / Layout container -->
</div>
<!-- Overlay -->
<div class="layout-overlay layout-sidenav-toggle"></div>
</div>
<!-- / Layout wrapper -->
<?php echo view('_general/footer_javascript'); ?>
<script>
var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'ZAR' ,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
	    });

  function approve_voc(voc_id){
    $.ajax({
		'url': '/voc/detail/ajax/approve_voc',
		'data': {
			voc_id:voc_id
		},
		'type': 'post',
		'dataType': 'json',
		'beforeSend': function () {
		}
	})
	.done( function (response) {
        if(response=='ok'){
            Swal.fire({
            title: 'Success!',
            text: "VOC Approved",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
             window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error approving VOC!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}

function decline_voc(voc_id){
    $.ajax({
		'url': '/voc/detail/ajax/decline_voc',
		'data': {
			voc_id:voc_id
		},
		'type': 'post',
		'dataType': 'json',
		'beforeSend': function () {
		}
	})
	.done( function (response) {
        if(response=='ok'){
            Swal.fire({
            title: 'Success!',
            text: "VOC Declined!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
                window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error declining VOC!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}
function order_get_voc_stock(){
	
	$.post("/voc/detail/ajax/order_get_voc_stock/", {voc_id:<?php echo $data->VOC_ID?>},
	function(result){
		var quoteStock = JSON.parse(result);
		
		Object.entries(quoteStock).forEach(entry => {
			const [key, value] = entry;

			row = document.createElement('tr');
			var itemCat = value.STOCK_CATEGORY;
			var stockrow = $(`#stock-voc-category_${itemCat}`);
			var tbl_stock_body = document.querySelector('#tbl_voc_stock_body');
			row.className += "item";
			row.innerHTML = `
				<input type='hidden' name='stock[${value.EBQ_CODE}][category]' value='${itemCat}'/>
				<td class="py-3 ebq">${value.EBQ_CODE}</td>
				<td class="py-3">${value.DESCRIPTION}</td>
				<td class="py-3">${formatter.format(value.AVG_COST*(1+(value.MARKUP/100)))}</td>
				<td class="py-3">
				<div class="input-group" style = "width: 170px;">
						
						<input type="text" name="stock[${value.EBQ_CODE}][quantity]" readonly class="form-control quant input-number" value="${value.QUANTITY}" min="1" max="5" onchange='handleInputChange(event.target);updateTotal(this,'${value.EBQ_CODE}')' onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
						
					</div>
				</td>
				<td id='#${value.EBQ_CODE}'class="py-3">${formatter.format((1 * value.AVG_COST*(1+(value.MARKUP/100))) * value.QUANTITY )}</td>

				
			`;
				
				$(row).insertAfter(stockrow);
				// tbl_stock_body.append(row);
				$('#myInput').val('');
				
		})
		

	})

}
    $(document).ready(function() {
        
        order_get_voc_stock();

        
    
    });
</script>
<?php echo view('_general/footer');

