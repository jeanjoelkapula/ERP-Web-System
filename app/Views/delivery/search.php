<?php echo view('_general/header'); ?>
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
                <span class="text-muted font-weight-light">Delivery Notes /</span> Search

                <span class="float-right">
                    <a href="/delivery/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Delivery Note</a>
                    &nbsp; | &nbsp;<button onclick="download_deliverylist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Delivery Notes List</button>
                </span>
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
                            <th>Delivery ID</th>
                            <th>Delivery Date</th>                        
                            <th>Waybill Number</th>                        
                            <th>Notes</th>
                            <th>Packing Bill ID</th>
                            <th>Signed</th>
                            <th>Actions</th>                        
                        </tr>
                    </thead>
                       <tbody>
                       <?php if(isset($object)) { foreach ($object->getResult('array') as $row): { ?> 
                                <tr>
                                    <td><?php echo $row['DELIVERY_ID']; ?></td>                                                                    
                                    <td><?php echo $row['DELIVERY_DATE'];?></td>                                      
                                    <td><?php echo $row['DELIVERY_WAYBILL'];?></td>                                    
                                    <td><?php echo $row['NOTES'];?></td>
                                    <td><?php echo $row['PACKING_BILL_ID'];?></td>
                                    <td>
                                        <div class = "row">  
                                            <div class="col-sm-3 pb-4">                                
                                                <?php   
                                                    if ($row['IS_SIGNED_OFF'] == 1) {
                                                        echo "  <label class='custom-control custom-checkbox'>
                                                                    <input type='checkbox' checked='checked' name='signed' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'>
                                                                    <span class='custom-control-label'></span>
                                                                </label>
                                                            ";
                                                    }
                                                    else {
                                                        echo "  <label class='custom-control custom-checkbox'>
                                                                    <input type='checkbox' name='signed' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'> 
                                                                    <span class='custom-control-label'></span>
                                                                </label>
                                                            ";
                                                    }
                                                    ?>    
                                            </div>                                                                                                                                       
                                        <div>
                                    </td>   
                                    
                                    <td>
                                    <div class="btn-group dropdown-toggle-hide-arrow">  
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                        <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">                                            
                                            <a class="dropdown-item <?php if ($row['IS_SIGNED_OFF'] == 1)  echo "disabled";?> " <?php if ($row['IS_SIGNED_OFF'] == 1)  {?> href="javascript:void(0)" <?php } else {?> href="/delivery/update/<?php echo $row['DELIVERY_ID'];?>" <?php } ?>>
                                            
                                            <form method="post" action="/delivery/update/<?php echo $row['DELIVERY_ID']; ?>">                                    
                                            <input type="hidden" name="form_update_delivery" value="false" /> 
                                            <input type="hidden" name="DELIVERY_ID" value=<?php echo $row['DELIVERY_ID']; ?> /> 
                                            <input type="hidden" name="DELIVERY_DATE" value=<?php echo $row['DELIVERY_DATE']; ?>>                                    
                                            <input type="hidden" name="DELIVERY_WAYBILL" value="<?php echo $row['DELIVERY_WAYBILL']; ?>">
                                            <input type="hidden" name="PRICE" value=<?php echo number_format($row['PRICE'],2); ?>>
                                            <input type="hidden" name="NOTES" value=<?php echo $row['NOTES']; ?>>                                
                                            <input type="hidden" name="PACKING_BILL_ID" value=<?php echo $row['PACKING_BILL_ID']; ?>>        
                                            <input type="hidden" name="IS_SIGNED_OFF" value=<?php echo $row['IS_SIGNED_OFF']; ?>>        
                                            Edit                 
                                        </form>                                                                                                                                                                                                                        
                                            </a>
                                            <a class="dropdown-item" href="/delivery/preview/<?php echo $row['DELIVERY_ID']; ?>">                                                
                                            <form method="post" action="/delivery/preview/<?php echo $row['DELIVERY_ID']; ?>">                                    
                                            <input type="hidden" name="form_update_delivery" value="false" /> 
                                            <input type="hidden" name="DELIVERY_ID" value=<?php echo $row['DELIVERY_ID']; ?> /> 
                                            <input type="hidden" name="DELIVERY_DATE" value=<?php echo $row['DELIVERY_DATE']; ?>>                                    
                                            <input type="hidden" name="DELIVERY_WAYBILL" value="<?php echo $row['DELIVERY_WAYBILL']; ?>">
                                            <input type="hidden" name="PRICE" value=<?php echo number_format($row['PRICE'],2); ?>>
                                            <input type="hidden" name="NOTES" value=<?php echo $row['NOTES']; ?>>                                
                                            <input type="hidden" name="PACKING_BILL_ID" value=<?php echo $row['PACKING_BILL_ID']; ?>>        
                                            <input type="hidden" name="IS_SIGNED_OFF" value=<?php echo $row['IS_SIGNED_OFF']; ?>>        
                                            Preview                 
                                        </form>                                                                                                                        
                                        </a>
                                        <a class="dropdown-item <?php if ($row['IS_SIGNED_OFF'] == 1)  echo "disabled";?>" <?php if ($row['IS_SIGNED_OFF'] == 1)  {?> href="javascript:void(0)" <?php } else {?> href="/delivery/update/sign/<?php echo $row['DELIVERY_ID'];?>" <?php } ?>>
                                            Sign Delivery
                                        </a>
                                        </div>
                                    </div>                                                    
                                    </td>                                                                                                                                                                        
                                </tr>  
                                <?php } endforeach; } ?> 
                        </tbody>
                    </table>

                </div>
            </div>   
   </div>
   <!-- / Content -->
</div>
<!-- Layout content -->
</div>
<?php echo view('_general/footer_javascript'); ?> 
<script src="/assets/vendor/libs/datatables/datatables.js"></script>

<script>
function download_deliverylist(){
    window.location.href="/delivery/search/dl/deliverylist";

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