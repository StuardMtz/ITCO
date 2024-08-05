$('#placa').select2({
    theme: "classic",
    language: "es",
    placeholder: "Nombre del propietario o número de placa...",
    minimumInputLength: 2,
    multiple: false,
    tags: false,
    width: 'resolve',
    ajax: {
        type:'get',
        url: url_global+'/propV',
        dataType: 'json',
        data: function(params){
        return{
            q: $.trim(params.term)
    };
 },
    processResults: function(data){
    return{
        results: data
    };
 },
    cache: false
    }
 });