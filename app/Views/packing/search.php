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
                <span class="text-muted font-weight-light">Packing Bill /</span> Search

                <span class="float-right">
                    <a href="/packing/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New Packing Bill</a>
                    &nbsp; | &nbsp;<button onclick="download_packinglist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Packing Bill List</button>
                </span>
            </h4>
            <!-- Filters -->
            <form method="post" action="/packing/search" autocomplete="off">
                <input type="hidden" name="filter" value="true" />
                <div class="ui-bordered px-4 pt-4 mb-4">
                <div class="form-row align-items-center">
                    <div class="col-md mb-4">
                    <label class="form-label">Date From</label>
                        <div class="input-group date">
                            <input id="s_date_from" name="s_date_from" type="text" class="form-control input-date " value="<?php echo date('Y-m-d', strtotime($s_date_from))?>">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-md mb-4">
                    <label class="form-label">Date To</label>
                        <div class="input-group date">
                            <input id="s_date_to" name="s_date_to" type="text" class="form-control input-date " value="<?php echo date('Y-m-d', strtotime($s_date_to))?>">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>    
                    <div class="col-md col-xl-2 mb-4">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Search</button>
                    </div>
                </div>
                </div>
            </form>
            </br>
            <div class="card">
                <div class="card-datatable table-responsive">

                    <div id="datatable_data_loading" style="text-align:center; padding:150px;">
                        <i class="far fa-5x fa-spinner-third fa-spin"></i>
                        <h4 class="mt-5">loading...</h4>
                    </div>

                    <table id="datatable_data" class="table table-striped table-bordered font-sm" style="display:none">
                    <thead>
                    <tr>
                        <th>Packing Bill ID</th>
                        <th>Order No</th>
                        <th>Source Hub</th>
                        <th>Destination Hub</th>   
                        <th>Ship via</th>   
                        <th>Pack Date</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                        <th>Created date</th>
                        <th>Actions</th>

                    </tr>
                    </thead>
                       <tbody>
                       <?php foreach ($result->getResult('array') as $row): {
                              $approved = $row['STATUS'] ==="APPROVED"; 
                              $declined = $row['STATUS'] ==="DECLINED"; ?>
                                <tr>
                                    <td><?php echo $row['PACKING_BILL_ID'];?></td>
                                    <td><?php if ($row['ORDER_NO'] != null) echo $row['ORDER_NO']; else echo $row['INTERNAL_ORDER_NO']?></td>
                                    <td><?php echo $row['SOURCE_HUB'];?></td>
                                    <td><?php echo $row['DESTINATION_HUB_NAME'];?></td>
                                    <td><?php echo $row['SHIP_VIA'];?></td>
                                    <td><?php echo $row['PACK_DATE'];?></td>
                                    <td><?php echo $row['DELIVERY_DATE'];?></td>
                                    <td><?php echo $row['STATUS'];?></td>
                                    <td><?php echo $row['CREATED_DATE'];?></td>
                                    <td>
                                    <?php  if(($ionAuth->isAdmin($ionAuth->user()->row()->id)) || ($ionAuth->inGroup('electrical_manager'))){?>    
                                        <div class="btn-group dropdown-toggle-hide-arrow">  
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                <a class="dropdown-item  <?php if($approved || $declined) echo 'disabled';?>" href="<?php if($approved || $declined) echo 'javascript:void(0)';else echo "/packing/update/".$row['PACKING_BILL_ID'];?>">Edit</a>
                                                <a class="dropdown-item" href="<?php echo "/packing/preview/".$row['PACKING_BILL_ID'];?>">Preview</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item <?php if($approved) echo 'disabled';?>" href="javascript:void(0)" onclick="<?php if(!$approved) echo "handleStatus('/packing/update/".$row['PACKING_BILL_ID']."','form_approve_packing')";?>">Approve</a>
                                                <a class="dropdown-item  <?php if($declined) echo 'disabled';?>" href="javascript:void(0)"onclick="<?php if(!$declined) echo "handleStatus('/packing/update/".$row['PACKING_BILL_ID']."','form_decline_packing')";?>">Decline</a>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <a href="<?php if($approved || $declined) echo 'javascript:void(0)';else echo "/packing/preview/".$row['PACKING_BILL_ID'];?>" class="btn btn-outline-secondary">Preview</a>
                                    <?php } ?>
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

function download_packinglist(){
    window.location.href="/packing/search/dl/packinglist";

    Ladda.bind('button[type=ladda]');

    setTimeout(function(){
        Ladda.stopAll();
    }, 3000);
}

async function handleStatus(url,status){
    $.post(url, {[status] : true},function(result){
        result = JSON.parse(result);

        if (status == "form_approve_packing") {
            message = "Packing Bill Approved";
        }
        else {
            message = "Packing Bill Declined";
        }

        if(result=="ok"){
            Swal.fire({
            title: 'Success!',
            text: message,
            type: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok!'
            }).then((result) => {
                window.location.reload();
            })
        } else {
            Swal.fire('Error!','Error approving packing bill!','error');
        }
    });
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