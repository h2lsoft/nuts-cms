// add uploader component
if($('#fieldset_Uploader'))
{
	str  = '<p class="fileUploader"><div id="file-uploader"></div></p>';
	$('#fieldset_Uploader').append(str);


	$(document).ready(function(){

		var uploader = new qq.FileUploader({
                element: $('#file-uploader')[0],
				allowedExtensions: ['jpg', 'png', 'gif'],
                action: 'index.php?mod=_gallery&do=add&_action=m_upload',
				
				onSubmit: function(id, fileName){
					
				},
				onComplete: function(id, fileName, responseJSON){
					// alert(fileName+': '+responseJSON);
				}

            });

	});

	
	
}


