<div id="invoice-wizard-step-2" class="card animated fadeIn tab-pane step-content">
    <div class="card-body">
        <input type="hidden" name="job_id" value="" />

        <div class="form-group">
            <label class="col form-label">Account <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control required" name="account" required value="<?php if(isset($account)) echo $account;?>"></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Tax Reference <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control required" name="tax_reference" required value="<?php if(isset($tax_reference)) echo $tax_reference;?>"></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Pastel Invoice Number<span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control required" name="pastel_invoice" required value="<?php if(isset($pastel_invoice)) echo $pastel_invoice;?>"></div>
        </div>
        <div class="form-group">
            <label class="col form-label">Pastel Invoice Date <span class="text-danger">*</span></label>
            <div class="col"><input type="text" class="form-control" id="datepicker" name="pastel_date" value="<?php if(isset($pastel_date)) echo $pastel_date;?>" required></div>
        </div>

        <div class="form-group">
            <label class="col form-label">Store<span class="text-danger"></span></label>
            <div class="col"><input type="text" class="form-control" disabled name="store" value=""></div>
        </div>

        <div class="form-group">
            <label class="col form-label">Job <span class="text-danger"></span></label>
            <div class="col"><input type="text" class="form-control " disabled name="job_type" value=""></div>
        </div>
      
        <div class="hr-line-dashed"></div>
        <br>  
        <br>
    </div>
</div>
