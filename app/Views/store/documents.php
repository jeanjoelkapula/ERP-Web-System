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
				<span class="text-muted font-weight-light">Store /</span> Documents
			</h4>
			<br/>             
			<div class="card">
				<div class="container-fluid flex-grow-1 container-p-y">
					<div class="container-m-nx container-m-ny bg-lightest mb-3">
						<ol class="breadcrumb text-big container-p-x py-3 m-0">
                            <?php
                                foreach($store_info as $info) {
                            ?>
                                <li class="breadcrumb-item">
								    <a href="javascript:void(0)"><?php echo $info['STORE_ID']; echo " - "; echo $info['STORE_NAME']; ?></a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0)">Documents</a>
                                </li>
                            <?php
                                }
                            ?>
						</ol>
						<hr class="m-0">
						<div class="file-manager-actions container-p-x py-2">
							<div>
								<a class="btn btn-primary mr-2" href="/store/upload?store=<?php foreach($store_info as $info) {echo $info['STORE_ID'];} ?>"><span style="color:white;" ><i class="ion ion-md-cloud-upload" ></i>&nbsp; Upload</span></a>
								<button type="button" id="download-button" class="btn btn-secondary icon-btn mr-2" disabled=""><i class="ion ion-md-cloud-download"></i></button>
								<div class="btn-group mr-2">
									<button type="button" id="remove-button" class="btn btn-default md-btn-flat px-2" disabled=""><i class="ion ion-md-close-circle"></i></button>
								</div>
							</div>
							<div>
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="btn btn-default icon-btn md-btn-flat active">
									<input type="radio" name="file-manager-view" value="file-manager-col-view" checked=""> <span class="ion ion-md-apps" style="margin-top:10px;"></span>
									</label>
									<label class="btn btn-default icon-btn md-btn-flat">
									<input type="radio" name="file-manager-view" value="file-manager-row-view"> <span class="ion ion-md-menu" style="margin-top:10px;"></span>
									</label>
								</div>
							</div>
						</div>
						<hr class="m-0">
					</div>
					<div class="file-manager-container file-manager-col-view">
						<div class="file-manager-row-header">
							<div class="file-item-name pb-2">Filename</div>
							<div class="file-item-changed pb-2">Created</div>
						</div>

						<?php
							foreach($file_results as $file){
								$icon = "";
								switch(strtolower($file['ext'])){
									case "jpeg":
									case "jpg":
									case "png":
									case "ico":
									case "gif":
									case "svg":
									case "ps":
									case "psd":
									case "tif":
									case "tiff":	
									case "ai":
									case "bmp":
										$icon = "fa-file-image";
										break;
									case "aif":
									case "cda":
									case "mid":
									case "midi":
									case "mp3":
									case "mpa":
									case "ogg":
									case "wav":
									case "wma":
									case "wpl":
										$icon = "fa-file-audio";
										break;

									case "ods":
									case "xlr":
									case "xls":
									case "xlsx":
										$icon = "fa-file-excel";
										break;
										
									case "3g2":
									case "3gp":
									case "avi":
									case "flv":
									case "h264":
									case "m4v":
									case "mkv":
									case "mov":
									case "mp4":
									case "mpg":
									case "mpeg":
									case "rm":
									case "swf":
									case "vob":
									case "wmv":
										$icon = "fa-file-video";
										break;

									case "doc":
									case "docx": 
									case "wpd":
									case "wps":
										$icon = "fa-file-word";
										break;

									case "pdf":
										$icon="fa-file-pdf";

										break;

									case "csv":
										$icon="fa-file-csv";
										break;

									default:
										$icon = "fa-file";
										break;
								}
						?>
							<div class="file-item">
								<div class="file-item-select-bg bg-primary"></div>
								<label class="file-item-checkbox custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" data-file="<?php echo $file['name']; ?>">
								<span class="custom-control-label"></span>
								</label>
								<div class="file-item-icon far <?php echo $icon; ?> text-secondary"></div>
								<a href="javascript:void(0)" class="file-item-name">
								<?php echo $file['name']; ?>
								</a>
								<div class="file-item-changed"><?php echo $file['date_created']; ?></div>
								<div class="file-item-actions btn-group">
									<button type="button" class="btn btn-default btn-sm rounded-pill icon-btn borderless md-btn-flat hide-arrow dropdown-toggle" data-toggle="dropdown"><i class="ion ion-ios-more"></i></button>
									<div class="dropdown-menu dropdown-menu-right">
										<a class="dropdown-item" href="javascript:void(0); downloadFile('<?php echo  $file['name']; ?>');">Download</a>
										<a class="dropdown-item" href="javascript:void(0); removeFile('<?php echo  $file['name']; ?>');">Remove</a>
									</div>
								</div>
							</div>
						<?php
							}
							
						?>

						<form id ="form-data" action="/store/documents/dl" method="post" style="display:none">
							<input id="store-input" type="text" name="store" value="<?php echo $store; ?>" hidden />
						</form>
						<form id ="form-remove" action="/store/documents/del" method="post" style="display:none">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Layout content -->
</div>
<?php echo view('_general/footer_javascript'); ?> 
<script src="/assets/vendor/libs/datatables/datatables.js"></script>
<script>
	function downloadFile(file) {
		 
		var form = document.getElementById('form-data');
		var store = document.getElementById('store-input');
		 form.innerHTML = '';
		 form.append(store);
		 input = document.createElement('input');
		 input.type="text";
		 input.name = 'file';
		 input.className="name-value";
		 input.value = file;
		 
		 form.append(input);

		 if (document.getElementById('form-data')==null){
			 document.body.appendChild(form);
		 } 
		 $(form).submit(); 
	}
	
	function removeFile(file) {
		Swal.fire({
			title: 'File Deletion',
			text: "Are you sure you want to remove this file?",
			type: 'question',
			showCancelButton: true,
			confirmButtonText: 'Yes',
			cancelButtonText: 'No'
		}).then(function(dismiss) {
			if (dismiss.value === true) {
				var formRemove = document.getElementById('form-remove');
				var store = document.getElementById('store-input');
				formRemove.innerHTML = '';
				formRemove.append(store);
					
				input = document.createElement('input');
				input.type="text";
				input.name = 'file[]';
				input.className="name-value";
				input.value = file;
			
				formRemove.append(input);
				

				if (document.getElementById('form-data')==null){
					document.body.appendChild(formRemove);
				} 
				$(formRemove).submit(); //  your code here
			}
		});
	}

	$(document).ready(function() {
		var downloadButton = document.getElementById('download-button');
		var removeButton = document.getElementById('remove-button');
		var form = document.getElementById('form-data');
		var store = document.getElementById('store-input');

	   $('input[type=checkbox').on('change', function(){
		   if ($('input[type=checkbox]:checked').length > 0){
			   downloadButton.disabled = false;
			   removeButton.disabled = false;
		   }
		   else{
			   downloadButton.disabled = true;
			   removeButton.disabled = true;
		   }
	   });

	   $(downloadButton).on('click', function(){

			var i = 0;                  //  set your counter to 1
			var length = $('input[type=checkbox]:checked').length;
			var elements = $('input[type=checkbox]:checked'); 
					   
			function myLoop() { 
				
				for(x=0; x < elements.length;++x){
					elements[x].checked = false;
					$(elements[x]).closest('div.selected').removeClass('selected border-primary');
				}
				
				//  create a loop function
				setTimeout(function() {  
					//  call a 3s setTimeout when the loop is called
					form.innerHTML = '';
					form.append(store);
					input = document.createElement('input');
					input.type="text";
					input.name = 'file';
					input.className="name-value";
					input.value = $(elements[i]).attr('data-file'); 
					
					form.append(input);
	
					if (document.getElementById('form-data')==null){
						document.body.appendChild(form);
					} 
					$(form).submit(); //  your code here
					i++;                    //  increment the counter
					if (i < length) {           //  if the counter < 10, call the loop function
					myLoop();             //  ..  again which will trigger another 
					}                       //  ..  setTimeout()
				}, 1000)
			}

			
			myLoop();

	   });

	   $(removeButton).on('click', function(){
			Swal.fire({
				title: 'File Deletion',
				text: "Are you sure you want to remove the selected files?",
				type: 'question',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then(function(dismiss) {
				if (dismiss.value === true) {
					var formRemove = document.getElementById('form-remove');
					var i = 0;                  //  set your counter to 1
					var length = $('input[type=checkbox]:checked').length;
					var elements = $('input[type=checkbox]:checked'); 
					formRemove.innerHTML = '';
					formRemove.append(store);
						
					for(x=0; x < elements.length;++x){
						elements[x].checked = false;
						$(elements[x]).closest('div.selected').removeClass('selected border-primary');
						input = document.createElement('input');
						input.type="text";
						input.name = 'file[]';
						input.className="name-value";
						input.value = $(elements[x]).attr('data-file'); 
					
						formRemove.append(input);
					}
					

					if (document.getElementById('form-data')==null){
						document.body.appendChild(formRemove);
					} 
					$(formRemove).submit(); //  your code here
				}
			});
		});

	});
</script>
<?php echo view('_general/footer');