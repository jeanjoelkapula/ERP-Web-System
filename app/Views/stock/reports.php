<?php echo view('_general/header'); ?>

<!-- css style to turn text color of validation messages red -->
<style>
.error {
    color: red;

}

.autocomplete-items {
    max-height: 200px; 
    overflow-y: scroll;
}

.dataTables_filter {
display: none;
}

</style>

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
                <span class="text-muted font-weight-light">Stock /</span> Reports

                <span class="float-right">

                <div class="myDIV" style="display:none;">
                <button onclick="download_stock_wastage()" data-style="zoom-out" data-color="blue" 
                type="ladda"  class="btn btn-primary ladda-button">
                <i class="fas fa-cloud-download"></i>&nbsp; Download Report</button>
             
                </div>
                </span>
            </h4>

        
            <br/>                                   
            <form method="post" id="form_filter" action="/stock/reports" autocomplete="off">
                <input type="hidden" name="filter" value="true" />
            
				<div class="ui-bordered px-4 pt-4 mb-4">                
				<div class="form-row align-items-center">
					<div class="col-md mb-4">
					<div class="col"><label class="form-label">Type</label></div>
					<div class="col"><select class="custom-select" name="type_filter" id="type_filter">
                    
                        <option value="-1" <?php if(isset($type_filter) && $type_filter == -1) echo 'selected'?>>Stock Wastage</option>						
                        <option value="0" <?php if(isset($type_filter) && $type_filter == 0) echo 'selected'?>>Stock Report from Each Hub</option>
                        <option value="1" <?php if(isset($type_filter) && $type_filter == 1) echo 'selected'?>>Stock Usage Report</option>
                        <option value="2" <?php if(isset($type_filter) && $type_filter == 2) echo 'selected'?>>Stock Work In Progress</option>                        
					</select></div>
                    </div>    
                    
                    <div class="col-md mb-4 hub_names" id="hub_names" name="hub_names" style="display:none;">
                        <div class="col"><label class="form-label">Hub</label></div>
					        <div class="col">
                                <?php
                                    echo "<select class='custom-select' name='hub_select' id='hub_select'>";
                                
                                    if(isset($hub_select))
                                    {
                                        echo "
                                        <option 'selected'>$hub_select</option>";
                                    } 

                                        $sql = "SELECT HUB_NAME FROM TBL_HUB";
                                        $metricResult = $db->query($sql);
                                        foreach ($metricResult->getResult('array') as $row) : {
                                            echo "<option>{$row['HUB_NAME']}</option>";
                                        }
                                        endforeach;

                                     
                                    
                                    echo "</select>";
                                ?>                        
                        </div>    
                    </div> 





                    <div class="col-md mb-4" id = "date_from_block">
                    <label class="col form-label">Date From</label>
                    <div class="col">
                        <input type="text" class="form-control" id="date_from" name="date_from" 
                        <?php if(isset($date_from)) echo "value=".$date_from; ?>
                        required placeholder="eg. 2020/11/01">
                        
                    </div>                    
                </div>
                <div class="col-md mb-4" id = "date_to_block">
                    
                    <label class="col form-label">Date To</label>
                    <div class="col">
                        <input type="text" class="form-control" id="date_to" name="date_to" 
                        <?php if(isset($date_to)) echo "value=".$date_to; ?>
                        required placeholder="eg. 2020/12/01">
                        
                    </div>
                </div>
					<div class="col-md mb-4">
					<label class="form-label d-none d-md-block">&nbsp;</label>
					<div class="col"><button type="submit" class="btn btn-primary btn-block" id="btn-add-all">Search</button></div>
                    </div>                                       
                </div>                                
                </div>
                
			</form>
     
            <div class="card">
                <div class="card-datatable table-responsive">

                    <div id="datatable_data_loading" style="text-align:center; padding:150px;">
                        <i class="far fa-5x fa-spinner-third fa-spin"></i>
                        <h4 class="mt-5">loading...</h4>
                    </div>

                    <table id="datatable_data" class="table table-striped table-bordered font-sm" style="display:none">
                    <thead>
           
                    <?php if(isset($object_wastage)) { ?>
                        <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Unit</th>
                        <th>Quantity</th>   
                        <th>Percentage</th>    
                        <th>Total Wastage</th> 
                    </tr>
                    <?php } 
                    elseif(isset($object_stock_report)) { ?>
                        <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>      
                        <th>Quantity</th>  
                        <th>Total Value</th>                             
                    </tr>
                    <?php }
                    elseif(isset($object_usage)) { ?>
                        <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Quantity</th>   
                        <th>Average Cost</th>   
                        <th>Total Value</th>                             
                    </tr>
                    <?php }
                    elseif(isset($object_progress)) { ?>
                        <tr>
                        <th>Product Code</th>
                        <th>Requisition Date</th>
                        <th>Expected Completion</th>      
                        <th>Hub Name</th>                             
                    </tr>
                    <?php }?>

                    </thead>
                       <tbody>
                       <?php if(isset($object_wastage))
                       { 
                          
                           foreach ($object_wastage->getResult('array') as $row): { 
                            $totalQty = 0;
                            $wastage = 0;
                            $metricID = 0;
                            $metricDesc = '';
                            if ($row['I_EBQ_CODE'] != null) $ebq_code= $row['I_EBQ_CODE']; else $ebq_code= $row['R_EBQ_CODE'];
                               ?> 
                                <tr>
                                    <td><?php echo $ebq_code; ?></td>    
                                    <td><?php echo $row['DESCRIPTION']; ?></td>
                                    <td><?php
                                    
                                    // get unit
                                    $sqlMetric = "select METRIC_ID from TBL_STOCK WHERE EBQ_CODE = '".$ebq_code."';";
                                    $queryMetric = $db->query($sqlMetric);
                                    foreach ($queryMetric->getResult('array') as $thisrow): {                                       
                                        $thisrow['METRIC_ID'];
                                        $metricID = $thisrow['METRIC_ID'];
                                    } endforeach;

                                    // get metric desc
                                    $sqlDesc = "select METRIC_DESCRIPTION from TBL_METRIC WHERE METRIC_ID = $metricID;";
                                    $queryDesc = $db->query($sqlDesc);
                                    foreach ($queryDesc->getResult('array') as $thisrow): {                                       
                                        $thisrow['METRIC_DESCRIPTION'];
                                        $metricDesc = $thisrow['METRIC_DESCRIPTION'];
                                    } endforeach;

                                    echo $metricDesc;
                                    ?>
                                    </td>
                                    <td><?php echo $row['QUANTITY'];
                                    $totalQty = $row['QUANTITY']; ?></td>  
                                    <td><?php echo $row['WASTAGE']."%"; ?> </td> 
                                    <td>
                                    <?php $sql = "select WASTAGE from TBL_STOCK WHERE EBQ_CODE = '".$ebq_code."';";
                                    $query = $db->query($sql);
                                    foreach ($query->getResult('array') as $thisrow): {                                       
                                        $thisrow['WASTAGE'];
                                        $wastage = $thisrow['WASTAGE'];
                                    } endforeach;

                

                                    echo number_format(($wastage/100) * $totalQty, 2, ',', ' ');
                                    ?>
                                    </td>
                                    

                                </tr>  
                                <?php } endforeach; } 
                                
                                elseif (isset($object_stock_report))
                                { 
                                    $avgcost = 0;
                                    $qty = 0;
                                    foreach ($object_stock_report->getResult('array') as $row): 
                                    {                               
                                    ?> 
                                         <tr>
                                             <td><?php echo $row['EBQ_CODE']; ?></td>    
                                                
                                             <td><?php echo $row['DESCRIPTION']; ?></td>    
                                             <td><?php  $qty = $row['QUANTITY']; echo $row['QUANTITY']; ?></td>  
                                             <?php
                                             // get the average cost per item
                                             $sql = "select AVG_COST from TBL_STOCK WHERE EBQ_CODE = '".$row['EBQ_CODE']."';";
                                             $query = $db->query($sql);
                                             foreach ($query->getResult('array') as $thisrow): {                                       
                                                 $thisrow['AVG_COST'];
                                                 $avgcost = $thisrow['AVG_COST'];
                                             } endforeach;
                                             ?>  
                                             <td><?php echo "R ".number_format($qty * $avgcost, 2, '.', ' ');?></td>                                                                                                                                             
                                         </tr>  
                                    <?php 
                                    } endforeach; 
                                }

                                elseif(isset($object_usage))
                                { 
                                   
                                    foreach ($object_usage->getResult('array') as $row): { 
                                     $totalQty = 0;
                                     $avgcost = 0;
                                     if ($row['I_EBQ_CODE'] != null) $ebq_code= $row['I_EBQ_CODE']; else $ebq_code= $row['R_EBQ_CODE'];
                                        ?> 
                                         <tr>
                                             <td><?php echo $ebq_code ?></td>    
                                             <td><?php echo $row['DESCRIPTION']; ?></td>
                                             <td><?php echo $row['QUANTITY']; ?></td>   
                                             <td><?php  echo "R ".number_format($row['AVG_COST'], 2, '.', ' ');?></td>  
                                             <td><?php    
                                             echo "R ".number_format($row['TOTAL_VALUE'], 2, '.', ' ');
                                             ?>
                                             </td>
                                             
         
                                         </tr>  
                                <?php } endforeach; 
                                } 
                                elseif(isset($object_progress))
                                { 
                                   
                                    foreach ($object_progress->getResult('array') as $row): {                                      
                                        ?> 
                                         <tr>
                                             <td><?php echo $row['EBQ_CODE']; ?></td>    
                                             <td><?php echo $row['REQUISITION_DATE']; ?></td>   
                                             <td><?php echo $row['EXPECTED_COMPLETION']; ?></td>   
                                             <td><?php echo $row['HUB_NAME']; ?></td>   
                                             
         
                                         </tr>  
                                <?php } endforeach; 
                                }?> 
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



function download_stock_wastage(){


    var from = document.getElementById("date_from").value.toString();
    var to = document.getElementById("date_to").value.toString();

    var type = document.getElementById("type_filter").value.toString();


    if(type == -1)
    {
        var report = "Stock Wastage"; 
    }
    if (type == 0)
    {

        var sel = document.getElementById("hub_select");
        var text= sel.options[sel.selectedIndex].text;
        var report = "Stock Report from Each Hub" + " : " + text;      

        console.log(report);     
    }
    if (type == 1)
    {
        var report = "Stock Usage Report";     
    }
    if (type == 2)
    {
        var report = "Stock Work in Progress";       
    }

    console.log(encodeURI(report));

    // var from = "Hello";

    // alert(from + to);

    // var from = $('#date_from').val();
    // var to = $('#date_to').val();

    var myJSON1 = JSON.stringify(from);
    var myJSON2 = JSON.stringify(to);

    // document.getElementById('date_form').submit();

    // $.ajax({ 
    //     type: "POST",
    //     url: window.location.origin + "/stock/reports/dl/report",
    //     data: { from_value: from,
    //             to_value : to 
    //         },
    //     success: function(data) { 
    //         console.log(data);
    //         } 
    // }); 

    $.post(window.location.origin + "/stock/reports", { from_value: from,
                to_value : to 
            },
        function(returnedData){
            // console.log(returnedData);

            // window.location.href="/stock/reports/dl/report";
            // Ladda.bind('button[type=ladda]');

            // setTimeout(function(){
            //     Ladda.stopAll();
            // }, 3000);
            
    }).fail(function(){
        console.log("error");
    });

   window.location.href="/stock/reports/dl/report?report_type="+encodeURI(report)+"&from_value="+from+"&to_value="+to;

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

    // ensure that the download button only displays when there is more than one row in the table
    <?php if (isset($object_wastage) || isset($object_stock_report) || isset($object_usage) || isset($object_progress))
    {
        ?>
        myFunction();    
        <?php
    }
    ?>

    var y = document.getElementById("type_filter").selectedIndex;

    console.log(y);

    var z = document.getElementById("hub_names");

    if(y == 1)
    {
        z.style.display = "block";
        $('#date_from_block').hide();
        $('#date_to_block').hide();
    } else {
        z.style.display = "none";
        $('#date_from_block').show();
        $('#date_to_block').show();
    }

    $('#type_filter').on('change', function() {

        var x = document.getElementById("hub_names");
    
        if(this.value == 0)
        {
            x.style.display = "block";
            $('#date_from_block').hide();
            $('#date_to_block').hide();
        } else {
            x.style.display = "none";
            $('#date_from_block').show();
            $('#date_to_block').show();
        }
    });

    // ensure that the download button only displays when there is more than one row in the table
    // x = document.getElementById("datatable_data").rows.length;
    // console.log(x);
    // if(x > 1)
    // {                            
    //     myFunction();               
    // }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // ensure that there are no duplicates of select dropdowns
    var map = {};
    $('select option').each(function() {
        if (map[this.value]) {
            $(this).remove()
        }
        map[this.value] = true;
    });    

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // validation for form details
    $( "#btn-add-all" ).click(function(e) {
    
    var validator = $('#form_filter').validate({
        errorPlacement: function errorPlacement(error, element) {
        $(element).parents('.form-group').append(
            error.addClass('invalid-feedback small d-block')
        )
        },
        highlight: function(element) {
        $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
        $(element).removeClass('is-invalid');
        },
        rules: {
        
        },
        messages: {
            type_filter: "Please enter the date.",
            date_to: "Please enter the date.",
            date_from: "Please enter the date."
        }
    });
    if (validator.form()) {                                    
        $("#form_filter").submit();  
    }
    
});

function myFunction() {
    $(".myDIV").toggle();
}

});


</script>
<?php echo view('_general/footer');    