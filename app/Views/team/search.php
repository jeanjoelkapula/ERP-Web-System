<?php echo view('_general/header'); ?>

<div class="layout-wrapper layout-2">
<div class="layout-inner">    
    
<?php echo view('_general/navigation'); ?>
<?php 
$role_search = '';
if($s_role_id <> -1){
	$role_search = " and r.id = $s_role_id ";
}
$status_search = '';
if($s_status_id <> -1){
	$status_search = " and u.active = $s_status_id ";
}

?>
<div class="layout-container">

    <?php echo view('_general/navigation_top'); ?>

    <!-- Layout content -->
    <div class="layout-content">
            
        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y">

            <h4 class="font-weight-bold py-3 mb-4">
                <span class="text-muted font-weight-light">User /</span> Search

                <span class="float-right">
                    <a href="/team/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New User</a>
                </span>
            </h4>

        
            <br/>             

            <!-- Filters -->
			<form method="post" action="/team/search" autocomplete="off">
				<input type="hidden" name="filter" value="true" />
				<div class="ui-bordered px-4 pt-4 mb-4">
				<div class="form-row align-items-center">
					<div class="col-md mb-4">
					<label class="form-label">Role</label>
					<select class="custom-select" name="s_role_id">
						<option value="-1" <?php if($s_role_id == -1) echo 'selected'?>>All</option>
						<?php $sql = "select id as value,description from TBL_ROLE"; 
							gen_select_dropdown($db,$sql,$s_role_id);
						?>
					</select>
					</div>
					<div class="col-md mb-4">
					<label class="form-label">Status</label>
					<select class="custom-select" name="s_status_id">
						<option value="-1" <?php if($s_status_id == -1) echo 'selected'?>>All</option>
						<option value="1" <?php if($s_status_id == 1) echo 'selected'?>>Active</option>
						<option value="0" <?php if($s_status_id == 0) echo 'selected'?>>Suspended</option>
					</select>
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
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Latest activity</th>   
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                       <tbody>
                        <?php 
						$sql = "select u.*,r.description as role 
								from TBL_USER u
								inner join TBL_USER_ROLE ur on ur.user_id = u.id
								inner join TBL_ROLE r on r.id = ur.role_id
								where u.id > 0 $role_search $status_search";
						$query = $db->query($sql);
						foreach($query->getResult() as $row){
                        ?>
						<tr>
							<td><?php echo $row->email?></td>
							<td><?php echo $row->first_name?></td>
							<td><?php echo $row->last_name?></td>
							<td><?php echo date("Y-m-d\ H:i:s",$row->last_login)?></td>
							<td><?php echo $row->role?></td>
							<td><?php if($row->active == 1){echo 'Active';} else {echo 'Suspended';}?></td>
                            <td> 
                                <div class="btn-group dropdown-toggle-hide-arrow">  
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                    <a class="dropdown-item" href="/team/member/<?php echo $row->id?>">Details</a>
                                        <div class="dropdown-divider"></div>
                                        <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                                            <?php if ($row->id != $_user_id) {?>
                                                    <a class="dropdown-item" href="javascript:void(0)"<?php if($row->active == 0) echo 'style="display:none;"';?> onclick="suspend_user('<?php echo $row->id?>')">Suspend User</a>
                                            <?php } ?>
                                            <a class="dropdown-item" href="javascript:void(0)"<?php if($row->active == 1) echo 'style="display:none;"';?> onclick="activate_user('<?php echo $row->id?>')">Re-activate User</a>
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

function suspend_user(user_id){
    $.ajax({
		'url': '/team/member/ajax/suspend_user',
		'data': {
			user_id:user_id
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
            text: "User Suspended!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
             window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error suspending user!','error');
        }
	})
	.fail( function (code, status) {

	})
	.always( function (xhr, status) {

	});
}

function activate_user(user_id){
    $.ajax({
		'url': '/team/member/ajax/activate_user',
		'data': {
			user_id:user_id
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
            text: "User Re-activated!",
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
                window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error re-activating user!','error');
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