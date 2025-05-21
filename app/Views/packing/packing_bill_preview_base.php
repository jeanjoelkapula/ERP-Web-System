<div class="card-body p-5">
	<div class="row">
        <div class="col-sm-8 pb-4"> 
            <div class="media align-items-center mb-4">
                <div class="media-body text-big font-weight-bold ml-2">
                    <!-- Logo -->
                    <div class="d-flex">
                        <div class="">
                            <div class="w-100 position-relative" >
                                <img src="/assets/img/pep-logo-print.png" id = "pep-logo-print"/>
                            </div>
                        </div>
                    </div>
                    <!-- / Logo -->
                </div>
            </div>	
        </div> 
       
        <div class="" style = "">
            <div class="d-flex text-center align-items-center mb-4">
                <div class="media-body text-big font-weight-bold ml-2">
                    <h1 class="font-weight-bold py-3 mb-4">
                        Packing Bill
                    </h1>
                </div>
            </div>	
        </div>	
	</div>
    <br>
    <br>
    <div class="row">
		<div class="col-sm-6 pb-4">   
            <div class="row">
                <div class="table-responsive mb-4">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>
                                    <strong><?php if (!$is_internal) echo 'Store No:'; else echo 'Source Hub:'; ?></strong><br><br>
                                    <strong><?php if (!$is_internal) echo 'Store Name:'; else echo 'Destination Hub:'; ?></strong><br><br>
                                    <strong>Delivery Date:</strong><br><br>
                                    <strong>Suggested Packing Date:</strong>
                                </td>
                                <td>
                                    <?php 
                                        if ($is_internal) {
                                            echo $source_hub;
                                        }
                                        else {
                                            if(isset($store['id'])) echo $store['id'];
                                        }
                                        
                                        
                                    ?><br><br>
                                    <?php 
                                        if ($is_internal) {
                                            echo $destination_hub;
                                        }
                                        else {
                                            if(isset($store['name'])) echo $store['name'];
                                        }
                                        
                                        
                                    ?><br><br>
                                    <?php if(isset($delivery_date)) echo $delivery_date;?><br><br>
                                    <?php if(isset($pack_date)) echo $pack_date;?>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
		<div class="col-sm-6 pb-4">   
            <div class="row">
                <div class="table-responsive mb-4">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>
                                <?php if (!$is_internal) { ?> <strong> Deliver to:</strong><br><br> <?php } ?>
                                    <strong>Ship Via:</strong><br><br>
                                </td>
                                <td>
                                   <?php if (!$is_internal) { if(isset($deliver_to_site) && $deliver_to_site != 0) echo 'Deliver to Site: <br><br>'; else if (isset($destination_hub)) echo $destination_hub; }?>
                                   <?php if(isset($ship_via)) echo $ship_via;?><br><br>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
    </div>

	<div class="row">
		<div class="table-responsive mb-4">
			<table class="table m-0">
				<thead>
					<tr>
						<th class="py-3">
							Code
						</th>
						<th class="py-3">
							Description
						</th>
						<th class="py-3">
                            Quantity
						</th>
						<th class="py-3">
							Short
						</th>
						<th class="py-3">
							Check
						</th>
					</tr>
				</thead>
				<tbody id = "tbl_stock_body">
            
                    
				</tbody>
			</table>
		</div>
	</div>
    <br>
    <br>
    <div class="row">
		<div class="col-sm-6">   
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>
                                    <strong class="mr-4">Checked By:</strong>
                                    .......................................................................
                                </td>
                                <td>
                                
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
		<div class="col-sm-6 pb-4">   
            <div class="row">
                <div class="table-responsive table-borderless">
                    <table class="table m-0">
                        <tbody>
                            <tr>
                                <td>
                                    <strong class="mr-4">Date:</strong>
                                    .......................................................................
                                </td>
                                <td class="text-left">
                                   
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
    </div>
</div>