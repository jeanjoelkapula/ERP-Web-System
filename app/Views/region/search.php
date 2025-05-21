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
                <span class="text-muted font-weight-light">Region /</span> Search
                <?php 
                    if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                ?>
                    <span class="float-right">
                        <a href="/region/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Region </a>
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
                        <th>Manager</th>
                        <th>Division</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>                        
                                <?php foreach ($object->getResult('array') as $row): { ?> 
                                <tr>
                                    <td>
                                    
                                    <?php echo $row['REGION_NO']; ?>   
                                    
                                    

                                    <form method="post" action="/region/update/<?php echo $row['REGION_NO']; ?>">
                                    <input type="hidden" name="form_update_status" value="true" /> 
                                    <input type="hidden" name="region_id_var" value=<?php echo $row['REGION_NO']; ?>>
                                    <input type="hidden" name="region_name_var" value=<?php echo $row['REGION_NAME']; ?>>
                                    <input type="hidden" name="region_manager_var" value=<?php echo $row['REGION_MANAGER']; ?>>
                                    <input type="hidden" name="division_name_var" value=<?php echo $row['DIVISION_NAME']?>>
                                    <input type="hidden" name="region_email_var" value=<?php echo $row['EMAIL']; ?>>
                                    <input type="hidden" name="region_number_var" value=<?php echo $row['CONTACT_NUMBER']?>>
                                    
                                    
                                    </td>

                                    <td><?php echo $row['REGION_NAME'];?></td>
                                    <td><?php echo $row['REGION_MANAGER'];?></td>
                                    <td><?php echo $row['DIVISION_NAME']?></td>      
                                    <td><?php echo $row['EMAIL']?></td>     
                                    <td><?php echo $row['CONTACT_NUMBER']?></td>        
                                                                    
                                    <td>
                                    <?php 
                                        if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                                    ?>
                                        <button type="submit" class="btn btn-outline-secondary">Edit</button>
                                    <?php
                                        }
                                    ?>
                                    </td> 

                                    </form>

                                                                                                                                                                                                     
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