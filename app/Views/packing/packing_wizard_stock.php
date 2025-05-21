<div id="packing-bill-wizard-step-3" class="card animated fadeIn tab-pane step-content">
<h6 class="card-header">Packing Bill Stock</h6>
    <div class="card-body">
        <div class="row">
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
                                Quantity
                            </th>
                            <th class="py-3">
                                Unit
                            </th>
                            <!-- <th class="py-3">
                                Packed
                            </th> -->

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
	    </div>
    </div>
</div>
