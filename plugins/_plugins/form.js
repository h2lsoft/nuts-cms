str =  'Name';

// get array form select
cur_val = $('#former #'+str).val();

tmp_arr = array();
$('#former select#'+str+" option").each(function(){
	if(!empty($(this).attr('value')))
		tmp_arr[tmp_arr.length] = $(this).attr('value');
});


// replace select by input
$('#former select#'+str).after('<input type="text" name="'+str+'" id="'+str+'" value="'+cur_val+'" />').remove();


// autocomplete
$('#former #'+str).autocomplete(tmp_arr, {
		width: 400,
		highlight: false,
		multiple: false,
		scroll: true,
		scrollHeight: 300
});


// assign default category
if(empty($('#former #Category').val())){

    if(!empty($('#search_form #Category').val())){
        $('#former #Category').val($('#search_form #Category').val());

        setTimeout(function(){
            $('#former #Name').focus();
        }, 500);

    }

}