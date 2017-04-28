$(document).ready(function(){
	$('#reset_password').click(function(){
		$.post('ajax/reset_password.php', {'id':$('#url').val()}, function(){
			$('.error').text('An email has been sent to '+$('#create_account_email')+' with a link to reset your password. Please follow this link to reset your password.');
		});
	});
	$('.delete_form').click(function(){
		//var websocket = new WebSocket('ws://itweb-dev.mycompany.com:8080');
		//websocket.onopen = function(e) {
		//	console.log("Connection established!");
		//};
		var c = confirm("Are you sure you want to delete this form? All of your answers will be erased!");
		if (c){
			var id=$(this).attr('id').split("_");
			id=id[1];
			$.post('ajax/delete_form.php', {'id':id}, function(data){
				$('#form_'+id).parent().parent().parent().remove();
				websocket.send(JSON.stringify({'type':'delete','userform':id}));
			});
		}
	});	
});