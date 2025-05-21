<?php echo view('_general/header'); ?>

<div class="layout-wrapper layout-2">
<div class="layout-inner">    
    
<?php echo view('_general/navigation'); ?>

<?php
///  USE 1 JAN 2019 to include all data 
$min_date = date("Ymd", strtotime("2019-01-01"));

// Get the date of the last scheduled order as default max date
$sql = "select ORDER_DATE_CREATED from TBL_ORDER order by 1 desc limit 1";
$query = $db->query($sql);
$max_date = date("Ymd");
foreach($query->getResult() as $row){
    $max_date = date("Ymd", strtotime($row->ORDER_DATE_CREATED));
}

if ($s_date_from == -1 || $s_date_from == '') 
$s_date_from = $min_date;

if ($s_date_to == -1 || $s_date_to == '') 
$s_date_to = $max_date; 

$date_from = date("Ymd", strtotime($s_date_from));
$date_to = date("Ymd");


?>
<div class="layout-container">

    <?php echo view('_general/navigation_top'); ?>

    <!-- Layout content -->
    <div class="layout-content">
            
        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y">

            <h4 class="font-weight-bold py-3 mb-4">
                <span class="text-muted font-weight-light">Order /</span> Search
                <?php 
                    if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                ?>
                    <span class="float-right">
                        <a href="/order/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Order</a>
                        &nbsp; | &nbsp;<button onclick="download_orderlist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Orders</button>
                    </span>
                <?php
                    }
                ?>
            </h4>

        
            <br/>             

           <!-- Filters -->
			<form method="post" action="/order/search" autocomplete="off">
				<input type="hidden" name="filter" value="true" />
				<div class="ui-bordered px-4 pt-4 mb-4">
				<div class="form-row align-items-center">
					<div class="col-md mb-4">
					<label class="form-label">Date From</label>
					    <div class="input-group date">
                            <input id="s_date_from" name="s_date_from" type="text" class="form-control input-date " value="<?php echo date('Y-m-d', strtotime($s_date_from))?>">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
					</div>
					<div class="col-md mb-4">
					<label class="form-label">Date To</label>
					    <div class="input-group date">
                            <input id="s_date_to" name="s_date_to" type="text" class="form-control input-date " value="<?php echo date('Y-m-d')?>">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
					</div>    
					<div class="col-md col-xl-2 mb-4">
					<label class="form-label d-none d-md-block">&nbsp;</label>
					<button type="submit" class="btn btn-primary btn-block">Search</button>
					</div>
				</div>
				</div>
			</form>
            <!-- / Filters -->
            <div class="card">
                <div class="card-datatable table-responsive">

                    <div id="datatable_data_loading" style="text-align:center; padding:150px;">
                        <i class="far fa-5x fa-spinner-third fa-spin"></i>
                        <h4 class="mt-5">loading...</h4>
                    </div>

                    <table id="datatable_data" class="table table-striped table-bordered font-sm" style="display:none">
                    <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Date Created</th>
                        <th>Internal Order?</th>
                        <th>Status</th>   
                        <th>Created By</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                       <tbody>
                        <?php 
                        $sql = "select o.*, u.first_name, u.last_name from TBL_ORDER o
                                inner join TBL_USER u on u.id = o.USER_ID";
                        $sql .= " and o.ORDER_DATE_CREATED >= '$date_from' and o.ORDER_DATE_CREATED < date_add('$date_to', interval 1 day)";
                        if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_administrator')){
                            $sql .= " and o.STATUS != 'PENDING'";
                        }
						$query = $db->query($sql);
						foreach($query->getResult() as $row){
                        ?>
						<tr>
							<td><?php echo $row->ORDER_NO?></td>
							<td><?php echo $row->ORDER_DATE_CREATED?></td>
                            <td>
                                No
                            </td>
							<td><?php echo $row->STATUS?></td>
							<td><?php echo $row->first_name." ".$row->last_name?></td>
                            <td> 
                                <div class="btn-group dropdown-toggle-hide-arrow">  
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                        <a class="dropdown-item " href="/order/update/<?php echo $row->ORDER_NO?>">Details</a>
                                        <div class="dropdown-divider"></div>
                                        <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                                            <a class="dropdown-item" href="javascript:void(0)"<?php if($row->STATUS == 'APPROVED') echo 'style="display:none;"';?> onclick="approve_order('<?php echo $row->ORDER_NO?>')">Approve</a>
                                            <a class="dropdown-item" href="javascript:void(0)"<?php if($row->STATUS == 'DECLINED') echo 'style="display:none;"';?> onclick="decline_order('<?php echo $row->ORDER_NO?>')">Decline</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
						</tr>
                        <?php }
                        
                            $sql = "SELECT O.*, U.first_name, U.last_name FROM TBL_ORDER_INTERNAL O
                                    INNER JOIN TBL_USER U ON U.id = O.USER_ID  WHERE (O.DATE_CREATED >= '$date_from') AND (O.DATE_CREATED < date_add('$date_to', interval 1 day));";
                            $query = $db->query($sql);
                            foreach($query->getResult() as $row){
                        ?>
                                <tr>
                                    <td><?php echo $row->ORDER_NO?></td>
                                    <td><?php echo $row->DATE_CREATED?></td>
                                    <td>
                                        Yes
                                    </td>
                                    <td><?php echo $row->STATUS?></td>
                                    <td><?php echo $row->first_name." ".$row->last_name?></td>
                                    <td> 
                                        <div class="btn-group dropdown-toggle-hide-arrow">  
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                <a class="dropdown-item " href="/order/update/<?php echo $row->ORDER_NO?>">Details</a>
                                                <div class="dropdown-divider"></div>
                                                <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                                                    <a class="dropdown-item" href="javascript:void(0)"<?php if($row->STATUS == 'APPROVED') echo 'style="display:none;"';?> onclick="approve_order('<?php echo $row->ORDER_NO?>')">Approve</a>
                                                    <a class="dropdown-item" href="javascript:void(0)"<?php if($row->STATUS == 'DECLINED') echo 'style="display:none;"';?> onclick="decline_order('<?php echo $row->ORDER_NO?>')">Decline</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                        <?php 

                            }
                        ?>
                        </tbody>
                    </table>

                </div>
            </div>    
        </div>            
            
    </div>
    <!-- Layout content -->

</div>
    
<?php echo view('_general/footer_javascript'); ?> 


<script src="/assets/vendor/libs/datatables/datatables.js"></script>

<script>

function download_orderlist(){
    window.location.href="/order/search/dl/orderlist?s_date_from=<?php echo $s_date_from?>&s_date_to=<?php echo $s_date_to?>";

    Ladda.bind('button[type=ladda]');

    setTimeout(function(){
        Ladda.stopAll();
    }, 3000);
}

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

$(document).ready(function() {
   
   var dtable = $('#datatable_data').DataTable({
        order: [],
        pageLength: 25,
        responsive: true,
        language: { search: "Filter Results" },
        dom : "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
              "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"             
    });      
    
    
    
    $('#datatable_data_loading').hide();
    $('#datatable_data').show(); 
       
    $('.input-date').datepicker({format: "yyyy-mm-dd", autoclose: true });
});
</script>
<?php echo view('_general/footer');    