<form id="quote_category_form" method="post" action="<?php echo $url ?>" class="form-horizontal" autocomplete="off">
    <input type="hidden" name="form_create_quote_category" value="<?php echo $form_create; ?>" />
    <input type="hidden" name="form_update_quote_category" value="<?php echo $form_update; ?>" />
    <input type="hidden" name="category_id" value="<?php if (isset($category_id)) {echo $category_id;} ?>"/>
    <div class="panel-body">
                        
        <div class="form-group">
            <label class="col form-label">Quote category name</label>
            <div class="col"><input type="text" class="form-control" name="category_name" required="" value="<?php if (isset($category_name)) {echo $category_name;} ?>"></div>
        </div>

        <div class="hr-line-dashed"></div>
        
		<br/>  
		<button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> 
            <?php
                if ($action_type == 'create') {

            ?>
                    Create
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