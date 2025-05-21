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
                <span class="text-muted font-weight-light">VOC /</span> Search

                <span class="float-right">
                    <a href="/voc/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New VOC</a>
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
                        <th>VOC ID</th>
                        <th>Order No</th>
                        <th>Date Created</th>
                        <th>Approval Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                       <tbody>
                        <?php 
                        $sql = "select * from TBL_VOC";
						$query = $db->query($sql);
						foreach($query->getResult() as $row){
                        ?>
						<tr>
							<td><?php echo $row->VOC_ID?></td>
							<td><?php echo $row->ORDER_NO?></td>
							<td><?php echo $row->CREATED_DTM?></td>
                            <td><?php echo $row->VOC_STATUS?></td>
                            <td> 
                                <div class="btn-group dropdown-toggle-hide-arrow">  
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                        <?php if(($row->VOC_STATUS != 'APPROVED') && ($row->VOC_STATUS != 'DECLINED')) {?>
                                            <a class="dropdown-item"  href="/voc/update/<?php echo $row->VOC_ID; ?>"  >Edit</a>
                                            <div class="dropdown-divider"></div>
                                        <?php } ?> 
                                        <a class="dropdown-item " href="/voc/detail/<?php echo $row->VOC_ID?>">Details</a>
                                        <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                                           
                                            <a class="dropdown-item" href="javascript:void(0)"<?php if($row->VOC_STATUS == 'APPROVED') echo 'style="display:none;"';?> onclick="approve_voc('<?php echo $row->VOC_ID?>')">Approve</a>
                                            <a class="dropdown-item" href="javascript:void(0)"<?php if($row->VOC_STATUS == 'DECLINED') echo 'style="display:none;"';?> onclick="decline_voc('<?php echo $row->VOC_ID?>')">Decline</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
						</tr>
						<?php } ?>

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