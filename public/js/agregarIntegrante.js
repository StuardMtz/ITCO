$(document).ready(function(){
    var i=1;

$('#add').click(function(){
    i++;
    $('#dynamic_field').append('<div class="form-row" id="row'+i+'"><label for="nombreCargador"><b>Nombre del integrante</b></label><div class="input-group"><input type="text" class="form-control" name="nombre[]" required><div class="input-group-append"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></div><div class="valid-feedback">Excelente!</div><div class="invalid-feedback"> No puede dejar este campo vacio.</div></div></div>');
});
$(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      });
    });