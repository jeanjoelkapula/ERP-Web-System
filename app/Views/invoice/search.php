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
                <span class="text-muted font-weight-light">Invoice /</span> Search

                <?php 
                    if(($ionAuth->inGroup('electrical_manager') || ($ionAuth->inGroup('admin')))){
                ?>
                    <span class="float-right">
                        <a href="/invoice/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; Generate Invoice </a>
                        &nbsp; | &nbsp;<button onclick="download_invoicelist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download Invoice List</button>

                    </span>
                <?php
                    }
                ?>
            </h4>
            <form method="post" action="/invoice/search" autocomplete="off">
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
                        <th>Pastel Order No</th>
                        <th>Creation Date</th>
                        <th>Discount</th>
                        <th>Amount</th>
                        <th>Is Paid</th>
                        <th> Actions </th>
                    </tr>
                    </thead>
                       <tbody>
                       <?php foreach ($result->getResult('array') as $row): { ?>
                                <tr>
                                    <td><?php echo $row['PASTEL_INVOICE_NO'];?></td>
                                    <td><?php echo $row['INVOICE_DATE_CREATED'];?></td>
                                    <td><?php echo $row['DISCOUNT_PERCENTAGE']."%";?></td>
                                    <td>ZAR <?php echo number_format($row['INVOICE_AMOUNT'],2);?></td>
                                    <td>
                                    <div class = "row">  
                                            <div class="col-sm-3 pb-4 ml-4">                                
                                                <?php   
                                                    if ($row['INVOICE_PAID'] == 1) {
                                                        echo "  <label class='custom-control custom-checkbox'>
                                                                    <input type='checkbox' checked='checked' name='paid' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'>
                                                                <span class='custom-control-label'></span>
                                                                </label>
                                                            ";
                                                    }
                                                    else {
                                                        echo "  <label class='custom-control custom-checkbox'>
                                                                    <input type='checkbox' name='paid' class='custom-control-input' onclick='(function(e){e.preventDefault();})(event)'> 
                                                                <span class='custom-control-label'></span>
                                                                </label>
                                                            ";
                                                    }
                                                    ?>    
                                            </div>                                    </td>
                                    <td>
                                    <?php if($_role_id == 1 && !$row['INVOICE_PAID']) {?>    
                                        <div class="btn-group dropdown-toggle-hide-arrow">  
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                                <a class="dropdown-item" href="/invoice/preview/<?php echo $row['INVOICE_ID']?>">Preview</a>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                    <a class="btn btn-outline-secondary" href="/invoice/preview/<?php echo $row['INVOICE_ID']?>">Preview</a>
                                    <?php } ?>
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

function download_invoicelist(){
    window.location.href="/invoice/search/dl/invoicelist?s_date_from=<?php echo $s_date_from?>&s_date_to=<?php echo $s_date_to?>";

    Ladda.bind('button[type=ladda]');

    setTimeout(function(){
        Ladda.stopAll();
    }, 3000);
}

async function handleInvoicePaid(url,status){
        $.post(url, {[status] : true},function(result){ location.reload(); });
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