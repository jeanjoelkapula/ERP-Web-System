<form id="form-area-base" method="post" action="<?php echo $url ?>" class="form-horizontal" autocomplete="off">
    <input type="hidden" name="form_create_area" value="<?php echo $form_create; ?>" />
    <input type="hidden" name="form_update_area" value="<?php echo $form_update; ?>" />
    <input type="hidden" name="area" value="<?php if (isset($area_no)) {echo $area_no;} ?>"/>
    <div class="panel-body">
                        
        <div class="form-group">
            <label class="col form-label">Area Name</label>
            <div class="col"><input type="text" class="form-control" name="area_name" required="" value="<?php if (isset($area_name)) {echo $area_name;} ?>"></div>
        </div>
        
        <div class="form-group">
            <label class="col form-label">Area Manager</label>
            <div class="col"><input type="text" class="form-control" name="area_manager" required="" value="<?php if (isset($area_manager)) {echo $area_manager;} ?>"></div>
        </div>

        <div class="form-group">
            <label class="col form-label">Contact number</label>
            <div class="col"><input type="text" class="form-control" name="contact_number" value="<?php if (isset($contact_number)) {echo $contact_number;} ?>"></div>
        </div>

        <div class="form-group">
            <label class="col form-label">Email Address</label>
            <div class="col"><input type="email" class="form-control" name="email_address" value="<?php if (isset($email_address)) {echo $email_address;} ?>"></div>
        </div>


        <div class = "form-group">
            <label class="col form-label">Region</label>
            <div class = "col">
                <select class="custom-select btn-block" name="region_no">
                    <?php 
                         $sql = " SELECT REGION_NO AS value, REGION_NAME AS description FROM TBL_REGION;";

                        if (isset($region_no)){
                            gen_select_dropdown($db, $sql, $region_no);
                        }
                        else {
                            gen_select_dropdown($db, $sql, 0);
                        }
                    
                    ?>             
                </select>
            </div>
        </div>

        <div class="hr-line-dashed"></div>
        
		<br/>  
		<button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> 
            <?php
                if ($action_type == 'create') {

            ?>
                    Create Area
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