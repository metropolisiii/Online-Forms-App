function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function poll(table){
  	  $.ajax({ url: "ajax/checkin.php", cache:false, success: function(data){
		try{
		for (form_id in data){
			//Check if person has paid onsite
			for (field in data[form_id]){
				if ('person' in data[form_id][field]){
					var person = data[form_id][field]['person'];
					
					//New person added
					if ($('[userform='+form_id+'][person='+person+']').length == 0 && typeof data[form_id]["Person_"+person+"First_Name"] !== "undefined" && $(".input-sm").val() == ""){ //If the search is empty, modify stuff){
						var rowNode = table.row.add([
							data[form_id]["Person_"+person+"First_Name"]['response']+" "+data[form_id]["Person_"+person+"Last_Name"]['response'],
							data[form_id]['Company']['response'],
							"<a href='<?php echo VENDOR_REG_URL ?>?q="+data[form_id][field]['url']+"#p"+person+"' target='_blank'>Registration</a>",
							"<input type='checkbox' action='checkedin' "+(data[form_id][field]['checkedin']?"checked":"")+"/>",
							"<input type='checkbox' action='paid' "+(data[form_id][field]['paid']?"checked":"")+"/>",
							"<input type='text' class='comments square_info nochange' value='' /><input type='button' class='update_comments' value='Update'/>",
							"<select class='nochange cc_type'><option value=''></option><option value='VISA'>VISA</option><option value='MC'>Master Card</option><option value='DISC'>Discover</option><option value='AMEX'>AMEX</option></select>",
							"<textarea class='comments nochange'></textarea> <input type='button' class='update_comments' value='Update'/>",
							"<a class='btn btn-success' target='_blank' href='payment.php?id="+data[form_id][field]['account_id']+"'>Invoice </a>"
						])
						.draw()
						.node();
						$(rowNode).attr("userform",form_id);
						$(rowNode).attr("person", data[form_id][field]['person']);
						$(rowNode).find("td:eq(0)").addClass("name");
						$(rowNode).find("td:eq(1)").addClass("company");
						$(rowNode).find("td:eq(2)").addClass("registration");
						$(rowNode).find("td:eq(3)").addClass("checkedin");
						$(rowNode).find("td:eq(4)").addClass("paid");
						$(rowNode).find("td:eq(6)").addClass("square_info");
						$(rowNode).find("td:eq(7)").addClass("cc_type");
						$(rowNode).find("td:eq(8)").addClass("comments");
				
					}	
					else if (field.indexOf("First_Name")>0) {
						var paid=checkedin=false;
						
						//Update records
						$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".name").text(data[form_id]['Person_'+person+'First_Name']['response']+" "+data[form_id]['Person_'+person+'Last_Name']['response']);		
						if (data[form_id]['Person_'+person+'First_Name']['paid'] && data[form_id]['Person_'+person+'First_Name']['paid'] == 1)
							paid = true;
						if (data[form_id]['Person_'+person+'First_Name']['checkedin'] && data[form_id]['Person_'+person+'First_Name']['checkedin'] == 1)
							checkedin = true;
						$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".paid").find('input').prop('checked', paid);
						$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".checkedin").find('input').prop('checked', checkedin);
						if (!$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".square_info").find('.update_comments').is(":visible"))
							$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".square_info").find('input.comments').val(data[form_id]['Person_'+person+'First_Name']['square_info']);
						if (!$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".cc_type").find('.update_comments').is(":visible"))
							$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".cc_type").find('select').val(data[form_id]['Person_'+person+'First_Name']['cc_type']);
						if (!$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find("td.comments").find('.update_comments').is(":visible"))
							$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find("td.comments").find('textarea.comments').val(data[form_id]['Person_'+person+'First_Name']['comments']);
						
					}
					else if (field.indexOf("_Paid")>0 && typeof data[form_id]["Person_"+person+"First_Name"] !== "undefined"){
						var paid = false;
						if (data[form_id]["Person_"+person+"_Paid"]['response'] == "Paid"){
							paid = true;
						}
						$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".paid").find('input').prop('checked', paid);
					}
					if (!(['Person_'+person+'_Paid'] in data[form_id]) && typeof data[form_id]["Person_"+person+"First_Name"] !== "undefined")		
						$('[userform='+form_id+'][person='+data[form_id][field]['person']+']').find(".paid").find('input').prop('checked', false);
				}
			}
			$('[userform='+form_id+']').each(function(){
				$(this).find(".company").text(data[form_id]['Company']['response']);
			});						
							
		}
		//Delete records
		$('[userform='+form_id+']').each(function(){
			if (!(form_id in data))
				table.row($(this)).remove().draw();
			if (!("Person_"+$(this).attr('person')+"First_Name" in data[form_id])){
				table.row($(this)).remove().draw();
			}
		});
			
	
	 }
	  catch(err){
		  poll(table);
	  }
      }, dataType: "json"});
	 
	 
	
}

function setStats(d){
	$.ajax({
		type:'GET',
		url:'ajax/get_stats.php', 
		data:{'date': d}, 
		dataType: 'json',
		success:function(data){
			if (data['seven_fifty'] != null)
				$('#seven_fifty').text("$"+parseInt(data['seven_fifty'])*750);
			else
				$('#seven_fifty').text("$0");
			if (data['checked_in'] == null)
				$('#checked_in').text("0");
			else
				$('#checked_in').text(data['checked_in']);
		}
	});	
}

$(document).ready(function(){
	var table = $('#checkin_table').DataTable({ 'bPaginate': false, 'order':[1,'asc']});
	$(".dataTables_filter input")
    .unbind() // Unbind previous default bindings
    .bind("input", function(e) { // Bind our desired behavior
        // If the length is 3 or more characters, or the user pressed ENTER, search
        if(this.value.length >= 3 || e.keyCode == 13) {
            // Call the API search function
            table.search(this.value).draw();
        }
        // Ensure we clear the search if they backspace far enough
        if(this.value == "") {
            table.search("").draw();
        }
        return;
    });
	var checked_in = false;
	$('#checkin_table').floatThead({
		useAbsolutePositioning: false
	});
	//Move search to fixed header
	$('#checkin_table_filter').detach().appendTo('.table thead tr:nth-last-child(3) th:last-child');
	$('body').on('click', '#checkin_table input[type="checkbox"]', function(){
		var t = $(this);
		$.post('ajax/edit_customer_info.php', {'action':t.attr('action'), 'value':t.prop('checked'), 'userform':t.parent().parent().attr('userform'), 'person': t.parent().parent().attr('person')}, function(){
						
		});		
		if (t.attr('action') && t.attr('action') == 'checkedin'){
			if (t.is(':checked'))
				checked_in = true;
			else
				checked_in=false;
			$.post('ajax/update_comments.php', {'value':checked_in, 'userform':t.parent().parent().attr('userform'), 'person': t.parent().parent().attr('person'), 'checked_in':checked_in, 'date':$('#stats_date').val()}, function(){
				if (checked_in)
					$('#checked_in').text(parseInt($('#checked_in').text())+1);
				else{
					if (parseInt($('#checked_in').text()-1)<0)
						var numCheckedIn = 0;
					else
						var numCheckedIn = parseInt($('#checked_in').text()-1);
					$('#checked_in').text(parseInt(numCheckedIn));
				}
			});
		}
		else if (t.attr('action') && t.attr('action') == 'table_paid'){
			var action = "unpaid";
			if (t.is(':checked'))
				action = "paid";
			var userform = t.parent().parent().attr('userform');
			$.post('ajax/table_paid.php', {'action':action, userform:userform}, function(){
				i=0;
				if (action == "paid"){
					$("tr[userform='"+userform+"']").each(function(){
						$(this).find('.table_paid input').prop('checked',true);
						if (i < 6)
							$(this).find('.paid input').prop('checked', true);
						i++;
					});
				}
				else{
					$("tr[userform='"+userform+"']").each(function(){
						$(this).find('.table_paid input').prop('checked',false);
						if (i < 6)
							$(this).find('.paid input').prop('checked', false);
						i++;
					});
				}
				
			});			
		}
	});
	$('body').on('keyup paste', '#checkin_table .comments', function(){
		$(this).removeClass('nochange');
		$(this).next('.update_comments').show();
	});
	$('#checkin_table').on('change paste', '.cc_type', function(){
		$(this).removeClass('nochange');
		$(this).next('.update_comments').show();
	});
	$('body').on('click', '.update_comments', function(){
		var t = $(this);
		var isSquare = false;
		var isAmount = false;
		var isCCType = false;
		if (t.prev('.comments').hasClass('square_info'))
			isSquare = true;
		if (t.prev('.comments').hasClass('cc_type'))
			isCCType = true;
		if (t.prev('.comments').hasClass('amount_quantity'))
			isAmount = true;
		$.post('ajax/update_comments.php', {'value':t.prev('.comments').val(), 'userform':t.parent().parent().attr('userform'), 'person': t.parent().parent().attr('person'), 'isSquare':isSquare, 'isAmount':isAmount, 'isCCType':isCCType, 'date':$('#stats_date').val()},function(){
			t.prev('.comments').addClass('nochange');
			t.hide();
			if (isAmount){
				t.parent().find('.amount_paid').text("$"+parseInt(t.prev('.comments').val())*750);
				setStats($('#stats_date').val());
			}
		});
	});
	$('body').on('click', 'td.comments:not(.update_comments), td.square_info:not(.update_comments)', function(){
		$(this).find('.update_comments').hide();
	});
	
});