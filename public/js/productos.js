$('#producto').select2({
   placeholder: "Inserte el nombre registrado en diamante...",
   minimumInputLength: 2,
   multiple: false,
   tags: false,
   ajax: {
       type:'get',
       url: url_global+'/prod_faltante',
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