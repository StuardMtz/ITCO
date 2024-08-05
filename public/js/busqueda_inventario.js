$('#producto').select2({
   placeholder: "Buscar productos por categoria...",
   minimumInputLength: 2,
   multiple: false,
   tags: false,
   ajax: {
       type:'get',
       url: url_global+'/prod_inve',
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