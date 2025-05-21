<form id="form-hub-base" method="post" action="<?php echo $url ?>" class="form-horizontal" autocomplete="off">
    <input type="hidden" name="form_create_hub" value="<?php echo $form_create; ?>" />
    <input type="hidden" name="form_update_hub" value="<?php echo $form_update; ?>" />
    <input type="hidden" name="hub" value="<?php if (isset($hub_id)) {echo $hub_id;} ?>"/>
    <div class="panel-body">
                        
        <div class="form-group">
            <label class="col form-label">Hub Name</label>
            <div class="col"><input type="text" class="form-control" name="hub_name" required="" value="<?php if (isset($hub_name)) {echo $hub_name;} ?>"></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Location (GPS Coordinates)</label>
            <div class="col"><input type="text" class="form-control" name="hub_latitude" placeholder="Latitude: eg. S32.476824" 
            required="" value="<?php if (isset($hub_latitude)) {echo assignDirectionChar($hub_latitude, true);} ?>"></div>
      
            <div class="col" style="padding-top:0.5em"><input type="text" class="form-control" name="hub_longitude" placeholder="Longitude: eg. E24.061344"
            required="" value="<?php if (isset($hub_longitude)) {echo assignDirectionChar($hub_longitude, false);} ?>"></div>
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

        <div class="form-group">
            <label class="col form-label">Hub Description</label>
                <div class="col">
                    <textarea class="form-control" rows="3" name="hub_descr"><?php if (isset($hub_descr)) {echo $hub_descr;} ?></textarea>
                </div>
            </div>


        <div class="hr-line-dashed"></div>
        
		<br/>  
		<button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> 
            <?php
                if ($action_type == 'create') {

            ?>
                    Create Hub
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