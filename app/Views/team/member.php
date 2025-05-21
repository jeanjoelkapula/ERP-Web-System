<?php echo view('_general/header'); ?>

<?php 
$sql = "select u.*, r.id as role_id, r.description as role_description
        from TBL_USER u 
        inner join TBL_USER_ROLE ur on ur.user_id = u.id
        inner join TBL_ROLE r on ur.role_id = r.id 
        where u.id = ".$this->data['entity_id'];
$query = $db->query($sql);
$num_rows = count($query->getResult());

$data = $query->getRow();
if(!$data){  
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    exit();
}
if(empty($data->password)){
  $pwplaceholder = "Password not set!";
} else {
  $pwplaceholder = "Password set, leave blank for no change";
}
?>
<!-- css style to turn text color of validation messages red -->
<style>
.error {
    color: red;

}
</style>
<div class="layout-wrapper layout-2">
<div class="layout-inner">    
    
<?php echo view('_general/navigation'); ?>

<div class="layout-container">

    <?php echo view('_general/navigation_top'); ?>

    <!-- Layout content -->
    <div class="layout-content">

        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y" id="outer_wrapper">

            <div class="media align-items-center py-3 mb-3">
              <div class="media-body ml-4">
                <h4 class="font-weight-bold mb-0"><?php echo $data->first_name?> <?php echo $data->last_name?></h4>
                <div class="text-muted mb-2"><?php echo $data->email?></div>
              </div>
            </div>
            
            <div class="nav-tabs-top">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#tab-detail">Account Detail</a>
                </li>
                
              </ul>
              

              <div class="tab-content">
                  
                  
                <div class="tab-pane fade show active" id="tab-detail">
                    <?php require_once(APPPATH.'Views/team/member_view_detail.php')?>
                </div> 
                                  
                  
                

                
              </div>
            </div>

            
            
        </div>


    </div>
    <!-- Layout content -->

</div>


<?php echo view('_general/footer_javascript'); ?> 

<script src="/assets/vendor/libs/datatables/datatables.js"></script>
<?php if (isset($updated)){
		if($updated == 1){?>
		<script>
       		$.growl({ title: "Success", message: "User updated!" });
    	</script>
<?php }} ?>

<?php if (isset($changepassword)){
		if($changepassword == 1){?>
		<script>
       		Swal.fire("Alert! Password Change Required!","A password change is required to use the system! Please update your password","warning")
    	</script>
<?php }} ?>
<script>
	$(document).ready(function() {
       
        $( "#btn-submit-user" ).click(function() {
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
                    },
                    password_confirm:{
                        equalTo: '[name="password"]'
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
                let u_email = $("#email").val()
                //email check - if not used - form submitted
                $.post("/team/member/check_email_in_use/", {email : u_email},
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



    // validation rules for user details form
    
</script>

<?php echo view('_general/footer');