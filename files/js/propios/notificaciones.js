setInterval(function(){
    ajxM = $.ajax({
        type: 'POST',
        url: '../ajax/notificaciones.php?first=true',
        context: document.body
    }).done(function(data) {
        $('#notLayer').html(data)
    });
}, 30000);
ajxM = $.ajax({
    type: 'POST',
    url: '../ajax/notificaciones.php?first=true',
    context: document.body
}).done(function(data) {
    $('#notLayer').html(data)
});
