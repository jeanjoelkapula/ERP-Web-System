<?php echo view('_general/header'); 



$sql = "select j.*,o.QUOTE_ID
        from TBL_JOB j
        inner join TBL_ORDER o on o.ORDER_NO = j.ORDER_NO
        where j.JOB_ID = ".$this->data['entity_id'];

$query = $db->query($sql);
$num_rows = count($query->getResult());

$data = $query->getRow();

if(!$data){  
    return redirect()->to('/job/search');
    exit();
}
$jobtext = '';
if($data->JOB_LEVEL == 1){
    $jobtext = 'Level 1 - 24 Hour Response';
} 

if($data->JOB_LEVEL == 2){
    $jobtext = 'Level 2 - 2 Week Response';
}

if($data->JOB_LEVEL == 3){
    $jobtext = 'Level 3 - Under A Month Response';
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
                    <div class="media align-items-center py-3 mb-3">
                        <div class="media-body ml-4">
                            <h4 class="font-weight-bold mb-0">Job: <?php echo $data->JOB_ID?></h4>
                            <div class="text-muted mb-2">Details</div>
                        </div>
                    </div>
                    <div class="card mr-3 mt-3 pt-3 pl-3">
                        
                        <div class="row">
                            <div class="col-4">
                                <h4>Job Quote & Order</h4>
                                <br/>
                                <div class="form-group">
                                    <label class="col form-label">Job Order</label>
                                    <a href="/order/update/<?php echo $data->ORDER_NO?>"><button class="btn btn-outline-primary"><?php echo $data->ORDER_NO?></button></a>
                                </div>
                                <br/>
                                <?php if(!empty($data->QUOTE_ID)){ ?>
                                <div class="form-group">
                                    <label class="col form-label">Job Quote</label>
                                    <a href="/quote/update/<?php echo $data->QUOTE_ID?>"><button class="btn btn-outline-primary"><?php echo $data->QUOTE_ID?></button></a>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-6">
                                <h4>Job Details</h4>
                                <br/>
                                <div class="form-group">
                                <label class="col form-label">Job ID</label>
                                    <div class="col"><input type="text" readonly class="form-control" required="" value="<?php echo $data->JOB_ID?>"></div>
                                </div>
                                <div class="form-group">
                                <label class="col form-label">Job Status</label>
                                    <div class="col"><input type="text" readonly class="form-control" required="" value="<?php echo $data->JOB_STATUS?>"></div>
                                </div>
                                <div class="form-group">
                                <label class="col form-label">Created Date</label>
                                    <div class="col"><input type="text" readonly class="form-control" required="" value="<?php echo $data->CREATED_DATE?>"></div>
                                </div>
                                <div class="form-group">
                                <label class="col form-label">Job Notes</label>
                                    <div class="col"><textarea type="text" readonly class="form-control" required="" value=""><?php echo $data->NOTES?></textarea></div>
                                </div>
                                <div class="form-group">
                                <label class="col form-label">Job Level</label>
                                    <div class="col"><input type="text" readonly class="form-control" required="" value="<?php echo $jobtext?>"></div>
                                </div>
                                <div class="form-group">
                                <label class="col form-label">Job Completion Date</label>
                                    <div class="col"><input type="text" readonly class="form-control" required="" value="<?php if($data->COMPLETION_DATE == null){echo 'Not Completed';} else {echo $data->COMPLETION_DATE;}?>"></div>
                                </div>
                                <br/>
                                <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                                    <div class="form-group pb-5">
                                        <button class="btn btn-outline-danger float-right" onclick="cancel_job('<?php echo $data->JOB_ID?>')">Cancel Job</button>
                                    </div>
                                <?php } ?>
                              
                                
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
	$(function() {



	});
</script>

<?php echo view('_general/footer');
