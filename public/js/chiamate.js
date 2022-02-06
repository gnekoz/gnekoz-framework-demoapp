$(document).ready(function() {
	var base = $('head base').attr('href');
	
	$("#chiamata-form #pubblicita").autocomplete({
		source: base + '/chiamata/elencoTipoPubblicita',
		minLength: 2
	});
	
	$("a.send-notification").click(function() {
		var idChiamata = $(this).siblings('input[type="hidden"][name="id"]').val();
		var activeLink = $(this);
		var prevImage = $(this).css('background-image');
		$(this).data('prevImage', prevImage);
		
		$(this).css('background-image', 'url("' + base + '/images/loader.gif")');
		$.getJSON(base + '/chiamata/inviaMail', {id: idChiamata}, function(data) {
			var prevImage = activeLink.data('prevImage');
			activeLink.css('background-image', '');
            activeLink.removeClass('send-email');
            activeLink.addClass('email-sent');
			alert(data);
		}).error(function() {			
			var prevImage = activeLink.data('prevImage');
			activeLink.css('background-image', prevImage);
		});		
	});
});