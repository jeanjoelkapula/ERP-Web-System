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
					<span class="text-muted font-weight-light">Goods Received Note /</span> Create
				</h4>
				<div class="card mb-4">
					<h6 class="card-header">Note Details</h6>
					<div class="card-body">
						<?php
							$sql = "select TBL_STOCK.EBQ_CODE, TBL_STOCK.DESCRIPTION, TBL_METRIC.METRIC_DESCRIPTION from TBL_STOCK, TBL_METRIC where TBL_STOCK.METRIC_ID = TBL_METRIC.METRIC_ID"; 
							$mini_stock = $db->query($sql);                     
							?>
						<form method="post" action="/grn/create" class="form-horizontal" autocomplete="off" id="grn-form" onsubmit="return validateForm()" >
							<input type="hidden" name="form_create_grn" value="true" />                                
							<div class="panel-body">
								<!-- Field -->
								<div class="form-group">
									<label class="col form-label">Delivery Date <span class="text-danger">*</span></label>
									<div class="col"><input type="text" class="form-control" id="datepicker" name="grn_date" value="" required></div>
								</div>
								<!-- Field -->
								<div class = "form-group">
									<label class="col form-label">Hub</label>
									<div class = "col">
										<select class="custom-select btn-block" name = "hub_var">
										<?php                                     
											$sql = "select HUB_NAME from TBL_HUB;";
											$query = $db->query($sql);
											
											foreach ($query->getResult() as $row): {
											    
											    echo '<option>';                                             
											
											    echo $row->HUB_NAME.'</option>';
											
											} 
											endforeach;
											                
											?>   
										</select>
									</div>
								</div>

								<!-- Field -->
                                <div class = "col">
                                    <div class = "spacer">
                                        <!-- Tab Styling -->
                                        <style>
                                            .tab{
                                                overflow: hidden;
                                                border: 1px solid #dedede;
                                                background-color: white;
                                                border-radius: 5px 5px 0px 0px;
                                            }

                                            .tab-button{
                                                background-color: inherit;
                                                float: left;
                                                border: none;
                                                outline: none;
                                                cursor: pointer;
                                                padding: 14px 16px;
                                                transition: 0.3s;
                                            }

                                            .tab-button.active{
                                                background-color: #dedede;
                                                border: none;
                                                outline: none;
                                            }

                                            .tab-button:hover {
                                                background-color: #ededed;
                                                border: none;
                                                outline: none;
                                            }

                                            .tabcontent {
                                                display: none;
                                                padding: 6px 12px;
                                                border: 1px solid #dedede;
                                                border-top: none;
                                                border-radius: 0px 0px 5px 5px;
                                            }

                                            .spacer{
                                                margin-top: 16px;
                                                margin-bottom: 16px;
                                            }


                                        </style>
                                        <!-- Tab links -->
                                        
                                        <div class="tab">
                                        <button class="tab-button" type="button" onclick="openSearch(event, 'purchase')" id="defaultOpen" >Purchase Order</button>
                                        <button class="tab-button" type="button" onclick="openSearch(event, 'requisition')" id="defaultClose">Requisition</button>
                                        </div>

                                        <!-- Tab Content-->

                                        <div class = "tabcontent" id = "purchase">
                                            <div class = "row" id = "purchase-search-group">
                                                <div class="form-group">
                                                    <label class="col form-label">Purchase Order Search <span class="text-danger">*</span></label>
                                                    <div class="col">
                                                        <div class="autocomplete" style="width:500px;">
                                                            <input id="purchase-search" type="text" class="form-control" placeholder="Order No: 1 Date:2020-11-01 Vendor:Jacobs and Co (PTY) LTD">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" style= "margin-left: 15px; margin-top: 23px;">
                                                    <button id="btn-purchase-add" class="btn btn-primary" type="button"><i class="fas fa-plus"></i>&nbsp; Add Item 
                                                </div>
                                            </div>
                                            <!-- purchase info-->
                                            <br>                                               
                                            <div class="form-group" id = "purchase-info" style= "display: none">
                                                <label class="col" style = "color: #696969; line-height: 5px;">Purchase Order ID &nbsp;&rarr;&nbsp;<label id ="label-purchase-id" style = "color: #ababab"></label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Date Ordered &nbsp;&rarr;&nbsp;<label id ="label-purchase-date-o" style = "color: #ababab"></label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Date Required &nbsp;&rarr;&nbsp;<label id ="label-purchase-date-r" style = "color: #ababab"></label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Shipped Via &nbsp;&rarr;&nbsp;<label id ="label-purchase-via" style = "color: #ababab"></label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Vendor Name &nbsp;&rarr;&nbsp;<label id ="label-purchase-vname" style = "color: #ababab"></label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Vendor Address &nbsp;&rarr;&nbsp;<label id ="label-purchase-vaddress" style = "color: #ababab">&nbsp;</label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Vendor PO BOX &nbsp;&rarr;&nbsp;<label id ="label-purchase-pobox" style = "color: #ababab"></label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Vendor Zip Code &nbsp;&rarr;&nbsp;<label id ="label-purchase-zip" style = "color: #ababab"></label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Total Cost &nbsp;&rarr;&nbsp;<label id ="label-purchase-tcost" style = "color: #ababab"></label></label>  
                                                <button id="btn-change-purchase" class="btn btn-primary" style = "margin-left: 15px; margin-top: 23px;"type="button">&nbsp; Change                                             
                                            </div>
                                        </div>

                                        <div class = "tabcontent" id = "requisition">
                                            <div class = "row" id = "requisition-search-group" >
                                                <div class="form-group">
                                                    <label class="col form-label">Requisition Search <span class="text-danger">*</span></label>
                                                    <div class="col">
                                                        <div class="autocomplete" style="width:500px;">
                                                            <input id="requisition-search" type="text" class="form-control" placeholder="Requisition No: 9 EBQ: EBQ00103 Date:2020-11-01">
                                                        </div>
                                                        <button id="btn-requisition-add" class="btn btn-primary" type="button"><i class="fas fa-plus"></i>&nbsp; Add Item 
                                                
                                                    </div>
                                                </div>                                                
                                            </div>
                                            <!-- requisition info-->
                                            <br>                                               
                                            <div class="form-group" id = "requisition-info" style= "display: none">
                                                <label class="col" style = "color: #696969; line-height: 5px;">Requisition No <label id ="label-requisition-no" style = "color: #ababab">&nbsp;&rarr;&nbsp;</label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Requisition Date <label id ="label-requisition-date" style = "color: #ababab">&nbsp;&rarr;&nbsp;</label></label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Requisition Notes <label id ="label-requisition-notes" style = "color: #ababab">&nbsp;&rarr;&nbsp;</label> </label>
                                                <label class="col" style = "color: #696969; line-height: 5px;">Requisition Item <label id ="label-requisition-item" style = "color: #ababab">&nbsp;&rarr;&nbsp;</label> </label>
                                                <button id="btn-change-requisition" class="btn btn-primary" style = "margin-left: 15px; margin-top: 23px;"type="button">&nbsp; Change                                             
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Table -->
								<div class = "form-group" id ='purchase-table'>
									<label class="col form-label">Stock Items</label>
									<div class = "col">
										<table id = "tbl_stock" class="table table-striped font-sm">
                                            <thead>
                                                <tr>
                                                    <th>EBQ Code</th>
                                                    <th>Description</th>
                                                    <th>Unit</th>
                                                    <th>Quantity</th>
                                                    <th>Price per Unit</th>
                                                    <th>Total Price</th>
                                                    <th>Approved</th>
                                                    <th>Approval Note</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_stock_body">
                                          
                                            </tbody>
                                            <input type = "hidden" name = "grn-type" id = "grn-type" value = "">
                                            <input type = "hidden" name = "source-id" id = "source-id" value = "">
                                        </table>
									</div>
								</div>

                                <!-- Table -->
                                <div class = "form-group">
                                    <div id='requisition-table'>
                                        <label class="col form-label">Sub Items</label>
                                        <div class = "col">
                                            <table id = "tbl_req" class="table m-0 font-sm">
                                                <thead id = "tbl_stock_head">
                                                    <tr>
                                                        <th>Product Code</th>
                                                        <th>Description</th>
                                                        <th>Unit</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbl_req_body">
                                            
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    </div>
                                </div>
								<!--Submit -->
								<div class="hr-line-dashed"></div>
								<br/>
								<button type="button" class="btn btn-primary" id ="btn-grn-create"><i class="fas fa-check"></i>&nbsp;Create</button>                                                                    
							</div>
					</div>
					</form>
				</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Layout content -->
</div>
<?php echo view('_general/footer_javascript'); ?> 

<script>
    var id_set = false;
                                    
	$(document).ready(function() {   

        document.getElementById("defaultOpen").click();
        
        var purchaseOrders = [];   
        var requisitions = [];
        var purchaseStock = [];
        var requisitionStock = [];   
        var tbl_stock = document.querySelector('#tbl_stock_body');
        var tbl_req = document.querySelector('#tbl_req');
        var form = $('#grn-form');

        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            },
            "Please check your input."
        );

        form.validate({

            
            
            errorPlacement: function errorPlacement(error, element) {
                $(element).parents('.form-group').append(
                error.addClass('col invalid-feedback small d-block')
            )
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            rules: {
                datepicker:{
                    //yyyy-mm-dd
                    "date_format":/^((?:19|20)\\d\\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])/
                }
            
            },
            messages: {
                datepicker: {
                    date_format: "please use yyyy-mm-dd format"
                }
            }
        });

        $('#btn-grn-create').click(function (){
            
            if (!form.valid()) {
                return;
            }
            else {
                form.submit();
            }
            
            
        });

        //make autocomplete suggestions request through ajax request as user types in search box
        $('#purchase-search').keyup(function(e) {                       
            $.ajax({
                type: "POST",
                url: window.location.origin + '/purchase/autocomplete',
                data: {
                    'purchase-search': 'true',
                    'search-value': '%' + e.target.value + '%'                  
                },
                success: function(purchase_order_info) {
                    
                    purchaseOrders = purchase_order_info;
                    
                    var autoSuggestions = [];

                    try {                        
                        
                        purchase_order_info.forEach(function(item, index) {
                            autoSuggestions[index] = item.PURCHASE_ORDER_ID + ' : ' + item.ORDER_DATE + ' : ' + item.VENDOR_NAME;
                            
                        });

                        //set autocomplete suggestion to search input
                        autocomplete(document.getElementById("purchase-search"), autoSuggestions);
                    } catch (err) {
                       // console.log(err);
                    }

                },
                error: function(data) {
                    //console.log(data);
                }
            });
        });

        //make autocomplete suggestions request through ajax request as user types in search box
        $('#requisition-search').keyup(function(e) { 
            console.log(e.target.value)  ;                    
            $.ajax({
                type: "POST",
                url: window.location.origin + '/requisition/autocomplete',
                data:{
                    'requisition-search': 'true',
                    'search-value': '%' + e.target.value + '%'                  
                },
                success: function(requisition_info) {
                    
                    requisitions = requisition_info;
                    
                    var autoSuggestions = [];

                    try {                        
                        console.log(requisition_info);
                        requisition_info.forEach(function(item, index) {
                            autoSuggestions[index] = '#' + item.REQUISITION_NO + ' : ' + item.EBQ_CODE + '  ' + item.REQUISITION_DATE                            
                        });

                        //set autocomplete suggestion to search input
                        autocomplete(document.getElementById("requisition-search"), autoSuggestions);
                    } catch (err) {
                       console.log(err);
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
        
        //handle on add item button click
        $('#btn-purchase-add').click(function() {

            var itemName = $('#purchase-search').val();

            var splitInput = itemName.split(' : ');
            var purchase_id = splitInput[0];
            var purchase_date = splitInput[1];
            var purchase_vendor = splitInput[2];

            //declarations
            obj = {};
	        is_search_value_in_autocomplete = -1;	        
    
            //verify search value with suggestion list
            try {
                
                purchaseOrders.forEach(function (item, index){
                    if (item.PURCHASE_ORDER_ID == purchase_id) {
                        is_search_value_in_autocomplete = index;
                        obj = purchaseOrders[index];
                    }
	            });
            }
            catch(err) {
                Swal.fire('Error!','This item does not exist','error');
            }
	         
    
            //check if selected hub is already added
	        if (is_search_value_in_autocomplete > -1) {	            
                
                document.getElementById("purchase-search-group").style.display = 'none';
                document.getElementById("requisition-search-group").style.display = 'none';
                document.getElementById("defaultClose").style.display = 'none';
                document.getElementById("purchase-info").style.display = 'block';

                document.getElementById("label-purchase-id").appendChild(document.createTextNode(obj.PURCHASE_ORDER_ID));
                document.getElementById("label-purchase-date-o").appendChild(document.createTextNode(obj.ORDER_DATE));  
                document.getElementById("label-purchase-date-r").appendChild(document.createTextNode(obj.DATE_REQUIRED));  
                document.getElementById("label-purchase-via").appendChild(document.createTextNode(obj.SHIP_VIA));  
                document.getElementById("label-purchase-vname").appendChild(document.createTextNode(obj.VENDOR_NAME));          
                document.getElementById("label-purchase-vaddress").appendChild(document.createTextNode(obj.VENDOR_ADDRESS));  
                document.getElementById("label-purchase-pobox").appendChild(document.createTextNode(obj.VENDOR_PO_BOX));  
                document.getElementById("label-purchase-zip").appendChild(document.createTextNode(obj.VENDOR_ZIP_CODE));  
                document.getElementById("label-purchase-tcost").appendChild(document.createTextNode("R"+obj.TOTAL));  


                $.ajax({
                    type: "POST",
                    url: window.location.origin + '/purchase/autocomplete',
                    data: {
                        'purchase-stock-request': 'true',
                        'purchase-id': obj.PURCHASE_ORDER_ID,                    
                    },
                    success: function(purchase_stock_info) {

                        purchaseStock = purchase_stock_info
                        //stores stockItems related to selected purchase order
                        purchase_stock_info.forEach(function(item, index) {
                        
                            row = document.createElement('tr');
        
                            row.innerHTML = `                    
                            <td class="">${item.EBQ_CODE}</td>
                            <input type = "hidden" name = "ebqs[]" value = ${item.EBQ_CODE}>
                            <td class="">${item.DESCRIPTION}</td>
                            <td class="">${item.METRIC_DESCRIPTION}</td>
                            <td class="">${item.QUANTITY}</td> 
                            <input type = "hidden" name = "quantities[]" value = ${item.QUANTITY}>                    
                            <td class="">${item.UNIT_PRICE}</td> 
                            <input type = "hidden" name = "prices[]" value = ${item.UNIT_PRICE}>               
                            <td class="">${item.TOTAL}</td>                            
                            <td class="" name = "switch-cell"></td>                            
                            <td class="" name = "note-cell"></td>`;
        
                            tbl_stock.append(row);
                            
                                                    
                        });

                        var elements = document.getElementsByName('switch-cell');
                        var notes = document.getElementsByName('note-cell');

                        elements.forEach(function(item, index){
                            var checkbox = document.createElement('input');
                            checkbox.setAttribute("type", "checkbox");
                            checkbox.setAttribute("checked", "checked");
                            checkbox.setAttribute("value", "1");
                            checkbox.setAttribute("id", "check-"+purchase_stock_info[index].EBQ_CODE);
                            checkbox.setAttribute("onClick", "ManageNoteText(this)");
                            checkbox.setAttribute("name", "check");
                            
                            var textbox = document.createElement('input');
                            textbox.setAttribute("type", "text");
                            textbox.setAttribute("value", "in_order");
                            textbox.setAttribute("class", "form-control");
                            textbox.setAttribute("name", "textboxes[]");
                            textbox.setAttribute("id", "text-"+purchase_stock_info[index].EBQ_CODE);


                            var checkboxvalue = document.createElement('input');
                            checkboxvalue.setAttribute("type", "hidden");
                            checkboxvalue.setAttribute("id", "checkval-"+purchase_stock_info[index].EBQ_CODE);
                            checkboxvalue.setAttribute("value", "1");
                            checkboxvalue.setAttribute("name", "checkboxes[]");

                            

                            item.append(checkbox);
                            item.append(checkboxvalue);
                            notes[index].append(textbox);
                            

                        });
                        document.getElementById("grn-type").value = "purchase";
                        document.getElementById("source-id").value = obj.PURCHASE_ORDER_ID; 
                        id_set = true;
                        
                    },
                    error: function(data) {
                       console.log(data);
                    }

                    


                });
                
            }          
            else {
                Swal.fire('Error!','This item does not exist','error');
            }
        });

         
        //handle on add item button click
        $('#btn-requisition-add').click(function() {

            var itemName = $('#requisition-search').val();

            var splitInput = itemName.split(' : ');
            var requisition_id = splitInput[0].split('#')[1];
            var requisition_date = splitInput[1];

            //declarations
            obj = {};
            is_search_value_in_autocomplete = -1;	        

            //verify search value with suggestion list
            try {
                requisitions.forEach(function (item, index){
                    if (item.REQUISITION_NO == requisition_id) {
                        is_search_value_in_autocomplete = index;
                        obj = requisitions[index];
                    }
                });
            }
            catch(err) {
                Swal.fire('Error!','This item does not exist','error');
            }
            
            console.log(is_search_value_in_autocomplete)
            //check if selected hub is already added
            if (is_search_value_in_autocomplete > -1) {	            
                
                document.getElementById("purchase-search-group").style.display = 'none';
                document.getElementById("requisition-search-group").style.display = 'none';
                document.getElementById("defaultOpen").style.display = 'none';
                document.getElementById("requisition-info").style.display = 'block';

                document.getElementById("label-requisition-no").appendChild(document.createTextNode(obj.REQUISITION_NO));
                document.getElementById("label-requisition-date").appendChild(document.createTextNode(obj.REQUISITION_DATE));  
                document.getElementById("label-requisition-notes").appendChild(document.createTextNode(obj.NOTES));  
                document.getElementById("label-requisition-item").appendChild(document.createTextNode(obj.EBQ_CODE)); 

                $.ajax({
                    type: "POST",
                    url: window.location.origin + '/requisition/autocomplete',
                    data: {
                        'requisition-stock-request': 'true',
                        'ebq-code': obj.EBQ_CODE                 
                    },
                    success: function(requisition_stock) {

                        requisitionStock = requisition_stock
                        //stores stockItems related to selected purchase order
                        requisition_stock.forEach(function(item, index) {
                        
                            row = document.createElement('tr');

                            row.innerHTML = `                    
                            <td class="">${item.EBQ_CODE}</td>
                            <input type = "hidden" name = "ebqs[]" value = ${item.EBQ_CODE}>
                            <td class="">${item.DESCRIPTION}</td>
                            <td class="">${item.METRIC_DESCRIPTION}</td>                            
                            <td class="">${item.QUANTITY}</td>
                         `;

                            tbl_req.append(row);   
                        });

                        var elements = document.getElementsByName('switch-cell');
                        var notes = document.getElementsByName('note-cell');

                        elements.forEach(function(item, index){
                            var checkbox = document.createElement('input');
                            checkbox.setAttribute("type", "checkbox");
                            checkbox.setAttribute("checked", "checked");
                            checkbox.setAttribute("value", "1");
                            checkbox.setAttribute("id", "check-"+requisition_stock[index].EBQ_CODE);
                            checkbox.setAttribute("onClick", "ManageNoteText(this)");
                            checkbox.setAttribute("name", "check");
                            
                            var textbox = document.createElement('input');
                            textbox.setAttribute("type", "text");
                            textbox.setAttribute("value", "in_order");
                            textbox.setAttribute("class", "form-control");
                            textbox.setAttribute("name", "textboxes[]");
                            textbox.setAttribute("id", "text-"+requisition_stock[index].EBQ_CODE);

                            var checkboxvalue = document.createElement('input');
                            checkboxvalue.setAttribute("type", "hidden");
                            checkboxvalue.setAttribute("id", "checkval-"+requisition_stock[index].EBQ_CODE);
                            checkboxvalue.setAttribute("value", "1");
                            checkboxvalue.setAttribute("name", "checkboxes[]");

                            

                            item.append(checkbox);
                            item.append(checkboxvalue);
                            notes[index].append(textbox);

                            

                        });

                        document.getElementById("grn-type").value = "requisition";
                        document.getElementById("source-id").value = obj.REQUISITION_NO; 
                        id_set = true;
                    },
                    error: function(data) {
                    console.log(data);
                    }


                });
                
            }          
            else {
                Swal.fire('Error!','This item does not exist','error');
            }
        });
        
        
	});

    function validateForm(){
        if(id_set == true){
            return true;
        }else{
            Swal.fire('Error!','A purchse order or requisition must be selected','error');
            return false;
        }
    }

    //allow user to reset the form
    $('#btn-change-purchase').click(function(){
            table_parent = document.querySelector('#tbl_stock_body');
        while (table_parent.firstChild) {
            table_parent.removeChild(table_parent.firstChild);
        }

        document.getElementById("purchase-search-group").style.display = '';
        document.getElementById("requisition-search-group").style.display = '';
        document.getElementById("defaultClose").style.display = '';
        document.getElementById("purchase-info").style.display = 'none';

        document.getElementById("label-purchase-id").innerHTML = "";
        document.getElementById("label-purchase-date-o").innerHTML = "";  
        document.getElementById("label-purchase-date-r").innerHTML = "";  
        document.getElementById("label-purchase-via").innerHTML = "";  
        document.getElementById("label-purchase-vname").innerHTML = "";          
        document.getElementById("label-purchase-vaddress").innerHTML = "";  
        document.getElementById("label-purchase-pobox").innerHTML = "";  
        document.getElementById("label-purchase-zip").innerHTML = "";  
        document.getElementById("label-purchase-tcost").innerHTML = "";  

        document.getElementById("label-requisition-no").innerHTML = "";
        document.getElementById("label-requisition-date").innerHTML = "";  
        document.getElementById("label-requisition-notes").innerHTML = "";  
        document.getElementById("label-requisition-item").innerHTML = "";  

        id_set = false;
        $('#purchase-search').val('');

    });

    //allow user to reset the form
    $('#btn-change-requisition').click(function(){
            table_parent = document.querySelector('#tbl_stock_body');
        while (table_parent.firstChild) {
            table_parent.removeChild(table_parent.firstChild);
        }

        document.getElementById("requisition-search-group").style.display = '';
        document.getElementById("purchase-search-group").style.display = '';
        document.getElementById("defaultOpen").style.display = '';
        document.getElementById("requisition-info").style.display = 'none';

        document.getElementById("label-purchase-id").innerHTML = "";
        document.getElementById("label-purchase-date-o").innerHTML = "";  
        document.getElementById("label-purchase-date-r").innerHTML = "";  
        document.getElementById("label-purchase-via").innerHTML = "";  
        document.getElementById("label-purchase-vname").innerHTML = "";          
        document.getElementById("label-purchase-vaddress").innerHTML = "";  
        document.getElementById("label-purchase-pobox").innerHTML = "";  
        document.getElementById("label-purchase-zip").innerHTML = "";  
        document.getElementById("label-purchase-tcost").innerHTML = "";  

        document.getElementById("label-requisition-no").innerHTML = "";
        document.getElementById("label-requisition-date").innerHTML = "";  
        document.getElementById("label-requisition-notes").innerHTML = ""; 
        document.getElementById("label-requisition-item").innerHTML = "";   

        id_set = false;
        $('#requisition-search').val('');

    });


    //text_note

    function ManageNoteText(control){        
        var related_ebq = control.id.split("-").pop();

        var textbox = document.getElementById('text-'+related_ebq);
        var checkval = document.getElementById('checkval-'+related_ebq)
        textbox.value = "";

        var comparater_argument = document.createElement('input');
        comparater_argument.setAttribute("checked", "checked");
        
        if(control.checked == comparater_argument.checked){
            textbox.value = "in_order";
            checkval.value = "1";
        }else{
            textbox.value = "";
            checkval.value = "0";
        }

    }
   
    //tab logic
   
    function openSearch(evt, tab) {
        var i, tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        tablinks = document.getElementsByClassName("tab-button");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        document.getElementById(tab).style.display = "block";

        if (tab=='purchase'){
            document.getElementById('requisition-table').style.display = "none";
            document.getElementById('purchase-table').style.display = "block";
        }
        else {
            document.getElementById('requisition-table').style.display = "block";
            document.getElementById('purchase-table').style.display = "none";
        }
        
        
        evt.currentTarget.className += " active";
    }

	        
</script>
<?php echo view('_general/footer');