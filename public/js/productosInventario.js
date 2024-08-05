$('#producto').select2({
    placeholder: "Buscar producto...",
    minimumInputLength: 2,
    multiple: false,
    tags: false,
    ajax: {
        type:'get',
        url: url_global+'/busproinve',
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