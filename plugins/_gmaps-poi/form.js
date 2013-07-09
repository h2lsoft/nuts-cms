
$('#former #geocoder').click(function(){

    address = $('#former #Address').val()+", ";
    address += $('#former #ZipCode').val()+" ";
    address += $('#former #City').val()+", ";
    address += $('#former #Country').val();

    geocoder('Latitude', 'Longitude', address);

});
