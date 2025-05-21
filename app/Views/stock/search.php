<?php 
    echo view('_general/header'); 
    $ionAuth = new \IonAuth\Libraries\IonAuth();
?>

<div class="layout-wrapper layout-2">
<div class="layout-inner">    
    
<?php echo view('_general/navigation'); ?>


<div class="layout-container">

    <?php echo view('_general/navigation_top'); ?>

    <!-- Layout content -->
    <div class="layout-content">
            
        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y">

            <h4 class="font-weight-bold py-3 mb-4">
                <span class="text-muted font-weight-light">Stock /</span> Sheet
                <?php 
                    if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')) || ($ionAuth->inGroup('stock_controller')))){
                ?>
                    <span class="float-right">
                        <a href="/stock/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Stock </a>
                        &nbsp;<button onclick="download_stocklist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Stock Sheet</button>
                    </span>
                <?php
                    }
                ?>
            </h4>

        
            <br/>             
            <div class="card">
                <div class="card-datatable table-responsive">

                    <div id="datatable_data_loading" style="text-align:center; padding:150px;">
                        <i class="far fa-5x fa-spinner-third fa-spin"></i>
                        <h4 class="mt-5">loading...</h4>
                    </div>

                    <table id="datatable_data" class="table table-striped table-bordered font-sm" style="display:none">
                    <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Description</th>                        
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Purchase Cost</th>
                        <th>Average Cost</th>
                        <th>Value</th>
                        <th>Is active</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                       <tbody>
                       <?php foreach ($object->getResult('array') as $row): { ?> 
                                <tr>
                                    <td><?php echo $row['EBQ_CODE']; ?></td>                                                                    
                                    <td><?php echo $row['DESCRIPTION'];?></td>                                      
                                    <td><?php echo $row['METRIC_DESCRIPTION'];?></td>
                                    <td><?php if($row['QUANTITY'] == '') { echo "0";} else echo $row['QUANTITY'];?></td>
                                    <td><?php 
                                        if ($row['IS_BUILT'] != 1) {
                                            echo "R ".number_format($row['PURCHASE_COST'], 2, '.', ' ');
                                        }
                                        else {
                                            $sql = "SELECT ROUND(SUM(PURCHASE_COST), 2) AS PURCHASE_COST, ROUND(SUM(AVG_COST), 2) AS AVG_COST FROM TBL_STOCK S
                                            INNER JOIN 
                                                (SELECT EBQ_CODE_SUB FROM TBL_STOCK_COMBINATION 
                                                WHERE (EBQ_CODE_LG = '".$row['EBQ_CODE']."')) SI
                                            WHERE (S.EBQ_CODE = SI.EBQ_CODE_SUB);";

                                            $result = $db->query($sql);

                                            echo "R ".$result->getResult('array')[0]['PURCHASE_COST'];
                                        }

                                        ?>
                                    </td>
                                    <td><?php 
                                        if ($row['IS_BUILT'] != 1){
                                            echo "R ".number_format($row['AVG_COST'], 2, '.', ' ');
                                        }
                                        else {
                                            echo "R ".$result->getResult('array')[0]['AVG_COST'];
                                        }
                                    
                                    ?></td> 
                                    <td><?php 
                                        if ($row['IS_BUILT'] != 1){
                                            echo "R ".number_format($row['AVG_COST'] * $row['QUANTITY'], 2, '.', ' ');
                                        }
                                        else {
                                            echo "R ".number_format($result->getResult('array')[0]['AVG_COST'] * $row['QUANTITY'], 2, '.', ' ');
                                        }
                                    ?></td>   
                                    
                                    <td>
                                        <div class = "row">  
                                            <div class="col-sm-3 pb-4">                                
                                                <?php   
                                                    if ($row['IS_ACTIVE'] == 1) {
                                                        echo "  <label class='custom-control custom-checkbox'>
                                                                    <input type='checkbox' checked='checked' name='inbusiness' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'>
                                                                <span class='custom-control-label'></span>
                                                                </label>
                                                            ";
                                                    }
                                                    else {
                                                        echo "  <label class='custom-control custom-checkbox'>
                                                                    <input type='checkbox' name='inbusiness' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'> 
                                                                <span class='custom-control-label'></span>
                                                                </label>
                                                            ";
                                                    }
                                                    ?>    
                                            </div>
                                                                                                                                       
                                            
                                    <div>
                              



                                    <td>
                                        <form method="post" action="/stock/update/<?php echo $row['EBQ_CODE']; ?>">                                    
                                            <input type="hidden" name="form_update_stock" value="false" /> 
                                            <input type="hidden" name="ebq_code" value=<?php echo $row['EBQ_CODE']; ?> /> 
                                            <input type="hidden" name="description" value=<?php echo $row['DESCRIPTION']; ?>>                                    
                                            <input type="hidden" name="metric_description" value="<?php echo $row['METRIC_DESCRIPTION']; ?>">
                                            <input type="hidden" name="quantity" value=<?php echo $row['QUANTITY']; ?>>
                                            <input type="hidden" name="avg_cost" value=<?php echo $row['AVG_COST']; ?>>                                
                                            <input type="hidden" name="value" value=<?php echo number_format($row['AVG_COST'] * $row['QUANTITY'],2); ?>>
                                        <div class="col-sm-6 pb-4">
                                            <?php 
                                                if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')) || ($ionAuth->inGroup('stock_controller')))){
                                            ?>
                                                <button type="submit" class="btn btn-outline-secondary">Edit</button>

                                            <?php
                                                }
                                            ?>
                                        </div>                   
                                    </td> 
                                    </form>

                                    </td>                                                                                                                                                                        
                                </tr>  
                                <?php } endforeach; ?> 
                        </tbody>
                    </table>

                </div>
            </div>    
        </div>            
            
    </div>
    <!-- Layout content -->

</div>
    
<?php echo view('_general/footer_javascript'); ?> 


<script src="/assets/vendor/libs/datatables/datatables.js"></script>

<script>

function download_stocklist(){
    window.location.href="/stock/search/dl/stocklist";

    Ladda.bind('button[type=ladda]');

    setTimeout(function(){
        Ladda.stopAll();
    }, 3000);
}

$(document).ready(function() {
   
   var dtable = $('#datatable_data').DataTable({
        order: [],
        pageLength: 25,
        responsive: true,
        language: { search: "Filter Results" },
        dom : "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
              "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"             
    });      
    
    
    
    $('#datatable_data_loading').hide();
    $('#datatable_data').show(); 
       
   
});
</script>
<?php echo view('_general/footer');    