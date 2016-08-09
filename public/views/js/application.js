$(document).ready(function(){
    
    $("#partida_data").mask("9999-99-99");
    $("#partida_horario").mask("99:99");
    $("#usuario_cpf").mask("999.999.999-99");
    $("#usuario_datanascimento").mask("99/99/9999");
    
    $("#resgate_valor").maskMoney({showSymbol:true, symbol: "", decimal:",", thousands:"."});
        
});