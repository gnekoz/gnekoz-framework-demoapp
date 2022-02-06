$(document).ready(function() {
	
	var explicitSave = false;
	
	$("#consuntivazioni-form .date-picker").datepicker({ dateFormat: 'dd/mm/yy', altField: 'input[name="search-date"]', altFormat: 'yy-mm-dd' }, $.datepicker.regional['it']);
	
	$('#consuntivazioni-form input[name="save-action"]').click(function() {
		explicitSave = true;
	});	
	
	$('#consuntivazioni-form').submit(function() {
		
		// Controllo se invocato esplicitamente il salvataggio
		if (explicitSave)
			return true;
		
		// Controllo
		if ($('#consuntivazioni-table').has('input[name="_modified[]"][value="1"]').length) {
			var ret = window.confirm("Ci sono modifiche non salvate. Vuoi salvare le modifiche?");
			if (!ret) {
				$('input[name="_modified[]"]').val('');							
			}
		}
		return true;
	});
	
	
	$('#consuntivazioni-table tbody td').change(function(event) {
				
		// Ottengo la riga coinvolta
		var row = $(this).closest('tr');
		
		// Aggiorno il flag di modifica
		row.find('input[name="_modified[]"]').val('1');		
	});
	
			
	$('.table-page-link').click(function() {
		$('input[name="search-date"]').val($(this).attr('rel'));
		$('#consuntivazioni-form').submit();
	});
});