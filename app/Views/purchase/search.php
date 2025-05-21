<?php 
    echo view('_general/header'); 
    $ionAuth = new \IonAuth\Libraries\IonAuth();
?>

<div class="layout-wrapper layout-2">
<div class="layout-inner">    
    
<?php echo view('_general/navigation'); ?>


<div class="layout-container">

    <?php echo view('_general/navigation_top'); ?>

    <!-- Layout content -->
    <div class="layout-content">
            
        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y">

            <h4 class="font-weight-bold py-3 mb-4">
                <span class="text-muted font-weight-light">Purchase Order /</span> Search

                <span class="float-right">
                    <a href="/purchase/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New order </a>
                    &nbsp;<button onclick="download_purchase_list()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Purchase Order List</button>
                </span>
            </h4>

        
            <br/>             
            <div class="card">
                <div class="card-datatable table-responsive">

                    <div id="datatable_data_loading" style="text-align:center; padding:150px;">
                        <i class="far fa-5x fa-spinner-third fa-spin"></i>
                        <h4 class="mt-5">loading...</h4>
                    </div>

                    <table id="datatable_data" class="table table-striped table-bordered font-sm" style="display:none">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order Date</th>
                                <th>Date Required</th>
                                <th>Vendor</th>
                                <th>Total</th>
                                <th>Approval Status</th>
                                <th>Fulfilled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            foreach ($result->getResult('array') as $row): {
                                $approved = $row['APPROVAL_STATUS'] ==="APPROVED"; 
                                $declined = $row['APPROVAL_STATUS'] ==="DECLINED";
                                $ful_filled = $row['FUL_FILLED'] ==1;
                        ?> 
                            <tr>
                                <td><?php echo $row['PURCHASE_ORDER_ID'];?></td>
                                <td><?php echo $row['ORDER_DATE'];?></td>
                                <td><?php echo $row['DATE_REQUIRED'];?></td>
                                <td><?php echo $row['VENDOR_NAME'];?></td>
                                <td><?php echo 'R '.$row['TOTAL'];?></td>
                                <td><?php echo $row['APPROVAL_STATUS'];?></td>
                                <td>
                                    <div class = "row">  
                                        <div class="col-sm-3 pb-4">                                
                                            <?php   
                                                if ($row['FUL_FILLED'] == 1) {
                                                    echo "  <label class='custom-control custom-checkbox'>
                                                                <input type='checkbox' checked='checked' name='' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'>
                                                            <span class='custom-control-label'></span>
                                                            </label>
                                                        ";
                                                }
                                                else {
                                                    echo "  <label class='custom-control custom-checkbox'>
                                                                <input type='checkbox' name='SD' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'> 
                                                            <span class='custom-control-label'></span>
                                                            </label>
                                                        ";
                                                }
                                                ?>    
                                        </div>
                                    <div>
                                </td>
                                <td>
                                <?php if(($ionAuth->isAdmin($ionAuth->user()->row()->id)) || ($ionAuth->inGroup('electrical_manager'))){
                                ?>
                                    <div class="btn-group dropdown-toggle-hide-arrow">  
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                        <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                <?php
                                                    if ($row['FUL_FILLED'] != 1) {
                                                
                                                ?>
                                                        <a class="dropdown-item <?php if($approved || $declined) echo 'disabled';?>" href="<?php if($approved || $declined) echo 'javascript:void(0)';else echo "/purchase/update/".$row['PURCHASE_ORDER_ID'];?>">Edit</a>
                                                <?php
                                                    }
                                                ?>
                                            <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                                                <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item <?php if($approved) echo 'disabled';?>" href="javascript:void(0)" onclick="<?php if (!$approved) echo  "approve_order('".$row['PURCHASE_ORDER_ID']."');"; else echo 'javascript:void(0)'; ?>">Approve</a>
                                                    <a class="dropdown-item <?php if($declined) echo 'disabled';?>" href="javascript:void(0)" onclick="<?php if (!$declined) echo  "decline_order('".$row['PURCHASE_ORDER_ID']."');"; else echo 'javascript:void(0)'; ?>">Decline</a>
                                                <div class="dropdown-divider"></div>
                                            <?php
                                                }
                                            ?>
                                            <a class="dropdown-item" href="/purchase/preview/<?php echo $row['PURCHASE_ORDER_ID']; ?>">Preview</a>
                                        </div>
                                    </div>
                                <?php
                                  }
                                ?>
                                </td>
                            </tr>  
                        <?php 
                            } endforeach; 
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

function download_purchase_list(){
    window.location.href="/purchase/search/dl/purchase_list";

    Ladda.bind('button[type=ladda]');

    setTimeout(function(){
        Ladda.stopAll();
    }, 3000);
}

function approve_order(order_no){
    $.ajax({
		'url': '/purchase/update/form_approve_purchase/' + order_no,
		'type': 'get',
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
		'url': '/purchase/update/form_decline_purchase/' + order_no,
		'type': 'get',
		'dataType': 'json',
		'beforeSend': function () {
		},
	})
	.done( function (response) {
        console.log(response);
        if(response=='ok'){
            Swal.fire({
            title: 'Success!',
            text: "Order Declined",
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
       
   
});
</script>
<?php echo view('_general/footer');    