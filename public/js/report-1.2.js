$(document).ready(function() {
   $("#report-chiamate-form #from").datepicker({ dateFormat: 'dd/mm/yy', altField: 'input[name="from-date"]', altFormat: 'yy-mm-dd' }, $.datepicker.regional['it']);
   $("#report-chiamate-form #to").datepicker({ dateFormat: 'dd/mm/yy', altField: 'input[name="to-date"]', altFormat: 'yy-mm-dd' }, $.datepicker.regional['it']);
   $("#report-consumi-form #from").datepicker({ dateFormat: 'dd/mm/yy', altField: 'input[name="from-date"]', altFormat: 'yy-mm-dd' }, $.datepicker.regional['it']);
   $("#report-consumi-form #to").datepicker({ dateFormat: 'dd/mm/yy', altField: 'input[name="to-date"]', altFormat: 'yy-mm-dd' }, $.datepicker.regional['it']);
   $("#report-contatti-form #from").datepicker({ dateFormat: 'dd/mm/yy', altField: 'input[name="from-date"]', altFormat: 'yy-mm-dd' }, $.datepicker.regional['it']);
   $("#report-contatti-form #to").datepicker({ dateFormat: 'dd/mm/yy', altField: 'input[name="to-date"]', altFormat: 'yy-mm-dd' }, $.datepicker.regional['it']);   
});


