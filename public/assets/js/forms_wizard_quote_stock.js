// With validation
$(function() {
  var $form = $('#quote-stock-wizard');
  var $btnFinish = $('<button class="btn-finish btn btn-primary hidden mr-2" type="button">Finish</button>');

  // Set up validator
  $form.validate({
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
      
    }
  });

  // Initialize wizard
  $form
    .smartWizard({
      autoAdjustHeight: false,
      backButtonSupport: true,
      useURLhash: false,
      showStepURLhash: false,
      toolbarSettings: {
        toolbarExtraButtons: [$btnFinish]
      }
    })
    .on('leaveStep', function(e, anchorObject, stepNumber, stepDirection) {
      // stepDirection === 'forward' :- this condition allows to do the form validation
      // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
      if (stepDirection === 'forward'){ return $form.valid(); }
      return true;
    })
    .on('showStep', function(e, anchorObject, stepNumber, stepDirection) {
      var $btn = $form.find('.btn-finish');

      // Enable finish button only on last step
      if (stepNumber === 1) {
        $btn.removeClass('hidden');
      } else {
        $btn.addClass('hidden');
      }
    });

  // Click on finish button
  $form.find('.btn-finish').on('click', function(e){
    if (!$form.valid()){ return; }
    var data = $form.serializeArray();
    var form_enable = data.find(item=> item.name ==='form_enable');
    var stock = data.find(item=> item.name.substring(0,5)==="stock");

    if(form_enable.value === 'false'){
      e.preventDefault();
      Swal.fire('Error!','This quote has already been approved or declined and cannot modified','error');      
      return;
    }
    if(!stock){
      e.preventDefault();
      Swal.fire('Error!','The quote requires at least one stock item!','error');
      return;
    }

		$.post('/quote/create/ajax/precheck_quantity/', data,
			function(result){
			var res = JSON.parse(result);
      if(res && res.length > 0){
        e.preventDefault();
        res.forEach(unavailableStock => {
          $(`#${unavailableStock}`).closest('tr').find('input.input-number').addClass('invalid-feedback small d-block').attr('style','margin-top:0px;outline:#d9534f solid 1px');
        })
        Swal.fire('Error!','The Highlighted stock items do not have enough quantity to complete the quote!','error');
        return;
      }
        $form.submit();
        return false;
		});		
    
  });
});
