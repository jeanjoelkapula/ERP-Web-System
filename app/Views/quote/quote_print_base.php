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
       
        <div class="" >
            <div class="d-flex text-center align-items-center mb-4">
                <div class="media-body text-big font-weight-bold ml-2">
                    <h1 class="font-weight-bold py-3 mb-4">
                        <?php if(isset($quote_type_name)) echo $quote_type_name;?>
                    </h1>
                </div>
            </div>	
        </div>	
	</div>
    <br>
    <br>
    <div class="row">
        <div class="media-body text-big font-weight-bold ml-2">
            <h5 class="font-weight-bold">
                Project Information
            </h5>
        </div>
    </div>
    <div class="row">
		<div class="col-sm-6 pb-4">   
            <div class="row">
                <div class="table-responsive mb-4">
                    <table class="table m-0">
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Store Number:</strong><br><br>
                                    <strong>Delivery Date:</strong>
                                </td>
                                <td>
                                <?php if(isset($store['id'])) echo $store['id'];?><br><br>
                                <?php if(isset($delivery_date)) echo $delivery_date;?> 
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
                    <table class="table m-0">
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Store Name:</strong><br>
                                </td>
                                <td>
                                    <?php if(isset($store['name'])) echo $store['name'];?>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
    </div>
    <div class="row">
        <div class="media-body text-big font-weight-bold ml-2">
            <h5 class="font-weight-bold">
                Contractor Information
            </h5>
        </div>
    </div>
    <div class="row">
		<div class="col-sm-6 pb-4">   
            <div class="row">
                <div class="table-responsive mb-4">
                    <table class="table m-0">
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Company Name:</strong><br><br>
                                    <strong>Action at Store:</strong><br><br>
                                    <strong>Prepared By:</strong>
                                </td>
                                <td>
                                    <?php if(isset($contractor['name'])) echo $contractor['name'];?><br><br>
                                    <?php if(isset($action_name)) echo $action_name;?><br><br>
                                    <?php if(isset($created_by['name'])) echo $created_by['name'];?>                                
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
                    <table class="table m-0">
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Ship Via:</strong><br><br>
                                    <strong>Quote Date:</strong><br><br>
                                    <strong>Contact No</strong>
                                </td>
                                <td>
                                    <?php if(isset($ship_via)) echo $ship_via;?><br><br>
                                    <?php if(isset($created_date)) echo $created_date;?><br><br>
                                    <?php if(isset($created_by['contact'])) echo $created_by['contact'];?>
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
							Price
						</th>
						<th class="py-3">
							Quantity
						</th>
						<th class="py-3">
							Total
						</th>
					</tr>
				</thead>
				<tbody>
                <?php
                    $sql = "SELECT * FROM TBL_QUOTE_STOCK_CATEGORY;";
                    $result = $db->query($sql);
                    foreach ($result->getResult('array') as $row) { ?>
                        <tr>
                            <thead class="thead-light">
                                <tr  id="stock-category_<?php echo $row['ID']?>">
                                    <th colspan="5"><strong><?php echo $row['NAME']?></strong></th>
                                </tr>
                            </thead>
                        </tr>
                        
                <?php } ?>
                <tr>				
                    <?php if(!isset($tax_percentage)) $tax_percentage = 15; ?> 
						<td colspan="4" class="text-right py-3">
							Subtotal:<br>
							<?php if(isset($pki_percentage)) echo "PKI Management Fee @ ".number_format($pki_percentage,2)."%:<br>" ?>
							Tax (<?php echo $tax_percentage;?>%):<br>
							<span class="d-block text-big mt-2">Total:</span>
						</td>
						<td class="py-3">
							<strong>ZAR <?php if(isset($stock_total)) echo number_format($stock_total,2);?></strong><br>
							<?php if(isset($stock_total) && isset($pki_percentage)){ 
                                $pki_increase = $stock_total*($pki_percentage/100); 
                                echo "<strong>ZAR ".number_format($pki_increase,2)."</strong><br>";
                            }?>
							<strong>ZAR <?php if(isset($stock_total) && isset($pki_increase)) echo number_format(($stock_total+$pki_increase) *($tax_percentage/100),2); else if(isset($stock_total)) echo number_format($stock_total *($tax_percentage/100),2); else echo 0.00?></strong><br>
							<strong class="d-block text-big mt-2">ZAR <?php if(isset($stock_total)) echo number_format(($stock_total+$pki_increase)*(1+($tax_percentage/100)),2);?></strong>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<br>
</div>