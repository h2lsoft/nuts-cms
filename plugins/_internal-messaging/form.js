// autocomplete
uri = 'index.php?mod=_internal-messaging&do=list&action=get_user';
$("#NutsUserIDFrom").autocomplete(uri, {
		width: 300,
		multiple: true,
		multipleSeparator: "; ",
		matchContains: false,
		autoFill: true,		
		delay:300,
		minChars:2,
		cacheLength:200,
		
		formatItem: function(data, i, n, value){
			return value;
			//return value.split(".")[0];
			
		},
		
		formatResult: function(data, value){
			return value;
			// return value.split(".")[0];
			
		}
});


$('#form p.bottom').css('margin-top', '-20px');