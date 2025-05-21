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
				<span class="text-muted font-weight-light">Job /</span> File Upload
			</h4>
			<br/>             
			<div class="card">
				<div class="card mb-4">
					<h6 class="card-header">
						Dropzone
					</h6>
					<div class="card-body">
						<form action="/job/upload?job=<?php foreach($job_info as $info) {echo $info['JOB_ID'];} ?>" class="dropzone needsclick dz-clickable" id="dropzone" method="POST" enctype="multipart/form-data">
							<div class="dz-message needsclick">
								Drop files here or click to upload
							</div>
                            <input type="hidden" name="job" id="job" value="<?php foreach($job_info as $info) {echo $info['JOB_ID'];} ?>"/>
                            <input type="hidden" name="order_no" id="order_no" value="<?php foreach($job_info as $info) {echo $info['ORDER_NO'];} ?>"/>
                            <input type="hidden" name="created_date" id="created_date" value="<?php foreach($job_info as $info) {
                                
                                $splitInput = explode(" ",$info['CREATED_DATE']);

                                    $stripDate = $splitInput[0];
                                    
                                    echo $stripDate;
                                } ?>"/>
							<input type="hidden" name="upload" id ="upload" value="true"/>
						</form>
					</div>
				</div>
				<div style="justify-content: center; display: flex; padding-bottom: 20px;">
					<button id="upload-button" type="button" class="btn btn-primary mr-2" ><span style="color:white; margin-left:auto; margin-right:auto;" ><i class="ion ion-md-cloud-upload" ></i>&nbsp; Upload</span></button>
				</div>
			</div>
		</div>
	</div>
	<!-- Layout content -->
</div>
<?php echo view('_general/footer_javascript'); ?> 
<script src="/assets/vendor/libs/datatables/datatables.js"></script>
<script src="/assets/js/forms_file-upload_job.js"></script>
<script>
	$(document).ready(function() {
	   
	    
	   
	});
</script>
<?php echo view('_general/footer');