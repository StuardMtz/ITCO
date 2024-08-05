$('#noserie').select2({
    dropdownParent: $('#nuevaTransferencia'),
    placeholder: "Ingrese la serie de la factura...",
    minimumInputLength: 2,
    multiple: false,
    tags: false,
    tokenSeparators: [",", " "],
    ajax: {
        type:'get',
        url: url_global+'/SelSer',
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