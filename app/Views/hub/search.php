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
                <span class="text-muted font-weight-light">Hub /</span> Search
                <?php 
                    if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                ?>
                    <span class="float-right">
                        <a href="/hub/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Hub </a>
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
                      <th>ID</th> 
                      <th>Name</th>
                      <th>Location</th>
                      <th>Description</th>
                      <th>Region</th>
                      <th>Actions</th>
                    </tr>
                    </thead>
                       <tbody>
                       <?php foreach ($result->getResult('array') as $row): {?> 
                                <tr>
                                    <td><?php echo $row['HUB_ID'];?></td>
                                    <td><?php echo $row['HUB_NAME'];?></td>
                                    <td><?php echo assignDirectionChar($row['HUB_LATITUDE'],true).",".assignDirectionChar($row['HUB_LONGITUDE'],false);?></td>
                                    <td><?php echo $row['HUB_DESCR'];?></td>
                                    <td><?php echo $row['REGION_NAME'];?></td>
                                    <td>
                                        <form method="post" action="/hub/update/<?php echo $row['HUB_ID']; ?>">
                                            <input type="hidden" name="form_update_status" value="true" /> 
                                            <input type="hidden" name="hub_name" value="<?php echo  $row['HUB_NAME'];?>" />
                                            <input type="hidden" name="hub_location" value="<?php echo  assignDirectionChar($row['HUB_LATITUDE'],true).",".assignDirectionChar($row['HUB_LONGITUDE'],false);?>" />
                                            <input type="hidden" name="hub_descr" value="<?php echo  $row['HUB_DESCR'];?>" />
                                            <input type="hidden" name="region_no" value="<?php echo  $row['REGION_NO'];?>" />
                                            <?php 
                                                if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                                            ?>
                                                <button type="submit" class="btn btn-outline-secondary">Edit</button>
                                            <?php
                                                }
                                            ?>
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