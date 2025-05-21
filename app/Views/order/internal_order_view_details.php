<div class="card-body">
    <div class="row">
        <div class="col-5">

            <?php 
                $sql = "SELECT * FROM TBL_ORDER_INTERNAL WHERE ORDER_NO = '".$this->data['entity_id']."';";
                $query = $db->query($sql)->getResultArray();

                $source_hub = $query[0]['SOURCE_HUB_ID'];
                $destination_hub = $query[0]['DESTINATION_HUB_ID'];
            ?>
            <h4> Source Hub </h4>
            <br>
            <select class="custom-select btn-block select2" name="s_hub_id" id="s_hub_id" disabled>
                <?php 
                $sql = "select HUB_ID as value, HUB_NAME as description from TBL_HUB";
                gen_select_dropdown($db,$sql,$source_hub);
                ?>
            </select>

        </div>
        <div class="col-5">
        <h4> Destination Hub </h4>
            <br>
            <select class="custom-select btn-block select2" name="d_hub_id" id="d_hub_id" disabled>
                <?php 
                $sql = "select HUB_ID as value, HUB_NAME as description from TBL_HUB";
                gen_select_dropdown($db,$sql,$destination_hub);
                ?>
            </select>

        </div>
    </div>
    <br/>
    <br/>
    
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
                    </tr>
                </thead>
                <tbody  id = "tbl_stock_body">
                    <?php
                        $sql = "SELECT S.*, M.METRIC_DESCRIPTION, IOS.QUANTITY FROM TBL_ORDER_INTERNAL OI
                            INNER JOIN TBL_INTERNAL_ORDER_STOCK IOS ON IOS.ORDER_NO = OI.ORDER_NO
                            INNER JOIN TBL_STOCK S ON S.EBQ_CODE = IOS.EBQ_CODE
                            INNER JOIN TBL_METRIC M ON S.METRIC_ID = M.METRIC_ID
                            WHERE OI.ORDER_NO = '".$this->data['entity_id']."';";
                        $query = $db->query($sql);

                        foreach ($query->getResult('array') as $row) { 
                    ?>
                        <tr>
                            <td><?php echo $row['EBQ_CODE']; ?></td>
                            <td><?php echo $row['DESCRIPTION']; ?></td>
                            <td><?php echo $row['QUANTITY']; ?></td>
                            <td><?php echo $row['METRIC_DESCRIPTION']; ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
            </table>
        </div>
    </div>

</div>
