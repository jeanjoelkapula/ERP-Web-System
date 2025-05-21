<form method="post" id="form_contractor" action="<?php echo $url ?>" class="form-horizontal" autocomplete="off">
    <input type="hidden" name="form_create_contractor" value="<?php echo $form_create; ?>" />
    <input type="hidden" name="form_update_contractor" value="<?php echo $form_update; ?>" />
    <input type="hidden" name="contractor" value="<?php if (isset($id)) {echo $id;} ?>"/>
	<div class="panel-body">
		<div class="form-group">
			<label class="col form-label">Contractor Name</label>
			<div class="col"><input type="text" class="form-control" name="name" required="" value="<?php if (isset($name)) {echo $name;} ?>"
            placeholder="Enter the contractor's name"></div>
		</div>
		<div class="form-group">
			<label class="col form-label">Contact Number</label>
			<div class="col"><input type="number" class="form-control" name="number"  value="<?php if (isset($contact_number)) {echo $contact_number;} ?>" 
            placeholder="Enter a contact number"></div>
		</div>
		<div class="form-group">
			<label class="col form-label">Email</label>
			<div class="col"><input type="email" class="form-control" name="email" required="" value="<?php if (isset($email)) {echo $email;} ?>"
            placeholder="Enter an email address"></div>
		</div>

        <?php
            if ($action_type == 'update') {

        ?>
            <div class="form-group">
                <label class="col form-label">In Business</label>
                    <div class="col">
                        <label class="switcher">

                        <?php if ($inbusiness == 1) { ?>
                            <input type="checkbox" checked='checked' name='inbusiness' class="switcher-input">

                        <?php 
                            }
                            else {
                        
                        ?>
                            <input type="checkbox" name='inbusiness' class="switcher-input">
                        <?php 
                            }
                        ?>
                        <span class="switcher-indicator">
                        <span class="switcher-yes"></span>
                        <span class="switcher-no"></span>
                        </span>
                        <span class="switcher-label"></span>
                    </div>
                </label>
            </div>
        <?php
            }
        ?>
        
		<div class="hr-line-dashed"></div>
		<br/>  
		<button type="button" id="btn-add-contractor" class="btn btn-primary"><i class="fas fa-check"></i> 
            <?php
                if ($action_type == 'create') {

            ?>
                    Create Contractor
            <?php
                }
                else {
            ?>
                    Update       
            <?php
                }
            ?>
        </button>
	</div>
</form>