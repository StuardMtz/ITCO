$('#sasucursal').select2({
    dropdownParent: $('#nuevaTransferencia')
});
$('#sucursal').select2({
    dropdownParent: $('#nuevaTransferencia'),
    placeholder: "Buscar unidad...",
    minimumInputLength: 2,
    multiple: false,
    tags: false,
    tokenSeparators: [",", " "],
});