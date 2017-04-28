$(document).ready(function(){	
	$('#delete_archive').click(function(){
		var c = confirm("Are you sure you want to delete these items from the archive? ");
		if (c){
			var d = [];
			$('input[type=checkbox]').each(function(){
				if ($(this).prop('checked')){
					var row = $(this).parent().parent().parent().children().index($(this).parent().parent());
					d.push({'form_id':data[row].fid});
				}
			});
			$('.clickableImage').each(function(){
				var row = $(this).parent().parent().parent().children().index($(this).parent().parent());
				d.push({'form_id':data[row].fid});
			});
			$.post('scripts/delete_archive.php', {data:d, reportid:$('#reportid').val()}, function(){
				location.reload();
			});
		}
	});
	
});