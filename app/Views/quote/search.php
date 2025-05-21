<?php 
    echo view('_general/header'); 
    $ionAuth = new \IonAuth\Libraries\IonAuth();
?>

<div class="layout-wrapper layout-2">
<div class="layout-inner">    
    
<?php echo view('_general/navigation'); ?>
<?php
///  USE 1 JAN 2019 to include all data 
$min_date = date("Ymd", strtotime("2019-01-01"));

// Get the date of the last scheduled order as default max date
$sql = "SELECT CREATED_DATE FROM TBL_QUOTE ORDER BY 1 DESC LIMIT 1";
$query = $db->query($sql);
$max_date = date("Ymd");
foreach($query->getResult() as $row){
    $max_date = date("Ymd", strtotime($row->CREATED_DATE));
}

if ($s_date_from == -1 || $s_date_from == '') 
$s_date_from = $min_date;

if ($s_date_to == -1 || $s_date_to == '') 
$s_date_to = $max_date; 

$date_from = date("Ymd", strtotime($s_date_from));
$date_to = date("Ymd", strtotime($s_date_to));
?>

<div class="layout-container">

    <?php echo view('_general/navigation_top'); ?>

    <!-- Layout content -->
    <div class="layout-content">
            
        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y">

            <h4 class="font-weight-bold py-3 mb-4">
                <span class="text-muted font-weight-light">Quote /</span> Search

                <span class="float-right">
                    <a href="/quote/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; Generate Quote </a>
                    &nbsp; | &nbsp;<button onclick="download_quotelist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Quote List</button>
                </span>
            </h4>
            <!-- Filters -->
            <form method="post" action="/quote/search" autocomplete="off">
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
                            <th>Creation Date</th>
                            <th>Quote Type</th>
                            <th>Store</th>
                            <th>Contractor</th>
                            <th>Total</th>
                            <th>Approval Status</th>
                            <th>Is Ordered?</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($result->getResult('array') as $row): { 
                        $approved = $row['STATUS'] ==="APPROVED"; 
                        $declined = $row['STATUS'] ==="DECLINED";
                        $pki_fee_increased_total = $row['TOTAL'] * (1+($row['PKI_PERCENTAGE']/100))?> 
                                <tr>
                                    <td><?php echo $row['QUOTE_ID'];?></td>
                                    <td><?php echo $row['CREATED_DATE'];?></td>
                                    <td><?php echo $row['TYPE_NAME'];?></td>
                                    <td><?php echo $row['STORE_ID']." - ".$row['STORE_NAME'];?></td>
                                    <td><?php echo $row['CONTRACTOR_NAME'];?></td>
                                    <td>ZAR <?php echo number_format($pki_fee_increased_total ,2 );
                                    ?></td>
                                    <td><?php 
                                        echo $row['STATUS'];
                                        if($row['STATUS'] == "DECLINED"){
                                            echo "<span class='fas fa-info-circle' data-toggle='popover' data-placement='top' style='margin-left:5px'data-content='".$row['DECLINE_REASON']."' title='' data-original-title='Declined Reason'></span>";
                                        }
                                        else{
                                            if(isset($row['NOTE']) && $row['NOTE'] != ""){
                                                echo "<span class='fas fa-info-circle' data-toggle='popover' data-placement='top' style='margin-left:5px'data-content='".$row['NOTE']."' title='' data-original-title='Extra Notes'></span>";
                                            }
                                        }  
                                    ?></td>
                                      <td>
                                        <div class = "row">  
                                            <div class="col-sm-3 pb-4">                                
                                                <?php   
                                                    if ($row['IS_ORDERED'] == 1) {
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
                                    <?php if(($ionAuth->isAdmin($ionAuth->user()->row()->id)) || ($ionAuth->inGroup('electrical_manager')) || ($ionAuth->inGroup('electrical_administrator'))){
                                    ?>    
                                        <td>
                                        <div class="btn-group dropdown-toggle-hide-arrow">  
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                <a class="dropdown-item  <?php if($approved || $declined) echo 'disabled';?>" href="<?php if($approved || $declined) echo 'javascript:void(0)';else echo "/quote/update/".$row['QUOTE_ID'];?>">Edit</a>
                                                <a class="dropdown-item" href="<?php echo "/quote/preview/".$row['QUOTE_ID'];?>">Preview</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item <?php if($approved) echo 'disabled';?>" href="javascript:void(0)" onclick="<?php if(!$approved) echo "handleQuoteStatus('/quote/update/".$row['QUOTE_ID']."','form_approve_quote')";?>">Approve</a>
                                                <a class="dropdown-item  <?php if($declined) echo 'disabled';?>" href="javascript:void(0)"onclick="<?php if(!$declined) echo "handleQuoteStatus('/quote/update/".$row['QUOTE_ID']."','form_decline_quote')";?>">Decline</a>
                                            </div>
                                        </div>
                                        </td>
                                    <?php } else { ?>
                                    <td>
                                        <a href="<?php if($approved || $declined) echo 'javascript:void(0)';else echo "/quote/update/".$row['QUOTE_ID'];?>" class="btn btn-outline-secondary <?php if($approved || $declined) echo 'disabled';?>">Edit</a>
                                    </td>
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

function download_quotelist(){
    window.location.href="/quote/search/dl/quotelist?s_date_from=<?php echo $s_date_from?>&s_date_to=<?php echo $s_date_to?>";

    Ladda.bind('button[type=ladda]');

    setTimeout(function(){
        Ladda.stopAll();
    }, 3000);
}


async function handleQuoteStatus(url,status){
    if(status === 'form_decline_quote'){
        const { dismiss: dismissed , value: declineReason } = await Swal.fire({
            title: 'Provide a reason for declining',
            input:"text",
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            showCancelButton: true,
            showCloseButton: true,
            inputLabel: ''
        });
        if(dismissed !== 'cancel' && dismissed !== 'close'){
            $.post(url, {[status] : true, reason: declineReason },function(result){ 
                result = JSON.parse(result);
                if(result=="ok"){
                    Swal.fire({
                    title: 'Success!',
                    text: "Quote Declined",
                    type: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok!'
                    }).then((result) => {
                        window.location.reload();
                    })
                } else {
                    Swal.fire('Error!','Error approving quote!','error');
                }
            });
        }
    }
    else{
        $.post(url, {[status] : true},function(result){ 
            result = JSON.parse(result);
            if(result=="ok"){
                Swal.fire({
                title: 'Success!',
                text: "Quote Approved",
                type: 'success',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok!'
                }).then((result) => {
                    window.location.reload();
                })
            } else {
                Swal.fire('Error!','Error approving quote!','error');
            }
        });
    }
}

$(document).ready(function() {

    // Hides popover when clicking away
    $('html').on('click', function(e) {
        if (typeof $(e.target).data('original-title') == 'undefined' && !$(e.target).parents().is('.popover')) {
            $('[data-original-title]').popover('hide');
        }
    });
   
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