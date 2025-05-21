<div class="card-body">
                    
        <div class="row">
            <div class="col-4">
            <?php if($data->ORDER_INTERNAL == 1){ ?>
                <?php 
                    $sql = "select sh.HUB_NAME as SOURCE_HUB, dh.HUB_NAME as DESTINATION_HUB
                            from TBL_ORDER_INTERNAL ot
                            inner join TBL_HUB sh on sh.HUB_ID = ot.SOURCE_HUB_ID
                            inner join TBL_HUB dh on dh.HUB_ID = ot.DESTINATION_HUB_ID
                            where ot.ORDER_NO = '".$this->data['entity_id']."'";
                    $query = $db->query($sql);
                    $s_hub = '';
                    $d_hub = '';
                    foreach($query->getResult() as $row){
                        $s_hub = $row->SOURCE_HUB;
                        $d_hub = $row->DESTINATION_HUB;
                    }
                ?>
                <h4> Internal Order Details</h4>
                <div class="form-group">
                    <label class="col form-label">Source Hub</label>
                    <div class="col"><input type="text" readonly class="form-control" id="contractor_name" required="" value="<?php echo $s_hub?>"></div>
                </div>
                <div class="form-group">
                    <label class="col form-label">Destination Hub</label>
                    <div class="col"><input type="text" readonly class="form-control" id="contractor_name" required="" value="<?php echo $d_hub?>"></div>
                </div>


            <?php } else { ?>
                <h4>Contractor Details </h4>
                <br/>
                <div class="form-group">
                    <label class="col form-label">Contractor</label>
                    <div class="col"><input type="text" readonly class="form-control" id="contractor_name" required="" value="<?php echo $data->CONTRACTOR_NAME?>"></div>
                </div>
                <div class="form-group">
                    <label class="col form-label">Contractor Email</label>
                    <div class="col"><input type="text" readonly class="form-control" id="contractor_email" required="" value="<?php echo $data->CONTRACTOR_EMAIL?>"></div>
                </div>
                <div class="form-group">
                    <label class="col form-label">Contractor Contact Number</label>
                    <div class="col"><input type="text" readonly class="form-control" id="contractor_contact_number" required="" value="<?php echo $data->CONTACT_NUMBER?>"></div>
                </div>
                <hr>
               

                <?php 
                    if (isset($data->TYPE_NAME)) {
                ?>
                        <h4>Quote Details </h4>
                        <br/>
                        <div class="form-group">
                            <label class="col form-label">Quote Type</label>
                            <div class="col"><input type="text" readonly class="form-control" id="quote_type" required="" value="<?php echo $data->TYPE_NAME?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="col form-label">Quote Total</label>
                            <div class="col"><input type="text" readonly class="form-control" id="quote_total" required="" value="<?php echo $data->TOTAL?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="col form-label">Quote Created Date</label>
                            <div class="col"><input type="text" readonly class="form-control" id="quote_created_dtm" required="" value="<?php echo $data->QUOTE_CREATED_DATE?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="col form-label">Quote Approved Date</label>
                            <div class="col"><input type="text" readonly class="form-control" id="quote_approved_dtm" required="" value="<?php echo $data->QUOTE_APPROVED_DTM?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="col form-label">Quote Status</label>
                            <div class="col"><input type="text" readonly class="form-control" id="quote_status" required="" value="<?php echo $data->QUOTE_STATUS?>"></div>
                        </div>
                <?php
                    }
                ?>
                
            <?php }?>

            </div>
            <div class="col-6">

                <h4> Order Details </h4>
                <br>
                <div class="form-group">
                    <label class="col form-label">Order Number</label>
                    <div class="col"><input type="text" disabled class="form-control" name="order_no" id="order_no"  value="<?php echo $this->data['entity_id']?>"/></div>    
                </div>
                <div class="form-group">
                    <label class="col form-label">Order Status</label>
                    <div class="col"><input type="text" disabled class="form-control" name="order_status" id="order_status"  value="<?php echo $data->STATUS?>"/></div>    
                </div>
                <div class="form-group">
                    <label class="col form-label">Order Notes</label>
                    <div class="col"><textarea type="text" disabled class="form-control" name="order_notes" id="order_notes"  value=""><?php echo $data->ORDER_NOTES ?></textarea><small><i>Any additional order notes.</i></small></div>
                </div>
                <?php if($ionAuth->isAdmin($_user_id) || $ionAuth->inGroup('electrical_manager')){ ?>
                    <div class="form-group pb-5">
                        <button class="btn btn-outline-success" <?php if($data->STATUS == 'APPROVED') echo 'style="display:none;"';?> onclick="approve_order('<?php echo $this->data['entity_id']?>')">Approve Order</button>
                        <button class="btn btn-outline-danger float-right" <?php if($data->STATUS == 'DECLINED') echo 'style="display:none;"';?> onclick="decline_order('<?php echo $this->data['entity_id']?>')">Decline Order</button>
                    </div>
                <?php } 
                
                    if(empty($data->VOC_ID)) {
                ?>
                        <div class="table-responsive mb-4">
                            <table class="table m-0 font-sm" id = "tbl_stock">
                                <thead>
                                    <tr>
                                        <th class="py-3">
                                            Code
                                        </th>
                                        <th class="py-3">
                                            Description
                                        </th>
                                        <th class="py-3">
                                            Price
                                        </th>
                                        <th class="py-3">
                                            Quantity
                                        </th>
                                        <th class="py-3">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody  id = "tbl_stock_body">
                                    <?php
                                        $sql = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY;";
                                        $result = $db->query($sql);
                                        foreach ($result->getResult('array') as $row) { ?>
                                            <tr>
                                                <thead class="thead-light">
                                                    <tr  id="stock-category_<?php echo $row['ID']?>">
                                                        <th colspan="5"><strong><?php echo $row['NAME']?></strong></th>
                                                    </tr>
                                                </thead>
                                            </tr>
                                            
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                <?php
                    }
                ?>
            </div>
        
        </div>


    </div>
<?php if(!empty($data->VOC_ID) && $data->VOC_STATUS == 'APPROVED'){?>
    <hr>
    <?php require_once(APPPATH.'Views/order/view_voc.php')?>
<?php } ?>

</div>
