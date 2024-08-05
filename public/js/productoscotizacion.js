$('#producto').select2({
    placeholder: "Insertar productos faltantes...",
    minimumInputLength: 0,
    multiple: false,
    tags: false,
    /*ajax: {
        type:'get',
        url: url_global+'/busProd',
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
    }*/
 });