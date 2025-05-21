<?php echo view('_general/header'); ?>
<?php 
$job_type_search = '';
if($s_job_type_id <> -1){
	$job_type_search = " where j.JOB_TYPE_ID = $s_job_type_id ";
}


?>
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
                    <h4 class="font-weight-bold py-3 mb-4">
                    <span class="text-muted font-weight-light">Job /</span> Search

                    <span class="float-right">         
                        <a href="/job/create/" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Job</a>
                        &nbsp; | &nbsp;<button onclick="download_joblist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Job List</button>
                    </span>
                    </h4>

                
                    <br/>             

                    <!-- Filters -->
                    <form method="post" action="/job/search" autocomplete="off">
                        <input type="hidden" name="filter" value="true" />
                        <div class="ui-bordered px-4 pt-4 mb-4">
                        <div class="form-row align-items-center">
                            <div class="col-md mb-4">
                            <label class="form-label">Job Type</label>
                            <select class="custom-select" name="s_job_type_id">
                                <option value="-1" <?php if($s_job_type_id == -1) echo 'selected'?>>All</option>
                                <?php $sql = "select JOB_TYPE_ID as value,JOB_TYPE_DESCRIPTION as description from TBL_JOB_TYPE"; 
                                    gen_select_dropdown($db,$sql,$s_job_type_id);
                                ?>
                            </select>
                            </div>
                            <!-- <div class="col-md mb-4">
                            <label class="form-label">Status</label>
                            <select class="custom-select" name="s_status_id">
                                <option value="-1" <?php// if($s_status_id == -1) echo 'selected'?>>All</option>
                                <option value="1" <?php //if($s_status_id == 1) echo 'selected'?>>Active</option>
                                <option value="0" <?php// if($s_status_id == 0) echo 'selected'?>>Suspended</option>
                            </select>
                            </div>     -->
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
                                    <th>Job ID</th>
                                    <th>Job Type</th>
                                    <th>Order Number</th>
                                    <th>Created Date</th>   
                                    <th>Notes</th>
                                    <th>Completion Date</th>
                                    <th>Job Level</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "select j.*,jt.JOB_TYPE_DESCRIPTION from TBL_JOB j
                                        inner join TBL_JOB_TYPE jt on jt.JOB_TYPE_ID = j.JOB_TYPE_ID
                                        $job_type_search ";
                                $query = $db->query($sql);
                                foreach($query->getResult() as $row){
                                ?>
                                <tr>
                                    <td><?php echo $row->JOB_ID ?></td>
                                    <td><?php echo $row->JOB_TYPE_DESCRIPTION ?></td>
                                    <td><?php echo $row->ORDER_NO ?></td>
                                    <td><?php echo $row->CREATED_DATE ?></td>
                                    <td><?php echo $row->NOTES ?></td>
                                    <td><?php if($row->COMPLETION_DATE == NULL){ echo 'In Progress';}else {echo $row->COMPLETION_DATE;}?></td>
                                    <td><?php echo $row->JOB_LEVEL?></td>
                                    <td><?php echo $row->JOB_STATUS?></td>
                                    <td>
                                        <div class="btn-group dropdown-toggle-hide-arrow">  
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">

                                                <a class="dropdown-item" href="<?php echo "/job/update/".$row->JOB_ID?>">Details</a>
                                                <a class="dropdown-item" href="/job/documents?job=<?php echo $row->JOB_ID; ?>">Documents</a>
                                                <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item " <?php if($row->JOB_STATUS == 'CANCELLED') echo 'style="display:none;"';?> onclick="cancel_job(<?php echo $row->JOB_ID?>)">Cancel Job</a>
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

function download_joblist(){
    window.location.href="/job/search/dl/joblist?s_job_type_id=<?php echo $s_job_type_id?>";

    Ladda.bind('button[type=ladda]');

    setTimeout(function(){
        Ladda.stopAll();
    }, 3000);
}

function complete_job(job_id){
    $.ajax({
		'url': '/job/update/ajax/complete_job',
		'data': {
			job_id:job_id
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
            text: "Job Completed!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
             window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error completing job!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}

function cancel_job(job_id){
    $.ajax({
		'url': '/job/update/ajax/cancel_job',
		'data': {
			job_id:job_id
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
            text: "Job Cancelled!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
                window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error cancelling job!','error');
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
