//plugin bootstrap minus and plus
$('.btn-number').click(function(e){
    handlePlusMinusClick(e);
});

$('.input-number').focusin(function(e){
    handleFocus(e);
});

$('.input-number').change(function(e) {
    handleInputChange(e);
});

$(".input-number").keydown(function (e) {
   handleKeyDown(e)
});

function handlePlusMinusClick(e){
    e.preventDefault();
    fieldName = $(e.target).attr('data-field');
    type      = $(e.target).attr('data-type');
    var input = $(`input[name='${fieldName}']`);
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('min')) {
                $(e.target).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(e.target).attr('disabled', true);
            }

        }
    } else {
        input.val(0);
    }
}

function handleInputChange(e){
    minValue =  parseInt($(e).attr('min'));
    maxValue =  parseInt($(e).attr('max'));
    valueCurrent = parseInt($(e).val());
    name = $(e).attr('name');
    if (!isNaN(valueCurrent)) { 
        if(valueCurrent >= minValue) {
            $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
        } else {
            alert('Sorry, the minimum value was reached');
            $(e).val($(e).data('oldValue'));
        }
        if(valueCurrent <= maxValue) {
            $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
        } else {
            alert('Sorry, the maximum value was reached');
            $(e).val($(e).data('oldValue'));
        }
    }
    else {
        $(e).val($(e).data('oldValue'));
        }
}

function handleKeyDown(e){
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) || 
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
    
}

function handleFocus(e){
    $(e).data('oldValue', $(e).val());
}

function handleUnrestrictedPlusMinusClick(e){
    e.preventDefault();
    fieldName = $(e.target).attr('data-field');
    type      = $(e.target).attr('data-type');
    var input = $(`input[name='${fieldName}']`);
    var currentVal = parseInt(input.val());
  
        if(type == 'minus') {
            input.val(currentVal - 1).change(); 
            if(parseInt(input.val()) == input.attr('min')) {
                $(e.target).attr('disabled', true);
            }

        } else if(type == 'plus') {
            input.val(currentVal + 1).change();
            if(parseInt(input.val()) == input.attr('max')) {
                $(e.target).attr('disabled', true);
            }

        } else {
            input.val(0);
        }
    }
