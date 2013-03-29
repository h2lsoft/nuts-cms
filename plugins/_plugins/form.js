transformSelectToAuctoComplete('Name', '#former');

// assign default category
if(empty($('#former #Category').val())){

    if(!empty($('#search_form #Category').val())){
        $('#former #Category').val($('#search_form #Category').val());

        setTimeout(function(){
            $('#former #Name').focus();
        }, 500);

    }

}
