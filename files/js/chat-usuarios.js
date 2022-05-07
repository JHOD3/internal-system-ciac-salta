$(document).ready(function () {
    setInterval(() => { loadUserLogueados() }, 3000);
    let loadIntervalV = '';
    function loadUserLogueados() {
        fetch('/ajax/chat.php',{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                "Access-Control-Allow-Origin" : "*",
                "Access-Control-Allow-Credentials" : true
            },
            body: JSON.stringify({
                query: 'user_on',
            })
        })
        .then(response => response.json())
        .then(response => {
            let html = '';
            if (response != false) {
                for (let i = 0; i < response.length; i++) {
                    let activo = (response[i].activo == 'activo')?'punto':'punto-i';
                    let count = (parseInt(response[i].count)!=0 && response[i].id_usuario == response[i].chat_id_usuario)?'<span class="chat-count">'+response[i].count+'</span>':'';
                     html += '<a class="chat-perfil open-chat" data-nombre="' + response[i].nombre_completo + '" data-id-usuario="' + response[i].id_usuario + '" data-id-medico="' + response[i].id_medico + '">' +
                        '<img src="https://ui-avatars.com/api/?name=' + response[i].nombre_completo + '&amp;rounded=true&amp;background=50C2F7&amp;color=ffffff" width="30" alt="">' +
                        '<span class="chat-tag-sistema-content">' +
                        '<span class="chat-tag-sistema-' + response[i].sistema + '">' + response[i].sistema + '</span>' +
                         count +
                        '</span>' +
                        '<div class="chat-content-name-tag">' +
                        response[i].nombre_completo +
                        '<div class="chat-content-tag">' +
                        '<span class="punto-content">' +
                        '<span class="'+activo+'"></span>' +
                        '<span class="online">Online</span>' +
                        '</span>' +
                        '</div>' +
                        '</div>' +
                        '</a>';

                }
                $('.chat-all-content-person').html(html);
            }else{
                $('.chat-all-content-person').html(html);
            }

        });

    }

    $('.chat-content').on('click','.open-closed', function (event) {
        event.preventDefault();
        let i = $(this).parent().parent().find('.chat-content-body-footer').attr('class');
        if (i.indexOf('chat-body-show') > 0) {
            $(this).parent().parent().find('.chat-content-body-footer').removeClass('chat-body-show');
            $(this).text('Open');
        }else{
            $(this).parent().parent().find('.chat-content-body-footer').addClass('chat-body-show');
            $(this).text('Closed');
        }
    })

    function makeid(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() *
                charactersLength));
        }
        return result;
    }

    $('.chat-content').on('click', '.open-chat', function (event) {
        event.preventDefault();
        loadMenssaje($(this).data('id-medico'), $(this).data('id-usuario'), $(this).data('nombre'));
    })

    function loadInterval(id_medico, id_usuario, nombre,code) {
        setTimeout(() => {
            $("."+code).scrollTop($("."+code)[0].scrollHeight);
        }, 500);
        loadIntervalV = setInterval(() => { loadMenssajeOpen(id_medico, id_usuario, nombre,code) }, 1000)
    }

    function loadMenssaje(id_medico, id_usuario, nombre) {
        let data = {
            query: 'user_load_chat_medico',
            id_medico: id_medico ,
            id_usuario: id_usuario
        };

        fetch('/ajax/chat.php',{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin':'*'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(response => {
                let code = makeid(20);
                let html =  '<div style="align-items: end;display: flex;"><div class="chat" onload="'+loadInterval(id_medico, id_usuario, nombre,code)+'">' +
                    '                <div class="chat-header">' +
                    nombre +
                    '                    <a href="" class="open-closed">Closed</a>' +
                    '                    <i class="fa fa-window-close removeChat" aria-hidden="true"></i>'+
                    '                </div>' +
                    '                <div class="chat-content-body-footer chat-body-show">' +
                    '                    <div class="chat-body '+code+'">';
                for (let i = 0; i < response.length; i++) {
                    if ( response[i].enviado_por == 'medico' ) {
                        html += '                        <div class="chat-perfil border-0 chat-perfil-start">' +
                            '                            <img src="https://ui-avatars.com/api/?name='+nombre+'&amp;rounded=true&amp;background=50C2F7&amp;color=ffffff" width="30" alt="">' +
                            '                            <div class="chat-content-name-tag content-mensaje-recibido">' +
                            response[i].mensaje +
                            '                            </div>' +
                            '                        </div>';
                    }else{
                        html += '                        <div class="chat-perfil border-0 chat-perfil-start">' +
                            '                            <div class="chat-content-name-tag content-mensaje-enviado">' +
                            response[i].mensaje+
                            '                            </div>' +
                            '                            <img src="https://ui-avatars.com/api/?name='+nombre+'&amp;rounded=true&amp;background=50C2F7&amp;color=ffffff" width="30" alt="">' +
                            '                        </div>';
                    }


                }
                html += '                    </div>' +
                    '                    <div class="chat-footer">' +
                    '                        <input type="text" name="mensaje" class="mensaje" data-class="'+code+'" >' +
                    '                        <a href="" class="btn-send enviar_mensaje enviar-'+code+'" data-chat-content="'+code+'" data-id-usuario="'+id_usuario+'" data-id-medico="'+id_medico+'">' +
                    '                            <i class="fa fa-paper-plane" aria-hidden="true"></i>' +
                    '                        </a>' +
                    '                    </div>' +
                    '                </div>' +
                    '            </div></div>';
                $('.chat-content').prepend(html);
            });
    }

    $('.chat-content').on('keyup', '.mensaje', function (event) {
        let key = event.which;
        let IDclass = $(this).data('class');
        if (key == 13) {
            $('.enviar-'+IDclass).click();
        }
    })

    function loadMenssajeOpen(id_medico, id_usuario, nombre, code) {
        let data = {
            query: 'user_load_chat_medico',
            id_medico: id_medico ,
            id_usuario: id_usuario
        };

        fetch('/ajax/chat.php',{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin':'*'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(response => {
                let html =  '';
                for (let i = 0; i < response.length; i++) {
                    if ( response[i].enviado_por == 'medico' ) {
                        html += '                        <div class="chat-perfil border-0 chat-perfil-start">' +
                            '                            <img src="https://ui-avatars.com/api/?name='+nombre+'&amp;rounded=true&amp;background=50C2F7&amp;color=ffffff" width="30" alt="">' +
                            '                            <div class="chat-content-name-tag content-mensaje-recibido">' +
                            response[i].mensaje +
                            '                            </div>' +
                            '                        </div>';
                    }else{
                        html += '                        <div class="chat-perfil border-0 chat-perfil-start">' +
                            '                            <div class="chat-content-name-tag content-mensaje-enviado">' +
                            response[i].mensaje+
                            '                            </div>' +
                            '                            <img src="https://ui-avatars.com/api/?name='+nombre+'&amp;rounded=true&amp;background=50C2F7&amp;color=ffffff" width="30" alt="">' +
                            '                        </div>';
                    }
                }
                $('.'+code).html(html);
            });
    }


    $('.chat-content').on('click', '.removeChat', function (event){
        $(this).parent().parent().remove();
        clearInterval(loadIntervalV);
    })

    $('.chat-content').on('click', '.enviar_mensaje', function (event) {
        event.preventDefault();
        let input = $(this);
        let mensaje = input.parent().find('input').val();
        let chat_content = input.data('chat-content');
        fetch('/ajax/chat.php',{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin':'*'
            },
            body: JSON.stringify({
                query: 'user_send_mensage',
                id_usuario: input.data('id-usuario'),
                id_medico: input.data('id-medico'),
                mensaje: mensaje
            })
        })
        .then(response => response.json())
        .then(response => {
            input.val('');
            setTimeout(() => {
                $("."+chat_content).scrollTop($("."+chat_content)[0].scrollHeight);
            }, 500);
        });
    })

    $('.enviar_mensaje').on('click', function (event) {
        event.preventDefault();
        fetch('/ajax/chat.php',{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin':'*'
            },
            body: JSON.stringify({
                query: 'user_on',
            })
        })
            .then(response => response.json())
            .then(response => {
                for (let i = 0; i < response.length; i++) {
                    let html =  '<a class="chat-perfil">' +
                        '<img src="https://ui-avatars.com/api/?name='+response[i].nombre_completo+'&amp;rounded=true&amp;background=50C2F7&amp;color=ffffff" width="30" alt="">' +
                        '<span class="chat-tag-sistema-content">' +
                        '<span class="chat-tag-sistema-'+response[i].sistema+'">'+response[i].sistema+'</span>'+
                        '</span>'+
                        '<div class="chat-content-name-tag">' +
                        response[i].nombre_completo +
                        '<div class="chat-content-tag">' +
                        '<span class="punto-content">' +
                        '<span class="punto"></span>'+
                        '<span class="online">Online</span>' +
                        '</span>' +
                        '</div>' +
                        '</div>' +
                        '</a>';
                    $('.chat-all-content-person').append(html);
                }

            });
    })
});
