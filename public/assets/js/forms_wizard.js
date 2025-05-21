// With validation
$(function() {
  var $form = $('#smartwizard-6');
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
      if (stepNumber === 3) {
        $btn.removeClass('hidden');
      } else {
        $btn.addClass('hidden');
      }
    });

  // Click on finish button
  $form.find('.btn-finish').on('click', function(){
    if (!$form.valid()){ return; }

    // Submit form
    alert("Great! We're ready to submit form.");
    return false;
  });
});
