function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
function regescape(s){
	return s.replace(/[-\/\\^$*+?.()|[\]{}:\'!\*]/g, '\\$&');
}

$(document).ready(function(){
	var clear_selected = function(){ //Set the form to a proper state when a field has been moved over. Clear the highlighted fields, and reset hidden fields
		$('.form_field').each(function(){
			$(this).removeClass('grayhighlighted');
		});
		$('#new_form_id').val('');
		$('#new_form_field').val('');
		$('#existing_form_field').val('');
		$('#existing_form_id').val('');
	};
	
	$('.form_item').click(function(){ //Expand a form's fields
		$(this).next('.form_fields').toggle();
	})
	$('.form_field:not(.taken)').live('click', function(event){ //Only allow a form element to be used once in a report. Once it hasn't been used make it unclickable. If a clickable field is clicked, highlight it and put it into a temporary hidden field so that when the right arrow button is clicked, it can be moved over to the report fields
		clear_selected();
		var x = $('#reports_move_controls').position().left;
		$('#reports_move_controls').css({position:"absolute", top:event.pageY, left:x});
		$(this).addClass('grayhighlighted');
		if ($(this).parent().parent().attr('id') == 'new_form_fields'){
			$('#new_form_field').val($(this).parent().prev().text()+':'+$(this).text().replace(/"/g,"'"));
			var id = $(this).attr('id');
			var firstchar = id.charAt(0);
			if (firstchar<='9' && firstchar >= '0')
				id="S__"+id;
			$('#new_form_id').val(id);
		}
		else{ //If we're clicking on a form field from the report container
			$('#existing_form_field').val($(this).text());
			$('#existing_form_id').val($(this).attr('id'));
		}
	});
	$("#move_field_left").click(function(){ //Moving a form field into the report fields
		if ($('#new_form_field').val()){
			var name=$('#new_form_field').val();
			var split_name=name.split(/:(.+)?/)[1].replace(":","");
						
			var id=regescape($('#new_form_id').val());
			var firstchar = id.charAt(0);
			if (firstchar<='9' && firstchar >= '0')
				id="S__"+id;
			$('#existing_form_fields').append('<div rel="'+$('#new_form_fields #'+id).parent().prev().attr('id')+'" id="'+$('#new_form_id').val()+'" class="form_field"><div class="field_info">'+name+' AS:</div><input type="text" id="as_'+$('#new_form_id').val()+'" value="'+split_name+'"/><div class="moveupdown"><span class="moveup left"><img src="images/moveup.png" /></span><span class="movedown right"><img src="images/movedown.png" /></span></div></div>');
			$('#new_form_fields #'+id).addClass('taken');
		}
		else if ($('#custom_field').val()){
			$('#existing_form_fields').append('<div class="form_field"><div class="field_info">'+$('#custom_field').val()+' AS:</div><input class="custom" type="text" value="'+$('#custom_field').val()+'"/><div class="moveupdown"><span class="moveup left"><img src="images/moveup.png" /></span><span class="movedown right"><img src="images/movedown.png"/></span></div></div>');
		}
		clear_selected();
	});
	$("#move_field_right").click(function(){ //Taking a form field out of the report fields
		if ($('#existing_form_field').val()){
			$('#existing_form_fields').find('.grayhighlighted').remove();
			$('#new_form_fields #'+(regescape($('#existing_form_id').val()))).removeClass('taken');
		}
		clear_selected();
	});
	$('.moveupdown .movedown').live('click', function(){ //Moving a form element down in the report
		$(this).parent().parent().insertAfter($(this).parent().parent().next());
	});
	$('.moveupdown .moveup').live('click', function(){ //Moving a form element up in the report
		$(this).parent().parent().insertBefore($(this).parent().parent().prev());
	});
	$('#custom_field').keyup(function(){ //Not much happening. Just making sure nothing is highlighted if we're adding a custom field
		clear_selected();
	});
	$('#make_report_form').submit(function(event){	//Prepare the fields. Validate required fields.	
		var valid=true;
		$('.required').each(function(){
			if ($(this).val()=='')
				valid=false;
		});
		if (!valid){
			alert("Please fill out required fields (fields with asterisks).");
			event.preventDefault();
		}
		var form_id=js_id=counter=as=0;
		$('#existing_form_fields .form_field').each(function(){
			form_id=($(this).attr('rel'))?$(this).attr('rel'):-1;
			js_id=($(this).attr('id'))?$(this).attr('id'):counter.toString();
			js_id_escaped=regescape(js_id);
			as=($('#as_'+js_id_escaped).val())?regescape($('#as_'+js_id_escaped).val()):regescape($(this).find('.custom').val());
			if (form_id != -1){
				var name=$(this).text().replace("AS:","");
				name=regescape(name.split(/:(.+)?/)[1]);
			}
			else{
				var name=regescape($(this).text().replace("AS:",""));
			}
			
			$('#make_report_form').append("<input type='hidden' name='form_id["+counter+"]' value='"+form_id+"'/>");
			$('#make_report_form').append('<input type="hidden" name="field['+counter+']" value="'+name+'"/>');
			$('#make_report_form').append('<input type="hidden" name="js_id['+counter+']" value="'+js_id+'"/>');
			$('#make_report_form').append('<input type="hidden" name="as['+counter+']" value="'+as+'"/>');
			counter++;			
		});
	});
	$('#delete_report').click(function(e){
		var c = confirm("Are you sure you want to delete this report? ");
		if (!c)
			e.preventDefault();
	});
	$('input[name=report_radio]').click(function(){
		var v=$(this).val();
		$('.report_header_link').each(function(){
			var link = $(this).attr('href');
			link=link.split("?");
			link=link[0];
			link=link+"?id="+v;
			$(this).attr('href',link);
		});
	});
	$('#report_add_user').click(function(){
		$('#report_permissions').append('<div class="reports_row"><div class="reports_column_name"><input type="text" name="reports_user"/></div><div class="reports_column"><button class="add_report_user">Add</button></div></div>');
	});
	$('.add_report_user').live('click', function(){
		var t= $(this);
		$.post('scripts/add_permissions.php', {"user":t.parent().parent().find('input[name=reports_user]').val(), "reportid":$('input[name=reportid]').val()}, function(data){
			if (data){
				t.parent().parent().find('.reports_column_name').html(t.parent().parent().find('input[name=reports_user]').val());
				t.parent().parent().attr('id', data);
				t.attr("class","delete_permission");
				t.text("Remove");
			}
			else{
				alert("Something went wrong.");
			}
		});
	});
	$('.delete_permission').live('click', function(){
		var c = confirm("Are you sure you want to remove this user from access this report?");
		var t = $(this);
		if (c){
			$.post('scripts/add_permissions.php', {"id": $(this).parent().parent().attr('id'), "action":"delete"}, function(data){
				t.parent().parent().remove();
			});
		}
	});
	$('.move_all_fields').on('click', function(){
		var t = $(this);
		t.parent().find('.form_field').not('.taken').each(function(){
			var id=$(this).attr('id');
			var firstchar = id.charAt(0);
			if (firstchar<='9' && firstchar >= '0')
				id="S__"+id;
			$('#existing_form_fields').append('<div rel="'+t.parent().prev().attr('id')+'" id="'+id+'" class="form_field"><div class="field_info">'+t.parent().prev().text()+':'+$(this).text()+' AS:</div><input type="text" id="as_'+id+'" value="'+$(this).text()+'"/><div class="moveupdown"><span class="moveup left"><img src="images/moveup.png" /></span><span class="movedown right"><img src="images/movedown.png" /></span></div></div>');
			$(this).addClass('taken');
		});
	});
});
