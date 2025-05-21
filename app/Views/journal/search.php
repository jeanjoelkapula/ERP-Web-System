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
                <span class="text-muted font-weight-light">Journal /</span> Search

                <span class="float-right">
                    <a href="/journal/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New </a>
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
                        <th>ID</th>
                        <th>EBQ Code</th>
                        <th>Quantity</th>                        
                        <th>Hub</th> 
                        <th>Cost</th>
                        <th>Entry Date </th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                        <tbody>                                                     
                            <?php foreach($journals as $journal): { ?> 
                            <tr>
                                <td>
                                    <?php echo $journal['JOURNAL_ID']; ?>
                                </td>  
                                <td>
                                    <?php echo $journal['EBQ_CODE']; ?>
                                </td> 
                                <td>
                                    <?php echo $journal['QUANTITY']; ?>
                                </td> 
                                <td>
                                    <?php echo $journal['HUB_NAME']; ?>
                                </td>
                                <td>
                                    ZAR <?php echo number_format($journal['COST'],2); ?>
                                </td>
                                <td>
                                    <?php echo $journal['ENTRY_DATE']; ?>
                                </td>
                                <td>
                                    <?php echo $journal['FULL_NAME']; ?>
                                </td>
                                <td>
                                    <?php echo $journal['STATUS'];                        
                                    $approved = $journal['STATUS'] ==="APPROVED"; 
                                    $declined = $journal['STATUS'] ==="DECLINED";
                                     if(isset($journal['NOTES']) && $journal['NOTES'] != "" ){
                                        echo "<span class='fas fa-info-circle' data-toggle='popover' data-placement='top' style='margin-left:5px'data-content='".$journal['NOTES']."' title='' data-original-title='Extra Notes'></span>";
                                    }   ?>
                                </td>
                                <?php if(($ionAuth->isAdmin($ionAuth->user()->row()->id)) || ($ionAuth->inGroup('electrical_manager'))){?>    
                                        <td>
                                        <div class="btn-group dropdown-toggle-hide-arrow">  
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                <a class="dropdown-item  <?php if($approved || $declined) echo 'disabled';?>" href="<?php if($approved || $declined) echo 'javascript:void(0)';else echo "/journal/update/".$journal['JOURNAL_ID'];?>">Edit</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item <?php if($approved) echo 'disabled';?>" href="javascript:void(0)" onclick="<?php if(!$approved) echo "handleJournalStatus('/journal/update/".$journal['JOURNAL_ID']."','form_approve_journal')";?>">Approve</a>
                                                <a class="dropdown-item  <?php if($declined) echo 'disabled';?>" href="javascript:void(0)"onclick="<?php if(!$declined) echo "handleJournalStatus('/journal/update/".$journal['JOURNAL_ID']."','form_decline_journal')";?>">Decline</a>
                                            </div>
                                        </div>
                                        </td>
                                    <?php } else { ?>
                                    <td>
                                        <a href="<?php if($approved || $declined) echo 'javascript:void(0)';else echo "/journal/update/".$journal['JOURNAL_ID'];?>" class="btn btn-outline-secondary <?php if($approved || $declined) echo 'disabled';?>">Edit</a>
                                    </td>
                                    <?php } ?>                                                                                                                                        
                            </tr>  
                            <?php }endforeach; ?>    
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
async function handleJournalStatus(url,status){
    if(status === 'form_decline_journal'){
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
            $.post(url, {[status] : true, reason: declineReason },function(result){ location.reload(); });
        }
    }
    else{
        $.post(url, {[status] : true},function(result){ location.reload(); });
    }
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