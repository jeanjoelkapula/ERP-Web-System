<?php echo view('_general/header'); ?>

<?php 

$sql = "SELECT * FROM TBL_ORDER WHERE ORDER_NO = '".$this->data['entity_id']."'";
$result = $db->query($sql)->getResultArray();
if ($is_internal == 0) {
	if ($result[0]['QUOTE_ID'] != null) {
		$sql = "SELECT o.*,q.QUOTE_ID,q.QUOTE_TYPE_ID,q.NOTE AS QUOTE_NOTE,q.APPROVED_DATE as QUOTE_APPROVED_DTM,
		q.TOTAL,qt.TYPE_NAME,c.CONTRACTOR_NAME,c.CONTACT_NUMBER,c.EMAIL as CONTRACTOR_EMAIL, v.*,q.CREATED_DATE as QUOTE_CREATED_DATE, q.STATUS as QUOTE_STATUS
		FROM TBL_ORDER o
		left JOIN TBL_QUOTE q ON q.QUOTE_ID = o.QUOTE_ID
		left JOIN TBL_QUOTE_TYPE qt ON qt.TYPE_ID = q.QUOTE_TYPE_ID
		left JOIN TBL_CONTRACTOR c ON c.CONTRACTOR_ID = q.CONTRACTOR_ID
		left JOIN TBL_VOC v ON v.ORDER_NO = o.ORDER_NO
		WHERE o.ORDER_NO = '".$this->data['entity_id']."'";
		$query = $db->query($sql);
		$num_rows = count($query->getResult());

		$data = $query->getRow();
		if(empty($data->QUOTE_ID)){
			$data->QUOTE_ID = 0;
		}
	}
	else {
		$sql = "SELECT V.ORDER_NO FROM TBL_ORDER O
		INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID
		WHERE O.ORDER_NO = '".$this->data['entity_id']."'";

		$result = $db->query($sql)->getResultArray();

		$sql = "SELECT V.*, O.*, C.CONTRACTOR_ID, C.CONTRACTOR_NAME, C.EMAIL AS CONTRACTOR_EMAIL, C.CONTACT_NUMBER, S.* FROM TBL_ORDER O        
		INNER JOIN TBL_VOC V ON V.VOC_ID = O.VOC_ID          
		INNER JOIN (SELECT Q.*, O.ORDER_NO FROM TBL_ORDER O
			INNER JOIN TBL_QUOTE Q ON Q.QUOTE_ID = O.QUOTE_ID         
			WHERE (O.ORDER_NO = '".$result[0]['ORDER_NO']."')) Q ON Q.ORDER_NO = Q.ORDER_NO
		INNER JOIN TBL_CONTRACTOR C ON C.CONTRACTOR_ID = Q.CONTRACTOR_ID 
		INNER JOIN TBL_STORE S ON S.STORE_ID = Q.STORE_ID
		WHERE (O.ORDER_NO = '".$this->data['entity_id']."');";

		$query = $db->query($sql);
		$num_rows = count($query->getResult());

		$data = $query->getRow();
		if(empty($data->QUOTE_ID)){
			$data->QUOTE_ID = 0;
		}
	}

	if(!$data){  
		return redirect()->to('/order/search');
		exit();
	}
}



?>

<div class="layout-wrapper layout-2">
<div class="layout-inner">    
    
<?php echo view('_general/navigation'); ?>

<div class="layout-container">

    <?php echo view('_general/navigation_top'); ?>

    <!-- Layout content -->
    <div class="layout-content">

        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y" id="outer_wrapper">

            <div class="media align-items-center py-3 mb-3">
              <div class="media-body ml-4">
                <h4 class="font-weight-bold mb-0"><?php echo $this->data['entity_id']?></h4>
                <div class="text-muted mb-2">Details</div>
              </div>
            </div>

			<div class="row">
              <div class="col">

                <div class="nav-tabs-top mb-4">
                  <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#tab-details">Order Detail</a>
                    </li>

                    
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-details">
                      
					  <?php 
					  	if ($is_internal == 1) {
							require_once(APPPATH.'Views/order/internal_order_view_details.php');
						}
						else {
							require_once(APPPATH.'Views/order/view_detail.php');
						}
					  
					  ?>
					  
                    </div>

                    
                  </div>
                </div>



            </div>


            
            
        </div>


    </div>
    <!-- Layout content -->

</div>


<?php echo view('_general/footer_javascript'); ?> 

<script src="/assets/vendor/libs/datatables/datatables.js"></script>

<script>
<?php if(!empty($data->VOC_ID)) { ?> 
		order_get_voc_stock();
<?php } ?>
	$(document).ready(function() {

	<?php if($is_internal == 1){ ?>
			order_get_internal_stock();
	<?php } else { ?>
			order_get_quote_stock();
	<?php }?>
	
	   
	});

  function approve_order(order_no){
    $.ajax({
		'url': '/order/update/ajax/approve_order',
		'data': {
			order_no:order_no
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
            text: "Order Approved",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
             window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error approving order!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}

function decline_order(order_no){
    $.ajax({
		'url': '/order/update/ajax/decline_order',
		'data': {
			order_no:order_no
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
            text: "Order Declined!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
                window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error declining order!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}
var formatter = new Intl.NumberFormat('en-US', {
					style: 'currency',
					currency: 'ZAR' ,
					minimumFractionDigits: 2,
					maximumFractionDigits: 2,
	});
function order_get_quote_stock(){
	
	$.post("/order/create/ajax/order_get_quote_stock/", {quote_id:<?php if ($is_internal ==0) echo $data->QUOTE_ID; else echo 'none'; ?>},
	function(result){
		var quoteStock = JSON.parse(result);
		
		Object.entries(quoteStock).forEach(entry => {
			const [key, value] = entry;

			row = document.createElement('tr');
			var itemCat = value.STOCK_CATEGORY;
			var stockrow = $(`#stock-category_${itemCat}`);
			var tbl_stock_body = document.querySelector('#tbl_stock_body');
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
				<td id='#${value.EBQ_CODE}'class="py-3">${formatter.format(value.AVG_COST*(1+(value.MARKUP/100)) * value.QUANTITY )}</td>

				
			`;
				
				$(row).insertAfter(stockrow);
				// tbl_stock_body.append(row);
				$('#myInput').val('');
				
		})
		

	})

}

function order_get_internal_stock(){

	$.post("/order/create/ajax/order_get_internal_stock/", {order_no:'<?php echo $this->data['entity_id'];?>'},
	function(result){
		var quoteStock = JSON.parse(result);
		
		Object.entries(quoteStock).forEach(entry => {
			const [key, value] = entry;

			row = document.createElement('tr');
			var itemCat = value.STOCK_CATEGORY;
			var stockrow = $(`#stock-category_${itemCat}`);
			var tbl_stock_body = document.querySelector('#tbl_stock_body');
			row.className += "item";
			row.innerHTML = `
				<input type='hidden' name='stock[${value.EBQ_CODE}][category]' value='${itemCat}'/>
				<td class="py-3 ebq">${value.EBQ_CODE}</td>
				<td class="py-3">${value.DESCRIPTION}</td>
				<td class="py-3">R${value.AVG_COST}</td>
				<td class="py-3">
				<div class="input-group" style = "width: 170px;">
						
						<input type="text" name="stock[${value.EBQ_CODE}][quantity]" readonly class="form-control quant input-number" value="${value.QUANTITY}" min="1" max="5" onchange='handleInputChange(event.target);updateTotal(this,'${value.EBQ_CODE}')' onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
						
					</div>
				</td>
				<td id='#${value.EBQ_CODE}'class="py-3">R${1 * parseFloat(value.AVG_COST)}</td>

				
			`;
				
				$(row).insertAfter(stockrow);
				// tbl_stock_body.append(row);
				$('#myInput').val('');
				
		})
		

	})

}

function order_get_voc_stock(){
	
	$.post("/order/create/ajax/order_get_voc_stock/", {voc_id:<?php if(!empty($data->VOC_ID)){ echo $data->VOC_ID;} else {echo 0;}?>},
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
				<td class="py-3">R${value.AVG_COST}</td>
				<td class="py-3">
				<div class="input-group" style = "width: 170px;">
						
						<input type="text" name="stock[${value.EBQ_CODE}][quantity]" readonly class="form-control quant input-number" value="${value.QUANTITY}" min="1" max="5" onchange='handleInputChange(event.target);updateTotal(this,'${value.EBQ_CODE}')' onkeydown='handleKeyDown(event)' onfocusin='handleFocus(event.target)'>
						
					</div>
				</td>
				<td id='#${value.EBQ_CODE}'class="py-3">R${1 * parseFloat(value.AVG_COST)}</td>

				
			`;
				
				$(row).insertAfter(stockrow);
				// tbl_stock_body.append(row);
				$('#myInput').val('');
				
		})
		

	})

}
    
</script>

<?php echo view('_general/footer');