(function() {
  var i = document.createElement('input');
  jQuery.support.placeholder = 'placeholder' in i;
})();

$(document).ready(function(){

	if($.support.placeholder)return;
	$("input:text[placeholder!=''], textarea[placeholder!='']").each(function(){

		// init
		cvalue = $.trim($('#'+$(this).attr('id')).attr('placeholder'));
		if(cvalue != '')
		{
			$('#'+$(this).attr('id')).val(cvalue).addClass('nuts_watermark');

			// focus
			$('#'+$(this).attr('id')).focus(function(){
				cvalue = $('#'+$(this).attr('id')).attr('placeholder');
				if($('#'+$(this).attr('id')).val() == cvalue)
					$('#'+$(this).attr('id')).val('').removeClass('nuts_watermark');
			});

			// blur
			$('#'+$(this).attr('id')).blur(function(){
				cvalue = $('#'+$(this).attr('id')).attr('placeholder');
				if($('#'+$(this).attr('id')).val() == '')
					$('#'+$(this).attr('id')).val(cvalue).addClass('nuts_watermark');
			});
		}

	});

});