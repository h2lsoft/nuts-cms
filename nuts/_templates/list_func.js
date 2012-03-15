// rewrite uri with system
$("#list th a").each(
	function(){
		$(this).attr('href', "javascript:system_goto('"+$(this).attr('href')+"', 'list');");
	}
);

// table
$('a.tt').tooltip({
    track: true,
    delay: 0,
    showURL: false,
    showBody: " - ",
    opacity: 0.85
});

function listTrColor()
{
	// click event
	$('#list:not(.listDnd) tbody tr td[noclick!="1"]').click(function () {

		if($("#list tbody .listDnd").length)return;

		if($(this).parents('tr').hasClass('tr_selected'))
		{
			$(this).parents('tr').removeClass('tr_selected')
		}	
		else
		{
			$(this).parents('tr').addClass('tr_selected');
		}	
	});	

}

listTrColor();


// drag and drop table
if($("#list tbody .listDnd").length)
{
	$("#list tbody .listDnd < td").css('cursor', 'move');	
	
	$('#list').sortable({
	
		axis:'y',
		opacity: 0.8,
		cursor: 'move',

		// forceHelperSize: true,
		// forcePlaceholderSize: true,
		helper: function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index)
			{
				// Set helper cell sizes to match the original sizes
				$(this).width($originals.eq(index).width())
			});

			return $helper;
		},

		items: 'tr.row',
		update: function(event, ui) {
			
			list = $('#list').sortable("toArray");			
			uri = $("#list .listDnd").attr('uri');			
			system_position(uri, list);
			
		}
	
	});
}
