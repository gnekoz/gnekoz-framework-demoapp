$(document).ready(function() {
	
	$('#user-form').submit(function() {
		$('#user-roles option').each(function() {
			$(this).attr('selected', 'selected');
		});		
	});
	
	
	$('option').dblclick(function() {
		if ($(this).parent().is('#all-roles')) {
			var id = $(this).attr('value');
			$('#user-roles').not(':has(option[value="' + id + '"])').append($(this).clone(true));			
		} else {
			$(this).detach();
		}
	});
    	
});