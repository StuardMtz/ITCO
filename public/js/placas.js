$('#placa').select2({
    theme: "classic",
    language: "es",
    placeholder: "Inserte el número de placa...",
    minimumInputLength: 2,
    multiple: false,
    tags: false,
    width: 'resolve',
    ajax: {
        type:'get',
        url: url_global+'/num_placa',
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
    cache: true
    }
 });