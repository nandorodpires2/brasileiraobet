$(document).ready(function(){
   
    // masks
    $("#partida_data").mask("9999-99-99");
    $("#partida_horario").mask("99:99");
    $("#usuario_cpf").mask("999.999.999-99");
    $("#usuario_datanascimento").mask("99/99/9999");
    
    // maskmoney
    $("#resgate_valor").maskMoney({showSymbol:true, symbol: "", decimal:",", thousands:"."});
    
    // numeric only
    $("#aposta_placar_mandante").numeric();
    $("#aposta_placar_visitante").numeric();
    
    /**
     * Notificacao
     * @returns {undefined}
     */
    $(".message-lida").click(function() {       
        var id = $(this).attr('id');
        notificacaoLida(id);        
    });
    
        
});

function notificacaoLida(id) {
    
    var base_url = get_base_url();
    
    $.ajax({
        url: base_url + "notificacao/lida",
        type: "get",
        data: {
            id: id       
        },
        dataType: "json",
        beforeSend: function() {                  
            
        },
        success: function(data) { 
            if (data.success === 1) {
                $("#row-" + id).hide();
            }
        },
        error: function(error) {
            
        }
    });
    
}

function get_base_url() {    
    if (window.location.host === 'localhost') {
        return "http://" + window.location.host + "/brasileiraobet/public/";
    } else {
        return "http://" + window.location.host + "/";
    } 
}