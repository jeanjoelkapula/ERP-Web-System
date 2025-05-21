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
                <span class="text-muted font-weight-light">Goods Received Note /</span> Search

                <span class="float-right">
                    <a href="/grn/create" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp; New </a>
                    &nbsp; | &nbsp;<button onclick="download_grnlist()" data-style="zoom-out" data-color="blue" type="ladda"  class="btn btn-primary ladda-button"><i class="fas fa-cloud-download"></i>&nbsp; Download GRN List</button>
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
                        <th>Date Recieved</th>
                        <th>Hub Name</th>                        
                        <th>Actionee</th>
                        <th>Source</th>
                        <th>Actions</th>   

                    </tr>
                    </thead>
                        <tbody>                                                     
                            <?php for($i = 0; $i < sizeof($grn_id);$i++): { ?> 
                            <tr>
                                <td><?php echo $grn_id[$i]; ?></td>
                                <td><?php echo $grn_date[$i];?></td>
                                <td><?php echo $grn_hub_name[$i];?></td>
                                <td><?php echo $grn_user_name[$i];?></td> 
                                <td><?php echo $grn_source[$i];?></td>                                             
                                <td>
                                    <div class="btn-group dropdown-toggle-hide-arrow">  
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars"></i></button>
                                        <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: top, left; top: -4px; left: 0px;">
                                        <a class="dropdown-item" href="/grn/update/<?php echo $grn_id[$i];?>">Edit</a>
                                        </div>
                                    </div>              
                                </td>                                                                                                                                                             
                            </tr>  
                            <?php }
                            endfor; ?>    
                        </tbody>
                    </table>

                </div>
            </div>    
        </div>            
            
    </div>
    <!-- Layout content -->

   

</div>

<!-- Popup content -->
<?php echo view('grn/printable')?>
    
<?php echo view('_general/footer_javascript'); ?> 


<script src="/assets/vendor/libs/datatables/datatables.js"></script>

<script>

function download_grnlist(){
    window.location.href="/grn/search/dl/grnlist";

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

function openPopup(params, details){

    var spltParams = params.split("%");

    var matrix_index = 1;

    var ebqs = [];
    var metrics = [];
    var quantities = [];
    var costs = [];

    var i;
    for(i = 0; i<spltParams.length-1;i++){
        switch(matrix_index){
            case 1:{
                matrix_index = 2;
                ebqs.push(spltParams[i]);
            }break;
            case 2:{
                matrix_index = 3;
                metrics.push(spltParams[i]);
            }break;
            case 3:{
                matrix_index = 4;
                quantities.push(spltParams[i]);
            }break;
            case 4:{
                matrix_index = 1;
                costs.push(spltParams[i]);
            }break;
        }
    }

    var splitDetails = details.split("%");
    
    var dateLabel = document.getElementById("print_date");
    dateLabel.innerHTML = splitDetails[0];

    var idLabel = document.getElementById("print_grn_id");
    idLabel.innerHTML = splitDetails[1];

    var hubLabel = document.getElementById("print_hub_name");
    hubLabel.innerHTML = splitDetails[2];

    var actioneeLabel = document.getElementById("print_actionee");
    actioneeLabel.innerHTML = splitDetails[3];

    var tcostLabel = document.getElementById("print_Tcost");
    tcostLabel.innerHTML = splitDetails[4];

    var tableBody = document.getElementById("table_body");
    tableBody.innerHTML = "";
    var tableRows = [];

    var j;


    for(l = 0; l<ebqs.length; l++){
        tableRows.push(tableBody.insertRow());
    } 
     
    for(j = 0; j<ebqs.length; j++){       
        
        var tableCells = [tableRows[j].insertCell(), tableRows[j].insertCell(), tableRows[j].insertCell(), tableRows[j].insertCell()];
        var tableCellValues = [ebqs[j], metrics[j], quantities[j], costs[j]];
        var k;
        for(k = 0; k<tableCells.length;k++){
            tableCells[k].innerHTML = tableCellValues[k];
        }
    }

    var html = "<html>";    
    html+= document.getElementById("popup").innerHTML;
    html+="</html>";

    var printWin = window.open('','','left=0,top=0,width=568,height=800,toolbar=0,scrollbars=0,status=0');
    printWin.document.write(html);
    printWin.document.close();
    printWin.focus();
    
}

</script>

<?php echo view('_general/footer'); 