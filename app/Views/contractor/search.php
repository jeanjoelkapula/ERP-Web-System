<?php echo view('_general/header');
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
                <span class="text-muted font-weight-light">Contractor /</span> Search

                <?php 
                    if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                ?>
                    <span class="float-right">
                        <a href="/contractor/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Contractor </a>
                        &nbsp; | &nbsp;<button onclick="download_contractors()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Contractor List</button>
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
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>In business</th>
                        <th>Actions</th>
                    </tr>
                    </thead>                    
                       <tbody>                        
                                <?php foreach ($object->getResult('array') as $row): { ?> 
                                <tr>
                                    <td><form method="post" action="/contractor/update/<?php echo $row['CONTRACTOR_ID']; ?>">
                                    <input type="hidden" name="form_update_status" value="true" /> 
                                    <input type="hidden" name="contractor" value=<?php echo $row['CONTRACTOR_ID']; ?>><?php echo $row['CONTRACTOR_ID']; ?>
                                    <input type="hidden" name="name" value="<?php echo $row['CONTRACTOR_NAME']; ?>">
                                    <input type="hidden" name="email" value=<?php echo $row['EMAIL']; ?>>
                                    <input type="hidden" name="contact_number" value=<?php echo $row['CONTACT_NUMBER']; ?>>
                                    <input type="hidden" name="inbusiness" value=<?php echo $row['IN_BUSINESS']; ?>></td>
                                    <td><?php echo $row['CONTRACTOR_NAME'];?></td>
                                    <td><?php echo $row['CONTACT_NUMBER'];?></td>
                                    <td><?php echo $row['EMAIL'];?></td>                                          
                                    <td>
                                        <div class = "row">  
                                            <div class="col-sm-3 pb-4">                                
                                                <?php   
                                                    if ($row['IN_BUSINESS'] == 1) {
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
                                                <div class="col-sm-6 pb-4">
                                                    <button type="submit" class="btn btn-outline-secondary">Edit</button>
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

function download_contractors(){
    window.location.href="/contractor/search/dl/contractorlist";

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