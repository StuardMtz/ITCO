$('#noseriecom').select2({
    dropdownParent: $('#nuevaTransferencia'),
    placeholder: "Ingrese la serie de la factura...",
    minimumInputLength: 0,
    multiple: false,
    tags: false,
    tokenSeparators: [",", " "]
});