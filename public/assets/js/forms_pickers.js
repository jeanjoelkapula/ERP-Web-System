// Bootstrap Datepicker
$(function() {
  var isRtl = $('html').attr('dir') === 'rtl';

  $('#datepicker').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#openingdate').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#maintenancedate').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#date_from').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#date_to').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#grndate').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#purchase-order-date').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#purchase-date-required').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#packing_date').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  $('#delivery_date').datepicker({
    orientation: isRtl ? 'auto right' : 'auto left',
    format: "yyyy-mm-dd",
    autoclose: true
  });

  // Date
});
