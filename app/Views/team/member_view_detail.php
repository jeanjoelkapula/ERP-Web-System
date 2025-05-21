<div class="card-body">
                    

    <form method="post" id="frm_user_details" action="/team/member" class="form-horizontal" autocomplete="off">
        <input type="hidden" name="form_update" value="true" />
        <input type="hidden" name="entity_id" value="<?php echo $entity_id?>" />

        <div class="row">
            <div class="col">

                <div class="form-group">
                    <label class="col form-label">First Name</label>
                    <div class="col"><input type="text" class="form-control" name="first_name" required="" value="<?php echo $data->first_name?>"></div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Last Name</label>
                    <div class="col"><input type="text" class="form-control" name="last_name" required="" value="<?php echo $data->last_name?>"></div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Email</label>
                    <div class="col"><input type="email" class="form-control" name="email" value="<?php echo $data->email?>"> <span class="help-block m-b-none"><small><i>Only unique email addresses are allowed.</i></small></span></div>
                </div>

                <div class = "form-group">
                    <label class="col form-label">Role</label>
                    <div class = "col">     
                    <input type="hidden" name="role_id" value ="<?php echo $data->role_id?>"/>                                   
                        <select <?php if(!$ionAuth->isAdmin($_user_id) && !$ionAuth->inGroup('electrical_manager') || $ionAuth->isAdmin($data->id) && !$ionAuth->isAdmin($_user_id)) echo 'disabled' ?> <?php if($_user_id == $entity_id) echo 'disabled';?> class="custom-select btn-block" name="role_id">
                            <?php 
                            if($ionAuth->isAdmin($_user_id)){
                                $sql = "select id as value, description from TBL_ROLE";
                            } else {
                                $sql = "select id as value, description from TBL_ROLE where name <> 'admin'";
                            }
                            gen_select_dropdown($db,$sql,$data->role_id);
                            ?>
						</select>
                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Password</label>
                    <div class="col"><input type="password" class="form-control" placeholder="<?php echo $pwplaceholder; ?>" name="password" autocomplete="off" value=""> <span class="help-block m-b-none"></div>
                </div>
                <div class="form-group">
                    <label class="col form-label">Confirm Password</label>
                    <div class="col"><input type="password" class="form-control" placeholder="<?php echo $pwplaceholder; ?>" name="password_confirm" autocomplete="off" value=""> <span class="help-block m-b-none"></div>
                </div>

                <div class="form-group">
                    <label class="col form-label">Contact Number</label>
                    <div class="col"><input type="tel" class="form-control" name="phone" value="<?php echo $data->phone ?>"></div>
                </div>

                <div class="hr-line-dashed"></div>

                <br>
                <!-- only allow admin users to see the suspend user option -->
                <?php if(($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')) && ($_user_id != $entity_id)){ ?>
                <div class="row">
                    <div class="col">
                        <label class="custom-control custom-checkbox px-2 m-0">
                            <input type="checkbox" class="custom-control-input" <?php if($data->active == 0) echo 'checked ';?>  name="active">
                            <span class="custom-control-label" style="font-weight: 500; font-size: .83125rem;"> &nbsp; &nbsp; Suspend User</span>
                          </label>
                    </div>
                </div>    
                <br>
                <?php } ?>

                <div class="hr-line-dashed"></div>



            </div>    
        
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button class="btn btn-primary" type="button" id="btn-submit-user"> <i class="fas fa-check"></i> Update Details</button>

            </div>
        </div>



    </form>
</div>
