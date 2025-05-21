<div class="card-body">

    <div class="row">
            <div class="col-4">
                <h4>VOC Details </h4>
                <br/>
                <div class="form-group">
                    <label class="col form-label">VOC ID</label>
                    <div class="col"><input type="text" readonly class="form-control" id="voc_id" required="" value="<?php echo $data->VOC_ID?>"></div>
                </div>
                <div class="form-group">
                    <label class="col form-label">Date Created</label>
                    <div class="col"><input type="text" readonly class="form-control" id="voc_created_dtm" required="" value="<?php echo $data->CREATED_DTM?>"></div>
                </div>
                <div class="form-group">
                    <label class="col form-label">Status</label>
                    <div class="col"><input type="text" readonly class="form-control" id="voc_created_dtm" required="" value="<?php echo $data->VOC_STATUS?>"></div>
                </div>

                <hr>

            </div>
            <div class="col-6">

                <h4> VOC Stock </h4>
                <br>

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
                        <tbody  id = "tbl_voc_stock_body">
                            <?php
                                $sql = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY;";
                                $result = $db->query($sql);
                                foreach ($result->getResult('array') as $row) { ?>
                                    <tr>
                                        <thead class="thead-light">
                                            <tr  id="stock-voc-category_<?php echo $row['ID']?>">
                                                <th colspan="5"><strong><?php echo $row['NAME']?></strong></th>
                                            </tr>
                                        </thead>
                                    </tr>
                                    
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        
        </div>


    </div>
</div>