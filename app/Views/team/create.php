<?php echo view('_general/header'); ?>
<div class="layout-wrapper layout-2">
<div class="layout-inner">
<?php echo view('_general/navigation'); ?>
<div class="layout-container">
<?php echo view('_general/navigation_top'); ?>
<!-- css style to turn text color of validation messages red -->
<style>
.error {
    color: red;

}
</style>
<!-- Layout content -->
<div class="layout-content">
	<!-- Content -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-4">
			<span class="text-muted font-weight-light">User /</span> Create
		</h4>
		<div class="card mb-4">
			<h6 class="card-header">User Detail</h6>
			<div class="card-body">
				<form method="post" id="frm_user_details" action="/team/create" class="form-horizontal" autocomplete="off">
					<input type="hidden" name="form_create" value="true" />
					<div class="panel-body">
						<div class="form-group">
							<label class="col form-label">First name</label>
							<div class="col"><input type="text" class="form-control" name="firstname" required="" value=""></div>
						</div>
						<div class="form-group">
							<label class="col form-label">Last name</label>
							<div class="col"><input type="text" class="form-control" name="lastname" required="" value=""></div>
						</div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
							<label class="col form-label">Email</label>
							<div class="col"><input type="email" class="form-control" name="email" id="email" value=""><small><i class="ml-1">Only unique email addresses are allowed.</i></small>  </div>
						</div>
						<div class = "form-group">
							<label class="col form-label">Role</label>
							<div class = "col">
								<select class="custom-select btn-block" name="role_id">
                                    <?php 
                                    if($ionAuth->isAdmin($_user_id)){
                                        $sql = "select id as value, description from TBL_ROLE";
                                    } else {
                                        $sql = "select id as value, description from TBL_ROLE where name <> 'admin'";
                                    }
                                    gen_select_dropdown($db,$sql,3);
                                    ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col form-label">Password</label>
							<div class="col"><input type="password" class="form-control" name="password" id="password" value=""><small><i>Please set a temporary password.</i></small></div>
						</div>
                        <div class="form-group">
                            <label class="col form-label">Confirm Password</label>
                            <div class="col"><input type="password" class="form-control" name="password_confirm" value=""></div>
                        </div>
						<div class="form-group">
							<label class="col form-label">Contact Number</label>
							<div class="col"><input type="text" class="form-control" name="phone" value=""></div>
						</div>
						<div class="hr-line-dashed"></div>
						<br/>  
						<button type="button" id="btn-submit" class="btn btn-primary"><i class="fas fa-check"></i> Create User</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Layout content -->
</div>
<?php echo view('_general/footer_javascript'); ?>

<!-- incase serversive email validation was triggered -->
<?php if ($error_code == 1){?>
    <script>
        Swal.fire("Email already exists!", "A duplicate email already exists, please check the details and use the correct email!", "error");
    </script>
<?php } ?>

<script>
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
       
       // validation for user details
        $( "#btn-submit" ).click(function() {
            var validator = $("#frm_user_details").validate({
                rules: {  
                    email: {
                        required: true,
                        email: true
                    },
                    firstname: {
                        required: true
                    },
                    lastname:{
                        required: true
                    },
                    phone:{
                        required: true
                    },
                    password:{
                        required:true
                    },
                    password_confirm:{
                        required: true,
                        equalTo: $("#password")
                    }
                },
                messages: {
                    email: "Please enter a valid Email!",
                    firstname: "Please enter a first name!",
                    lastname: "Please enter a last name !",
                    phone: "Please enter a contact number!",
                    password: "Please enter a password!",
                    password_confirm: "Passwords must match!"

                }      
            });
            if (validator.form()) {
                var u_email = $("#email").val()
                //email check - if not used - form submitted
                $.post("/team/create/check_email_in_use/", {email : u_email},
                function(result){
                    if (result == 0) {
                        $( "#frm_user_details" ).submit();
                    } else {
                        
                        Swal.fire("Email already exists!", "A duplicate email already exists, please check the details and use the correct email!", "error");
                    }
                });
            }
        });
	   
	});


    
</script>
<?php echo view('_general/footer');