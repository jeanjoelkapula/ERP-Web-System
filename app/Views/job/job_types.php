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
                    <h4 class="font-weight-bold py-3 mb-4">
                    <span class="text-muted font-weight-light">Job Type/</span> Search

                    <span class="float-right">
                        <?php if($ionAuth->isAdmin($_user_id)){?>
                            <button type="button" class="btn btn-primary btn-primary-outline waves-effect" onclick="modal_show_jobtype();"><span class="ion ion-md-add"></span>&nbsp; Add Job Type</button>
                        <?php } ?>
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
                                    <th>Job Type</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "select * from TBL_JOB_TYPE";
                                $query = $db->query($sql);
                                foreach($query->getResult() as $row){
                                ?>
                                <tr>
                                    <td><?php echo $row->JOB_TYPE_DESCRIPTION ?></td>
                                    <td><?php if($row->ACTIVE == 1) { echo 'Yes';} else { echo 'No';}?></td>
                                    <td> 
                                        <div class="btn-group dropdown-toggle-hide-arrow">  
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                <?php if($ionAuth->isAdmin($_user_id)){ ?>
                                                   
                                                    <a class="dropdown-item" href="javascript:void(0)"<?php if($row->ACTIVE == 0) echo 'style="display:none;"';?> onclick="disable_jobtype('<?php echo $row->JOB_TYPE_ID?>')">Disable</a>
                                                    <a class="dropdown-item" href="javascript:void(0)"<?php if($row->ACTIVE == 1) echo 'style="display:none;"';?> onclick="enable_jobtype('<?php echo $row->JOB_TYPE_ID?>')">Enable</a>
                                                   
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

<div class="modal fade" id="modal-job-type" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" id="modal-loadingoverlay-wrapper">
        <form class="modal-content" method="POST" action="/job/create/create_jobtype" autocomplete="off" id="frm-job-type">
        <input type="hidden" name="frm_create_jobtype" value="true"/>
        <div class="modal-header">
          <h5 class="modal-title">
            Job Types / <span class="font-weight-light">New</span><br>
            <small class="text-muted">Add a new Job Type.</small>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
        </div>
        <div class="modal-body">

          <div class="form-row">
            <div class="form-group col">
              <label class="form-label">Description</label>
              <input type="text" class="form-control" name="new_job_type_name" id="new_job_type_name" required="">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
</div>

<?php echo view('_general/footer_javascript'); ?>



<script>

function modal_show_jobtype()
{
    
    $('#modal-job-type').modal({show:true});
}

function enable_jobtype(job_type_id){
    $.ajax({
		'url': '/job/update/ajax/enable_jobtype',
		'data': {
			job_type_id:job_type_id
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
            text: "Jobtype Enabled!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
             window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error Updating jobtype!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}

function disable_jobtype(job_type_id){
    $.ajax({
		'url': '/job/update/ajax/disable_jobtype',
		'data': {
			job_type_id:job_type_id
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
            text: "Jobtype Disabled!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
                window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error Updating jobtype!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}

function create_jobtype(){

    
    


    
}

$(document).ready(function() {
    $("textarea").not(".allowemoji").keyup(function(){

    var strng = $(this).val();

    var cleanStr = removeEmojis(strng);

    $(this).val(cleanStr);

    });


    $("input").not(".allowemoji").keyup(function(){

    var strng = $(this).val();

    var cleanStr = removeEmojis(strng);

    $(this).val(cleanStr);

    });

   
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
