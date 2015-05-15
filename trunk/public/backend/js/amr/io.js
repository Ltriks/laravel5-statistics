var reader = null;
function handleFileSelect(evt, isTypedArray) {
    var f = $('#'+evt).get(0).files[0]; // File Object

    // Loop through the FileList and render image files as thumbnails.
    reader = new FileReader();

	reader.onload = (function (file) {
		return function (e) {
			var samples = new AMR({
				   	benchmark: true
			}).decode(e.target.result);

			AMR.util.play(samples);
		}
	})(f);
	
	// Read the file as a Binary String
	// var r = reader.readAsDataURL('http://7sbyx5.com1.z0.glb.clouddn.com/1_5716_Voice_20141230_105132.amr');
    var r = reader.readAsBinaryString(f);
}

$("#playAmr").on('click', function(evt){
	var amrId = $(this).attr('value');
	handleFileSelect('file-'+amrId);
});


