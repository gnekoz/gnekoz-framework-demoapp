$(document).ready(function () {
    var base = $('head base').attr('href');

    $("a.send-notification").click(function () {
        var idContatto = $(this).siblings('input[type="hidden"][name="id"]').val();
        var activeLink = $(this);
        var prevImage = $(this).css('background-image');
        $(this).data('prevImage', prevImage);

        $(this).css('background-image', 'url("' + base + '/images/loader.gif")');
        $.getJSON(base + '/contatto/inviaMail', {id: idContatto}, function (data) {
            var prevImage = activeLink.data('prevImage');
            activeLink.css('background-image', '');
            activeLink.removeClass('send-email');
            activeLink.addClass('email-sent');
            alert(data);
        }).error(function () {
            var prevImage = activeLink.data('prevImage');
            activeLink.css('background-image', prevImage);
        });
    });
    
    
    /**
     * 
     * @param integer idContatto
     * @returns void
     */
    var sendWa = function(idContatto) {
        $.ajax({
          dataType: "json",
          url: base + '/contatto/inviaWhatsApp',
          data: {id: idContatto},
          success: function (result) {
              if (result.link != null) {
                window.open(result.link, '_blank');                
              } else {
                alert(result.message);
              }
          },
          error: function () {
            alert("Errore durante l'invio del messaggio");
          }
        });        
    }
    
    
    $("a.send-wa-notification").click(function () {
        var idContatto = $(this).siblings('input[type="hidden"][name="id"]').val();
        sendWa(idContatto);
        return false;
    });
    
    
    var waLink = $('#wa-link').val();
    console.log(waLink);
    if (waLink != '' && waLink != undefined) {
        console.log('apertura pagina whatsapp');
        window.open(waLink, '_blank');
        window.location.href = '/gestionale/contatto';
    }    
//    $("#contatto-form #btn_save_send_whatsapp").click(function() {
//        var form = $("#contatto-form");
//        form.prop('target', '_blank');
//        form.submit();
//        //form.prop('target', '');
//        //return false;
//    });
});