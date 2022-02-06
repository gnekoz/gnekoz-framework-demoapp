$(document).ready(function() {
	
	var base = $('head base').attr('href');
	
	var ricalcolaImporto = function() {
		
		// FIXME url...
		var pPrezzoUnitario = $('#consumo-form #prezzo_unitario').val();
		var pQuantita = $('#consumo-form #quantita').val();
		var par = { prezzo_unitario: pPrezzoUnitario, quantita: pQuantita };
		$.getJSON(base + '/consumo/calcolaImporto', par, function(data) {
			$('#consumo-form #importo').val(data);
		});		
	};
	
	$("#consumo-form #id_prodotto").change(function() {
		
		// FIXME url...
		var pProdotto = $('#consumo-form #id_prodotto').val();		
		var par = { prodotto: pProdotto};
		$.getJSON(base + '/consumo/getPrezzo', par, function(data) {
			$('#consumo-form #prezzo_unitario').val(data);
		});
		
		ricalcolaImporto();
	});	
		 	
	$("#consumo-form #prezzo_unitario, #consumo-form #quantita").change(ricalcolaImporto);
});