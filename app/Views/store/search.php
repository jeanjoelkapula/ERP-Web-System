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
                <span class="text-muted font-weight-light">Store /</span> Search
                <?php 
                    if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                ?>
                    <span class="float-right">
                        <a href="/store/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Store </a>
                        &nbsp; | &nbsp;<button onclick="download_storelist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Store List</button>
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
                      <th>Store ID</th>
                        <th>Name</th>
                        <th>Area</th>
                        <th>FF Code</th>
                        <th>Opening Date</th>
                        <th>Maintenance Date</th>
                        <th>Contact Number</th>
                        <th>Trading Size (sq m)</th>
                        <th>Branch Size (sq m)</th>
                        <th>Open</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                       <tbody>
                       <?php foreach ($object->getResult('array') as $row): { ?> 
                                <tr>
                                    <td><form method="post" action="/store/update/<?php echo $row['STORE_ID']; ?>">
                                    <?php echo $row['STORE_ID']; ?>
                                    <input type="hidden" name="form_update_store" value="false" /> 
                                    <input type="hidden" name="store_id" value=<?php echo $row['STORE_ID']; ?> /> 
                                    <input type="hidden" name="store_name" value=<?php echo $row['STORE_NAME']; ?>>
                                    <input type="hidden" name="store_type_id" value="<?php echo $row['STORE_TYPE_ID']; ?>">
                                    <input type="hidden" name="ff_code" value=<?php echo $row['FF_CODE']; ?>>
                                    <input type="hidden" name="opening_date" value=<?php echo $row['OPENING_DATE']; ?>>
                                    <input type="hidden" name="maintenance_month" value=<?php echo $row['MAINTENANCE_MONTH']; ?>>
                                    <input type="hidden" name="contact_number" value=<?php echo $row['CONTACT_NUMBER']; ?>>
                                    <input type="hidden" name="trading_size" value="<?php echo $row['TRADING_SIZE']; ?>">
                                    <input type="hidden" name="branch_size" value=<?php echo $row['BRANCH_SIZE']; ?>>
                                    <input type="hidden" name="area" value=<?php echo $row['AREA_ID']; ?>>
                                    <input type="hidden" name="is_open" value=<?php echo $row['IS_OPEN']; ?>>
                                    </td>
                                    <td><?php echo $row['STORE_NAME'];?></td>
                                    <td><?php echo $row['STORE_TYPE_DESCRIPTION'];?></td>
                                    <td><?php echo $row['FF_CODE'];?></td>       
                                    <td><?php echo $row['OPENING_DATE'];?></td>
                                    <td><?php echo $row['MAINTENANCE_MONTH'];?></td>
                                    <td><?php echo $row['CONTACT_NUMBER'];?></td> 
                                    <td><?php echo $row['TRADING_SIZE'];?></td>
                                    <td><?php echo $row['BRANCH_SIZE'];?></td>
                                    <td>
                                        <div class = "row">  
                                            <div class="col-sm-3 pb-4">                                
                                                <?php   
                                                    if ($row['IS_OPEN'] == 1) {
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
                                        <?php 
                                            if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                                        ?>
                                            <div class="btn-group dropdown-toggle-hide-arrow">  
                                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                                <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                    <a class="dropdown-item" href="/store/update/<?php echo $row['STORE_ID']; ?>">Edit</a>
                                                    <a class="dropdown-item" href="/store/documents?store=<?php echo $row['STORE_ID']; ?>">Documents</a>
                                                </div>
                                            </div>
                                        <?php
                                            }
                                        ?>
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

function download_storelist(){
    window.location.href="/store/search/dl/storelist";

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