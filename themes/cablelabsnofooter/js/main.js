jQuery(document).ready( function($) {
	$('nav li.lev1').hover(
		function(){
			clearTimeout($.data(this,'timer'));
			$(this).find('.sub_menu').stop(true,true).show();
			$(this).find('a:first').css('color', '#ff0000');
		},
		function(){
			$.data(this,'timer', setTimeout($.proxy(function() {$(this).find('.sub_menu').stop(true,true).hide();}, this), 100));
			$(this).find('a:first').css('color', '#ffffff');
		}
	);
		
});

