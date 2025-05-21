<?php echo view('_general/header'); ?>

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
                <span class="text-muted font-weight-light">Requisition /</span> Search

                <span class="float-right">
                    <a href="/requisition/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New </a>
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
                        <th>Requisition Date</th>
                        <th>Hub</th>
                        <th>Item</th>
                        <th>Is Complete</th>                        
                        <th>Expected Completion</th>
                        <th>Approval Status</th>  
                        <th>Actions</th>
                    </tr>
                    </thead>
                        <tbody>                                                     
                            <?php foreach($requisitions as $item): { 
                                
                                $declined = ($item['APPROVAL_STATUS'] == 'DECLINED');
                                $approved = ($item['APPROVAL_STATUS'] == 'APPROVED');
                                $complete = ($item['IS_COMPLETE'] == 1);
                            ?> 
                            <tr>
                                <td>
                                    <?php echo $item['REQUISITION_NO']; ?>
                                </td>  
                                <td>
                                    <?php echo $item['REQUISITION_DATE']; ?>
                                </td> 
                                <td>
                                    <?php echo $item['HUB_NAME']; ?>
                                </td> 
                                <td>
                                    <?php echo $item['EBQ_CODE']; ?>
                                </td> 
                                <td>
                                    <div class = "row">  
                                        <div class="col-sm-3 pb-4">                                
                                            <?php   
                                                if ($item['IS_COMPLETE'] == 1) {
                                                    echo "  <label class='custom-control custom-checkbox'>
                                                                <input type='checkbox' checked='checked' name='inbusiness' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'>
                                                                <span class='custom-control-label'></span>
                                                            </label>
                                                        ";
                                                }
                                                else {
                                                    echo "  <label class='custom-control custom-checkbox'>
                                                                <input type='checkbox' name='inbusiness' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'> 
                                                                <span class='custom-control-label'></span>
                                                            </label>
                                                        ";
                                                }
                                                ?>    
                                        </div>
                                    <div>
                                <td>
                                    <?php echo $item['EXPECTED_COMPLETION']; ?>
                                </td>
                                <td>
                                    <?php echo $item['APPROVAL_STATUS']; ?>
                                </td>
                                <td>
                                    <div class="btn-group dropdown-toggle-hide-arrow">  
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                        <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                            <a class="dropdown-item btn btn-outline-secondary <?php if($approved || $declined) echo 'disabled';?>" href="/requisition/update/<?php echo $item['REQUISITION_NO']; ?>" <?php if ($declined || $approved) {echo 'disabled';} ?>>Edit</a>
                                            <?php 
                                                if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                                            ?>
                                                <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item   <?php if($approved) {echo 'disabled';}?>" href="javascript:void(0)" onclick="<?php if (!$approved) echo  'approve_order('.$item['REQUISITION_NO'].');'; else echo 'javascript:void(0)'; ?>" >Approve</a>
                                                    <a class="dropdown-item   <?php if($declined || $complete) echo 'disabled'; ?>" href="javascript:void(0)" onclick="<?php if (!$approved || !$declined) echo  'decline_order('.$item['REQUISITION_NO'].');'; else echo 'javascript:void(0)'; ?>">Decline</a>
                                                <div class="dropdown-divider"></div>
                                            <?php 
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </td>                                                                                                                                                           
                            </tr>  
                            <?php }endforeach; ?>    
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

function approve_order(order_no){
    $.ajax({
		'url': '/requisition/update/approve/' + order_no,
		'type': 'get',
		'dataType': 'json',
		'beforeSend': function () {
		}
	})
	.done( function (response) {
        if(response=='ok'){
            Swal.fire({
            title: 'Success!',
            text: "Requisition Approved",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
             window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error approving requisition!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}

function decline_order(order_no){
    $.ajax({
		'url': '/requisition/update/decline/' + order_no,
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
            text: "Requisition Declined",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
             window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error approving requisition!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});

}

</script>

<?php echo view('_general/footer'); 