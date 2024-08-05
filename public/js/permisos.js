$('#permisos').select2({
   placeholder: "Inserte el nombre registrado en diamante...",
   minimumInputLength: 0,
   multiple: true,
   tags: true,
   ajax: {
       type:'get',
       url: url_global+'/perus',
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