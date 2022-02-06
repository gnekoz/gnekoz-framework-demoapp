$(document).ready(function() {
    
    // Conferma cancellazione righe
    $("table a.row-delete").click(function() {
        var ret = window.confirm("Sei sicuro di voler cancellare la riga selezionata?");
        return ret;
    });
});