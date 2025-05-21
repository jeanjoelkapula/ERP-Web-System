<form method="post" id="form_stock" action="<?php echo $url ?>" class="form-horizontal" autocomplete="off">
	<input type="hidden" name="form_create_stock" value="<?php echo $form_create; ?>" />
	<input type="hidden" name="form_update_stock" value="<?php echo $form_update; ?>" />
	<input type="hidden" name="ebq_code" value="<?php if (isset($ebq_code)) {
													echo $ebq_code;
												} ?>" />

	<div class="form-group">
		<label class="col form-label">Product Code</label>
		<div class="col">

			<?php if (isset($found) && $found == true && $action_type == 'create') { ?>
				<input type="text" name="ebq_code" class="form-control is-invalid" value="<?php if (isset($ebq_code)) {
																								echo $ebq_code;
																							} ?>" placeholder="Enter a unique product code">
				<small class="invalid-feedback">The product code already exists in the database, please enter a unique product code for a new item entry.</small>
			<?php } else if (isset($found) && $found == true && $action_type == 'update') { ?>
				<input type="text" name="ebq_code" class="form-control is-invalid" value="<?php if (isset($ebq_code)) {
																								echo $ebq_code;
																							} ?>" placeholder="Enter a unique product code">
				<small class="invalid-feedback">The product code already exists in the database, please enter a unique product code for an item code.</small>
				<?php } else { ?>
				<input type="text" class="form-control" name="ebq_code" required="" value="<?php if (isset($ebq_code)) {
																								echo $ebq_code;
																							} ?>" placeholder="Enter a unique product code">
			<?php } ?>
		</div>
	</div>

	<div class="form-group">
		<label class="col form-label">Product Description</label>
		<div class="col">

		<?php if (isset($foundDescription) && $foundDescription == true && $action_type == 'create') { ?>
				<input type="text" name="stock_description" class="form-control is-invalid" value="<?php if (isset($stock_description)) {
																								echo $stock_description;
																							} ?>" placeholder="Enter a unique product description">
				<small class="invalid-feedback">The product description already exists in the database, please enter a unique product description for a new item entry.</small>
			<?php } else if (isset($foundDescription) && $foundDescription == true && $action_type == 'update') { ?>
				<input type="text" name="stock_description" class="form-control is-invalid" value="<?php if (isset($stock_description)) {
																								echo $stock_description;
																							} ?>" placeholder="Enter a unique product description">
					<small class="invalid-feedback">The product description already exists in the database, please enter a unique product description for a new item entry.</small>
			<?php } else { ?>
				<input type="text" class="form-control" name="stock_description" required="" value="<?php if (isset($stock_description)) {
																								echo $stock_description;
																							} ?>" placeholder="Enter a unique product description">
			<?php } ?>
		</div>
	</div>


	

	<div class="form-group">
		<label class="col form-label">Is the item built out of other stock items?</label>
		<div class="col">

			<?php		
			if (isset($ebq_code) && !isset($found)) {
				$sqlQuery = "SELECT COUNT(*) AS COUNT FROM TBL_STOCK_COMBINATION WHERE EBQ_CODE_LG = '$ebq_code';";
				$result = $db->query($sqlQuery);
				$foundItems = false;

				foreach ($result->getResult('array') as $row) : {
						if ($row['COUNT'] > 0) {
							$foundItems = true;
						}
					}
				endforeach;		
			
				if($foundItems) {
					?>
					<label class="switcher">	
						<input type="checkbox" checked='checked' name='is_built' id='is_built' class="switcher-input">
							<span class="switcher-indicator">
								<span class="switcher-yes"></span>
								<span class="switcher-no"></span>
							</span>
							<span class="switcher-label">Yes</span>
						</label>
					<?php
				} else {
					?>
						<label class="switcher">
							<input type="checkbox" <?php if (isset($stockQuantityAll)) { echo "checked='checked'"; } ?> name='is_built' id='is_built' class="switcher-input">
							<span class="switcher-indicator">
								<span class="switcher-yes"></span>
								<span class="switcher-no"></span>
							</span>
							<span class="switcher-label">Yes</span>
						</label>
					<?php
				}
			} else {
			?>
				<label class="switcher">
					<input type="checkbox" <?php if (isset($is_built) && $is_built == 1) { echo "checked='checked'"; } ?> name='is_built' id='is_built' class="switcher-input">
					<span class="switcher-indicator">
						<span class="switcher-yes"></span>
						<span class="switcher-no"></span>
					</span>
					<span class="switcher-label">Yes</span>
				</label>
			<?php
			}
			?>

		</div>
	</div>

	<div class="form-group built_stock_group" id="built_stock_group" style="display:none;">
		<div class="col">
			<div>
				<div class="row">
					<div class="form-group">
						<label class="col form-label">Stock Item Search <span class="text-danger">*</span></label>
						<div class="col">
							<div class="autocomplete" style="width:500px;">																	
								<input id="stockSearch" type="text" class="form-control" name="stock_item_search" placeholder="Search for a stock item">															
							</div>
						</div>
					</div>
					<div class="form-group" style="margin-left: 15px; margin-top: 23px;">
						<Button type="button" class="btn btn-primary" id="btn-stock-add"><i class="fas fa-plus"></i>&nbsp; Add Item </Button>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="table-responsive mb-4">
						<table class="table table-striped font-sm" id="tbl_stock">
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
									</th>
								</tr>
							</thead>
							<tbody id="tbl_stock_body">
								<?php if (isset($ebq_code) && !isset($found)) {
									$sql = "SELECT tsc.EBQ_CODE_SUB , 
									(SELECT DESCRIPTION FROM TBL_STOCK WHERE EBQ_CODE = tsc.EBQ_CODE_SUB) AS DESCRIPTION,
									tsc.QUANTITY 
									FROM TBL_STOCK ts 
									INNER JOIN 
									TBL_STOCK_COMBINATION tsc
									ON ts.EBQ_CODE = tsc.EBQ_CODE_LG WHERE ts.EBQ_CODE = '$ebq_code';";
									
									$hubResult = $db->query($sql);
									foreach ($hubResult->getResult('array') as $row) : {
								?>
								<tr>
											<td class="py-3"><?php echo $row['EBQ_CODE_SUB']; ?></td>
											<td class="py-3"><?php echo $row['DESCRIPTION']; ?></td>																					
											<td class="py-3">
												<div class="input-group" style="width: 170px;">
													<span class="input-group-btn">
														<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="create-stock[<?php echo $row['EBQ_CODE_SUB']; ?>]">
															-
														</button>
													</span>
													<input type="text" name="create-stock[<?php echo $row['EBQ_CODE_SUB']; ?>]" class="form-control" value="<?php echo $row['QUANTITY']; ?>" min="1" max="99999" >													
													<span class="input-group-btn">
														<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="create-stock[<?php echo $row['EBQ_CODE_SUB']; ?>]" >
															+
														</button>
													</span>
												</div>
											</td>
											<td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove">X</span></a></td>
									</tr>
									<?php
										}
									endforeach;
									?>
								<?php } else if (isset($stockQuantityAll)) {
				    
								foreach ($stockQuantityAll as $stockCode => $stockQuantityValue) : { 

									// get the product description
									$sqlDescription = "SELECT DESCRIPTION FROM TBL_STOCK WHERE EBQ_CODE = '$stockCode'";

									$descripResult = $db->query($sqlDescription);
                            
									// loop through the result to get the average cost of the item
									foreach ($descripResult->getResult('array') as $row) : {                        
										$descrip = $row['DESCRIPTION'];                        
									}
									endforeach;
								?>
								<tr>
											<td class="py-3"><?php echo $stockCode; ?></td>


											<td class="py-3"><?php echo $descrip; ?></td>																					
											<td class="py-3">
												<div class="input-group" style="width: 170px;">
													<span class="input-group-btn">
														<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="create-stock[<?php echo $stockCode; ?>]" >
															-
														</button>
													</span>
													<input type="text" name="create-stock[<?php echo $stockCode; ?>]" class="form-control" value="<?php echo $stockQuantityValue; ?>" min="1" max="99999" >													
													<span class="input-group-btn">
														<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="create-stock[<?php echo $stockCode; ?>]" >
															+
														</button>
													</span>
												</div>
											</td>
											<td class="py-3"><a href="#aboutModal" data-toggle="modal" data-target="#myModal" class="btn btn-circle btn-default btn-remove">X</span></a></td>
									</tr>
									<?php
										}
									endforeach;
									?>
								<?php }?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="form-group">
		<label class="col form-label">Is the item active?</label>
		<div class="col">

			<?php
			if (isset($is_active) && $is_active != 1) {
			?>
				<label class="switcher">
					<input type="checkbox" name='is_active' class="switcher-input">
					<span class="switcher-indicator">
						<span class="switcher-yes"></span>
						<span class="switcher-no"></span>
					</span>
					<span class="switcher-label">Yes</span>
				</label>
			<?php
			} else {

			?>
				<label class="switcher">
					<input type="checkbox" name='is_active' checked='checked' class="switcher-input">
					<span class="switcher-indicator">
						<span class="switcher-yes"></span>
						<span class="switcher-no"></span>
					</span>
					<span class="switcher-label">Yes</span>
				</label>
			<?php } ?>

		</div>
	</div>

	<?php /*<div class="not_built_field_group">
		<div class="form-group">
			<label class="col form-label">Purchase Cost (Per item)</label>
			<div class="col"><input type="number" class="form-control" id="purchase_cost" name="purchase_cost" value="<?php if (isset($purchase_cost)) {
																													echo $purchase_cost;
																												} ?>" placeholder="Enter the stock purchase cost" min="0"></div>
		</div>
	</div> */?>

	
	<div class="form-group purchase_cost" style="display:none;">
		<div class="form-group">
			<label class="col form-label">Purchase Cost (Sum of sub-items, calculated upon stock update)</label>
			<div class="col">
				<input type="number" class="form-control" name="purchase_cost_large" id="purchase_cost_large" value="<?php if (isset($purchase_cost)) {
																												echo $purchase_cost;
																											} ?>" placeholder="The last cost entered for the stock item" readonly min="0">
			</div>
		</div>
	</div>


	<div class="form-group not_built_field_group">
			<label class="col form-label">Markup Percentage</label>
			<div class="col"><input type="number" class="form-control" name="markup" value="<?php if (isset($markup)) {
																													echo $markup;
																												} ?>" placeholder="Enter the stock markup percentage" min="0" max="100"></div>
		</div>	

	<div class="form-group not_built_field_group">
			<label class="col form-label">Wastage Percentage</label>
			<div class="col"><input type="number" class="form-control" name="wastage" value="<?php if (isset($wastage)) {
																													echo $wastage;
																												} ?>" placeholder="Enter the stock wastage percentage" min="0" max="100"></div>
		</div>	

	<div class="form-group not_built_field_group">
			<label class="col form-label">Min Re-Order</label>
			<div class="col">
				<input type="number" class="form-control" name="minreorder" value="<?php if (isset($minreorder)) {
																												echo $minreorder;
																											} ?>" placeholder="Enter the stock minimum re-order" min="0">
			</div>
		</div>


	
	<div class="form-group last_cost" style="display:none;">
		<div class="form-group">
			<label class="col form-label">Last Cost</label>
			<div class="col">
				<input type="number" class="form-control" name="last_cost" value="<?php if (isset($last_cost)) {
																												echo $last_cost;
																											} ?>" placeholder="The last cost entered for the stock item" readonly min="0">
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col form-label">Metric</label>
		<div class="col">

			<?php
			echo "<select class='custom-select btn-block' name='metricdesc'>";
			if (isset($metName)) {
				echo $metName;
			} else {
				$sql = "SELECT METRIC_DESCRIPTION FROM TBL_METRIC";
				$metricResult = $db->query($sql);
				foreach ($metricResult->getResult('array') as $row) : {
					echo "<option>{$row['METRIC_DESCRIPTION']}</option>";
				}
				endforeach;
			}
			echo "</select>";
			?>
		</div>
	</div>																								

	<br>

	<div class="hr-line-dashed"></div>

	<br />

	<button type="button" class="btn btn-primary" id="btn-add-all"><i class="fas fa-check"></i>
		<?php
		if ($action_type == 'create') {
		?>
			Create Stock Item
		<?php
		} else {
		?>
			Update Stock Item
		<?php
		}
		?>
	</button>
</form>