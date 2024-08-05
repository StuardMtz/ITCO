<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
//Vtrans

//Rutas para el control de los inventarios
Route::get('inv_pend',['as'=>'inventarios_pendientes','uses'=>'InventarioController@inventarios_pendientes_de_realizar']);//Muestra el listado de inventarios por realizar
Route::get('ver/{id}',['as'=>'ver_inventario','uses'=>'InventarioController@ver_inventario_completo']);//Permite ver el contenido del inventario realizado
Route::get('datos_inve/{id}',['as'=>'datos_inve','uses'=>'InventarioController@datos_de_inventario']);//Carga los datos del inventario
Route::patch('ac_inv/{id}',['as'=>'actualizar_inventario','uses'=>'InventarioController@actualizar_inventario']);//Guarda los cambios realizados en los inventarios
Route::get('fin/{id}',['as'=>'finalizar_inventario','uses'=>'InventarioController@finalizar_inventario']);//Permite finalizar un inventario, para evitar modificaciones futuras
Route::get('prod_invent/{id}',['as'=>'productos_inventario','uses'=>'InventarioController@productos_del_inventario']);//Permite ver las categorias de los productos
Route::get('listaprod_inve/{id}',['as'=>'listaprod_inve','uses'=>'InventarioController@listado_de_productos']);
Route::get('porce_inve/{id}',['as'=>'porce_inve','uses'=>'InventarioController@porcentaje_inventario']);
Route::get('pro/{id}',['as'=>'resultado_busqueda','uses'=>'InventarioController@busquedas_inventario']);//Permite ver las categorias de los productos
Route::get('resul_bus/{id}/{categoria}',['as'=>'datos_resultado_busqueda','uses'=>'InventarioController@datos_busquedas']);//
Route::get('agrexisin/{id}',['as'=>'agregar_existencia_inventario','uses'=>'InventarioController@agregar_existencia_vista']);//Abre la ventana para agregar existencia
Route::put('gu_expro',['as'=>'guardar_existencia_producto','uses'=>'InventarioController@guardar_existencia_producto']);//Guarda los datos de la existencia de un producto
Route::get('cero/{encabezado}',['as'=>'existencia_cero','uses'=>'InventarioController@productos_cero']);//Permite ver las categorias de los productos
Route::get('datos_cero/{encabezado}',['as'=>'datos_existencia_cero','uses'=>'InventarioController@datos_inventario_cero']);//Carga los datos de los productos con existencia cero
Route::get('fina',['as'=>'finalizados','uses'=>'InventarioController@finalizados']);
Route::get('datos_fina',['as'=>'datos_inventarios_finalizados','uses'=>'InventarioController@datos_finalizados']);//Carga los datos de los inventarios finalizados por los supervisores
Route::get('repinv',['as'=>'reporte_inventarios','uses'=>'InventarioController@reporte_inventarios']);
Route::get('repdat',['as'=>'datos_reporte_inventario','uses'=>'InventarioController@datos_reporte_inventarios']);//Carga los datos de tofdos los inventarios
//Reporte general de inventarios filtrado por fechas
Route::get('repinvf',['as'=>'reporte_inventarios_f','uses'=>'InventarioController@reporte_inventarios_fecha']);
Route::get('repdatf/{inicio}/{fin}',['as'=>'datos_reporte_inventario_f','uses'=>'InventarioController@datos_reporte_inventarios_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
Route::get('crear_i',['as'=>'crear_inventario','uses'=>'InventarioController@crear_inventario']);//Permite crear un nuevo inventario
Route::get('eli_inv/{id}',['as'=>'eliminar_inventario','uses'=>'InventarioController@eliminar_inventario']);//Permite eliminar inventario
Route::post('guarinvgen',['as'=>'guardar_inventGeneral','uses'=>'InventarioController@guardar_inventario_general']);//Guarda los datos del encabezado de un nuevo inventario
Route::get('carprod/{id}',['as'=>'cargar_productos','uses'=>'InventarioController@cargar_productos_en_inventario']);//Funcion que permite cargar las existencias de los productos
Route::get('list_suc',['as'=>'listado_de_sucursales','uses'=>'InventarioController@listado_reporte_inventarios']);//Ruta para ver el listado de sucursales existentes
Route::get('dat_rep',['as'=>'datos_reporte_inventarios','uses'=>'InventarioController@datos_listado_de_usuarios_y_sucursales']);//Carga las sucursales para los usuarios supervisores
Route::get('invreal/{id}',['as'=>'inventarios_realizados','uses'=>'InventarioController@inve_por_sucursal']);//
Route::get('datinvereal/{id}',['as'=>'datos_inventarios_realizados','uses'=>'InventarioController@inventario_por_sucursal']);//Carga los inventarios por sucursal para los supervisores
Route::get('busproinve',['as'=>'bus_pro_inve','uses'=>'InventarioController@buscar_producto_inventario']);//
Route::get('ultconinve/{id}',['as'=>'ulti_con_inve','uses'=>'InventarioController@ultima_vez_contado_en_inventario']);
Route::get('invepfe/{id}',['as'=>'inventarios_por_fecha','uses'=>'InventarioController@vista_inventarios_por_fecha']);//Muestra la vista de los inventarios por fecha de los supervisores
Route::get('datinfec/{id}/{inicio}/{fin}',['as'=>'datos_inventarios_pfecha','uses'=>'InventarioController@datos_inventarios_fecha']);//Carga los inventarios por fecha para los supervisores
Route::get('dat_inve/{id}',['as'=>'datos_inventario_completo','uses'=>'InventarioController@inventario_datos']);//Permite ver los productos para realizar un inventario
Route::get('det_pro/{id}',['as'=>'det_produc','uses'=>'InventarioController@detalles_producto_inventario']);
Route::get('his_prod/{id}',['as'=>'his_produc','uses'=>'InventarioController@historial_conteo_producto']);
Route::get('invefech',['as'=>'inventarios_por_fecha_general','uses'=>'InventarioController@inventarios_por_fecha_general']);//Muestra todos los inventarios realizados en una fecha
Route::get('dainvefech/{inicio}/{fin}',['as'=>'datos_inventarios_fecha_g','uses'=>'InventarioController@datos_inventario_fecha_general']);//Carga los inventarios por fecha
//Imprimir inventarios en PDF
Route::get('pdf{id}',['as'=>'pdf','uses'=>'InventarioController@pdf']);
Route::get('pdf_dif/{id}',['as'=>'pdf_dif','uses'=>'InventarioController@pdf_diferencias']);
Route::get('pdf_dif_pos/{id}',['as'=>'pdf_dif_pos','uses'=>'InventarioController@pdf_diferencias_positivas']);
Route::get('pdf_dif_neg/{id}',['as'=>'pdf_dif_neg','uses'=>'InventarioController@pdf_diferencias_negativas']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Rutas para el control de entrega de productos por medio de los camiones
Route::get('entespe',['as'=>'entregas_en_espera','uses'=>'SolicitudesController@inicio']);//Ruta para mostrar solicitudes pendientes
Route::get('edisol/{id}',['as'=>'editar_solicitud','uses'=>'SolicitudesController@vista_editar_solicitud']);////Formulario para editar información de una solicitud
Route::patch('guaedsol/{id}',['as'=>'guardar_edit_solicitud','uses'=>'SolicitudesController@editar_solicitud']);//Envía una información para editar una solicitud
Route::get('solrut',['as'=>'solicitudes_en_ruta','uses'=>'SolicitudesController@solicitudes_en_ruta']);//Listado de las solicitudes que se encuentran en ruta
Route::get('datsolrut',['as'=>'datos_solicitudes_rutas','uses'=>'SolicitudesController@datos_solicitudes_en_ruta']);//Datos de solicitudes
Route::get('solentre',['as'=>'solicitudes_entregadas','uses'=>'SolicitudesController@solicitudes_entregadas']);//Listado de las  solicitudes entregadas
Route::get('datsolentre',['as'=>'datos_solicitudes_entregadas','uses'=>'SolicitudesController@datos_solicitudes_entregadas']);//Carga datos de las entregas realizadas
Route::get('solentrefe',['as'=>'solicitudes_entregadas_fecha','uses'=>'SolicitudesController@solicitudes_entregadas_fecha']);//Muestra las entregas por rango de fecha
Route::get('dsoentf/{inicio}/{fin}',['as'=>'datos_solicitudes_entregadas_fecha','uses'=>'SolicitudesController@datos_solicitudes_entregadas_fecha']);//carga los datos de las solicitudes
Route::get('vcitud/{id}',['as'=>'ver_solicitud','uses'=>'SolicitudesController@ver_solicitud']);//Vista con la información de una solicitud
Route::get('visclie',['as'=>'vista_clientes','uses'=>'SolicitudesController@vista_clientes']);// Vista listado de clientes
Route::get('datclie',['as'=>'datos_listado_clientes','uses'=>'SolicitudesController@datos_listado_de_clientes']);//Listado de clientes existentes
Route::get('visedcli/{id}',['as'=>'vista_editar_cliente','uses'=>'SolicitudesController@vista_editar_cliente']);//Formularo de edición de cliente
Route::post('ediclien/{id}',['as'=>'guardar_edicion_cliente','uses'=>'SolicitudesController@guardar_edicion_del_cliente']);//Envia datos de edición de cliente
Route::get('nuesol/{id}',['as'=>'nueva_solicitud','uses'=>'SolicitudesController@generar_guardar_solicitud']);//Formulario para crear una nueva solicitud
Route::post('g_solicitud/{id}',['as'=>'guardar_solicitud','uses'=>'SolicitudesController@guardar_nueva_solicitud']);//Envía datos de una solicitud para guardarlos
Route::get('n_cliente',['as'=>'crear_nuevo_cliente','uses'=>'SolicitudesController@nuevo_cliente']);//Formulario nuevo cliente
Route::post('g_cliente',['as'=>'g_cliente','uses'=>'SolicitudesController@guardar_datos_nuevo_cliente']);//Envía datos del cliente para guardar
Route::get('e_p_cliente/{id}',['as'=>'ver_entregas_clientes','uses'=>'SolicitudesController@entregas_por_cliente']);//Listado de entregas por cliente
Route::get('total_entre/{id}',['as'=>'total_entregas','uses'=>'SolicitudesController@total_entregas']);//Devuelve el listado del total de entregas por cliente
Route::get('d_e_p_cliente/{id}',['as'=>'d_e_p_cliente','uses'=>'SolicitudesController@datos_entregas_por_cliente']);//entregas realizadas a un mismo cliente
Route::get('elientre/{id}',['as'=>'eliminar_entrega','uses'=>'SolicitudesController@eliminar_entrega_en_espera']);//permite finalizar una entrega en estado de espera
Route::get('v_e_ruta/{id}',['as'=>'v_e_ruta','uses'=>'SolicitudesController@vista_editar_ruta']);//Muestra la vista para editar una ruta
Route::post('a_fecha/{id}',['as'=>'actualizar_fecha','uses'=>'SolicitudesController@agregar_fecha']);//Permite agregar la fecha a las rutas
Route::get('nrut/{id}',['as'=>'nueva_ruta','uses'=>'SolicitudesController@crear_nueva_ruta']);//Mustra la vista para crear una nueva ruta
Route::get('e_p_camion/{id}',['as'=>'entregas_por_camion','uses'=>'SolicitudesController@entregas_por_camion']);//Muestra las entregas que a realizado un camión
Route::get('d_p_camion/{id}',['as'=>'datos_entregas_por_camion','uses'=>'SolicitudesController@datos_por_camion']);//Muestra las entregas que a realizado un camión
Route::post('g_camion',['as'=>'g_camion','uses'=>'SolicitudesController@guardar_camion']);//Envía los datos del formulario para poder guardar un nuevo camión
Route::get('v_e_camion/{id}',['as'=>'v_e_camion','uses'=>'SolicitudesController@vista_editar_camion']);//Muestra el formulario para editar datos de un camión
Route::post('e_camion/{id}',['as'=>'editar_datos_camion','uses'=>'SolicitudesController@editar_camion']);//Envía los datos para editar la información de un camión
Route::get('v_ruta/{id}',['as'=>'v_ruta','uses'=>'SolicitudesController@ver_entregas_de_ruta']);//Muestra las entregas que contiene una ruta
Route::get('f_ruta/{id}',['as'=>'finalizar_ruta','uses'=>'SolicitudesController@finalizar_ruta_y_entregas']);// Permite marcar como finalizada una ruta
Route::get('can_sol/{id}',['as'=>'cancelar_solicitud','uses'=>'SolicitudesController@cancelar_solicitud']);//Vista para la edición de solicitud de entregas
Route::post('g_can_sol/{id}',['as'=>'guardar_cambios_solicitud','uses'=>'SolicitudesController@g_cancelar_solicitud']);//Guarda los cambios realiados a una entrega
Route::get('bit/{id}',['as'=>'bit','uses'=>'BitacoraController@historial']);//Muestra el historial de eventos de cada entrega realizada por un piloto
Route::get('lissuc',['as'=>'listado_sucursales_entregas','uses'=>'SolicitudesController@listado_de_sucursales_entregas']);//Muestra la vista con las entregas de otras sucursales
Route::get('datlissuc',['as'=>'datos_listado_sucursales_entregas','uses'=>'SolicitudesController@datos_listado_de_sucursales']);//Carga los datos de las sucursales
Route::get('entrdsuc/{sucursal}',['as'=>'entregas_de_sucursal','uses'=>'SolicitudesController@entregas_dentro_de_la_sucursal']);//Muestra las entregas actuales dentro de la sucursal
//Rutas para los reportes de solicitudes y entregas por sucursal //lisrep
Route::get('lisrep',['as'=>'listado_sucursales_reporte','uses'=>'SolicitudesController@todas_las_sucursales_reporte']);//Carga el listado de sucursales para el reporte de entregas
Route::get('datlisrep',['as'=>'datos_listado_sucursales_reporte','uses'=>'SolicitudesController@datos_listado_de_sucursales_reporete']);//Carga los datos de las sucursales
Route::get('repentresuc/{id}',['as'=>'reporte_entregas_sucusal','uses'=>'SolicitudesController@entregas_realizadas_o_solicitadas']);//Todas las entregas dentro de una sucursal
Route::get('drepetresuc/{id}',['as'=>'datos_reporte_entregas_sucursal','uses'=>'SolicitudesController@datos_entregas_realizadas_o_solicitadas']);//Datos de entregas
Route::get('mapsuc/{id}',['as'=>'mapa_sucursal','uses'=>'SolicitudesController@mapa_entregas_por_sucursal']);//Muestra el mapa con las entregas por sucursal
Route::get('mapcam/{id}',['as'=>'mapa_entregas_por_camion','uses'=>'SolicitudesController@mapa_entregas_por_camion']);//Muestra el mapa con las entregas realizadas por camion
Route::get('entresufec/{id}',['as'=>'reporte_entregas_sucursal_fecha','uses'=>'SolicitudesController@reporte_entregas_sucursal_fecha']);//Carga las entregas de una sucursal por fecha
Route::get('daentresufe/{id}/{inicio}/{fin}',['as'=>'datos_reporte_entregas_fecha','uses'=>'SolicitudesController@datos_reporte_entregas_sucursal_fecha']);
Route::get('mapsucfech/{id}/{inicio}/{fin}',['as'=>'mapa_sucursal_fecha','uses'=>'SolicitudesController@mapa_entregas_por_sucursal_fecha']);//Muestra las entregas por fecha en sucursal
Route::get('mapcamfech/{id}/{inicio}/{fin}',['as'=>'mapa_camion_fecha','uses'=>'SolicitudesController@mapa_entregas_por_camion_fecha']);//Muestra las entregas por fecha camion
Route::get('entre_muni',['as'=>'entregas_por_municipio','uses'=>'SolicitudesController@entrega_por_municipios']);//Carga la vista para las entregas por cada municipio
Route::get('daenmuni',['as'=>'datos_entregas_por_municipio','uses'=>'SolicitudesController@datos_entrega_por_municipios']);//Carga los datos de las entregas entre municipios
Route::get('fentre_muni',['as'=>'f_entre_muni','uses'=>'SolicitudesController@entregas_por_municipios_fecha']);//Muestra la vista de las entregas por fecha de municipios
Route::get('dfemuni/{inicio}/{fin}',['as'=>'d_fecha_muni','uses'=>'SolicitudesController@datos_entregas_municipios_fecha']);//Carga los datos de entregas por fecha
Route::get('repetmun/{id}',['as'=>'reporte_entregas_municipios','uses'=>'SolicitudesController@entregas_por_municipio_aldea']);//Reporte entrega municipios
Route::get('repentmu/{id}',['as'=>'datos_reporte_entregas_muni','uses'=>'SolicitudesController@datos_entrega_aldeas']);//Carga los datos de las entregas por aldeas
Route::get('repmunfec/{id}',['as'=>'reporte_entregas_fecha_municipios','uses'=>'SolicitudesController@entregas_por_municipio_aldea_fecha']);//Permite filtrar las solicitudes por orden de fecha
Route::get('darepmunfec/{id}/{inicio}/{fin}',['as'=>'datos_reporte_entregas_fecha_municipios','uses'=>'SolicitudesController@datos_entrega_aldeas_fecha']);
Route::get('marpa',['as'=>'marcadores_mapa','uses'=>'SolicitudesController@mapa_marcador_entregas']);//Muestra un mapa marcando los puntos donde se a realizado una entrega
Route::get('fmapa{inicio}/{fin}',['as'=>'marc_f_mapa','uses'=>'SolicitudesController@mapa_marcador_entregas_por_fecha']);//Muestra un mapa de puntos por fecha
Route::get('repgen',['as'=>'reporte_general_entregas','uses'=>'SolicitudesController@reporte_entregas_por_fecha_general']);//
Route::get('darepgen/{inicio}/{fin}',['as'=>'datos_reporte_general_entregas','uses'=>'SolicitudesController@datos_reporte_entregas_por_fecha']);
Route::get('map/{id}',['as'=>'mapa','uses'=>'SolicitudesController@ver_mapa']);//Muestra las ubicaciones de los diferentes eventos durante una entrega
Route::get('liscam',['as'=>'listado_camiones','uses'=>'SolicitudesController@listado_de_camiones']);//Carga el listado de camiones en sistegua
Route::get('dliscam',['as'=>'datos_listado_camiones','uses'=>'SolicitudesController@datos_listado_camiones']);//Datos del listado de camiones
Route::get('enpcam/{id}',['as'=>'entregas_x_camion','uses'=>'SolicitudesController@entregas_x_camion']);//Vista de las entregas realizadas por camión
Route::get('denpcam/{id}',['as'=>'datos_entregas_x_camion','uses'=>'SolicitudesController@datos_entregas_x_camion']);//Datos de todas las entregas realizadas por un camion
Route::get('fenpcam/{id}',['as'=>'entregas_x_camion_fecha','uses'=>'SolicitudesController@entregas_x_camion_fecha']);//Vista de las entregas realizadas filtradas por fecha
Route::get('dfendcam/{id}/{inicio}/{fin}',['as'=>'datos_entregas_x_camion_fecha','uses'=>'SolicitudesController@datos_entregas_x_camion_fecha']);//Datos entregas por fecha
//Función que permite anular una entrega a cliente ------------------------------------------------------------------------------------------------------------
Route::post('anulSol/{id}',['as'=>'anular_solicitud','uses'=>'SolicitudesController@anular_solicitud_de_entrega']);
//_____________________________________________________________________________________________________________________________________________________________
//Rutas para ver en la vista principal todas las rutas actuales y sus entregas correspondientes ---------------------------------------------------------------
Route::get('v_ruta',['as'=>'v_ruta_s','uses'=>'SolicitudesController@todas_las_rutas_modal']);//Ruta que carga tosas las rutas y camions disponbles
Route::get('v_det_ruta_/{id}',['as'=>'v_det_ruta_s','uses'=>'SolicitudesController@detalle_rutas']);//Ruta que carga todas las entregas por ruta
//_____________________________________________________________________________________________________________________________________________________________
//transferencias_usuarios_bodega
//Rutas para las funciones disponibles en las transferencias
Route::put('RePro',['as'=>'RePro','uses'=>'TransferenciaController@confirmar_producto_transferencia_sucursal']);//Permite editar la cantidad enviada a la sucursal
Route::get('AcTran/{id}',['as'=>'AcTran','uses'=>'TransferenciaController@eliminar_producto']);//Permite eliminar un producto de la transferencia
Route::get('initran',['as'=>'inicio_transferencias','uses'=>'TransferenciaController@inicio']);//muestra las transferencias creadas
Route::get('daini',['as'=>'datos_inicio','uses'=>'TransferenciaController@datos_transferencias']);//Carga los datos con las tranferencias realizadas
//Route::post('agreFac',['as'=>'agregar_factura','uses'=>'InventarioController@agregar_factura']);//Permite cargar datos de factura para transferencia
Route::get('EdTran/{id}',['as'=>'editar_cantidades','uses'=>'TransferenciaController@editar_transferencia']);//Permite editar los productos agregados a la transferencia
Route::get('proentra/{id}',['as'=>'productos_transferencia','uses'=>'TransferenciaController@productos_en_transferencia']);//Carga los productos de la transferencia
Route::get('detprotra',['as'=>'detalle_producto','uses'=>'TransferenciaController@detalle_producto_transferencia']);//Funcion que muestra los detalles de un producto
Route::put('guaprotra',['as'=>'guardar_pro_transferencia','uses'=>'TransferenciaController@guardar_producto_transferencia']);//Actualiza la cantidad a enviar en una transferencia
Route::get('eletra/{id}',['as'=>'eliminar_pro_transferencia','uses'=>'TransferenciaController@eliminar_producto_transferencia']);//Permite eliminar un producto de la transferencia
Route::post('agreproMa/{id}',['as'=>'agregar_pro_manual','uses'=>'TransferenciaController@agregar_producto_manual']);//Permite agregar un producto de forma manual a la trasnferencia
Route::get('agreMa',['as'=>'buscar_pro_manual','uses'=>'TransferenciaController@buscar_producto_manual']);//Permite buscar productos de forma manual
Route::get('histra/{id}',['as'=>'histo_transf','uses'=>'TransferenciaController@historial_de_transferencia']);//Carga el historial de una transferencia
Route::get('BTran',['as'=>'trans_bodega','uses'=>'TransferenciaController@transferencias_bodega']);//Muestra las transferencias que están en bodega
Route::get('DBTran',['as'=>'datos_trans_bodega','uses'=>'TransferenciaController@datos_transferencias_bodega']);//Carga los datos de las transferencias en bodega
Route::get('DeTran',['as'=>'despacho_transf','uses'=>'TransferenciaController@despacho_transferencias']);//Muestra las transferencias despachadas por bodega
Route::get('DDeTran',['as'=>'datos_despacho_transf','uses'=>'TransferenciaController@datos_transferencias_despacho']);//Carga los datos de las transferencias despachadas
Route::get('FTran',['as'=>'finalizadas_transf','uses'=>'TransferenciaController@transferencias_finalizadas']);//Muestra la vista de las transferencias finalizadas
Route::get('DFTran',['as'=>'datos_finalizadas_transf','uses'=>'TransferenciaController@datos_transferencias_finalizadas']);//Carga las transferencias finalizadas
Route::get('FTranFe',['as'=>'finalizadas_transf_fe','uses'=>'TransferenciaController@transferencias_finalizadas_fecha']);//Muestra las transerencias finalizadas filtradas por fecha
Route::get('DTranFe/{inicio}/{fin}',['as'=>'dat_final_transf','uses'=>'TransferenciaController@datos_transf_final_fecha']);//Datos de las transferencias finalizadas filtradas
Route::get('TranPla',['as'=>'trans_ot_sucursales','uses'=>'TransferenciaController@transferencias_de_otras_sucursales']);//Ruta para la vista de las transferencias creadas por planta
Route::get('DTranPla',['as'=>'dat_trans_sucursales','uses'=>'TransferenciaController@datos_transferencias_de_otras_sucursales']);//Carga los datos de las transferencias creadas por planta
Route::get('TranOSucf',['as'=>'tran_otr_sucurf','uses'=>'TransferenciaController@transferencias_otras_sucur_fecha']);//Muestra las transferencias a otras sucursales por fecha
Route::get('DTranOsucf/{inicio}/{fin}',['as'=>'dat_trans_otro_sucf','uses'=>'TransferenciaController@transferencias_otras_sucursales_fecha']);//Datos transferencias otras sucursales fechas
Route::get('nueTran/{id}',['as'=>'agre_datos_transf','uses'=>'TransferenciaController@nueva_transferencia']);//Permite agregar producto a una nueva transferencia
Route::get('DnueTra/{id}',['as'=>'dat_transf_agre','uses'=>'TransferenciaController@datos_nueva_transferencia']);//Obtiene los datos de los productos de la transferencia
Route::get('agrTran/{id}',['as'=>'agre_pro_transf','uses'=>'TransferenciaController@agregar_producto_transferencia']);//Permite actualizar los datos de una transferencia
Route::get('VTranPla/{id}',['as'=>'VTranPla','uses'=>'TransferenciaController@ver_transferencia_planta']);//Muestra los detalles de una transferencia realizada por planta
Route::get('ESTran/{id}',['as'=>'verficar_trans','uses'=>'TransferenciaController@verificar_transferencia']);//Muestra la vista para editar una transferencia supervisor
Route::get('ESTrand/{cd}',['as'=>'verficar_transdd','uses'=>'TransferenciaController@verificar_transferencia']);//Muestra la vista para editar una transferencia supervisor
Route::get('LisPro/{id}',['as'=>'dat_ver_transf','uses'=>'TransferenciaController@listado_en_transferencia']);
Route::put('EdPro',['as'=>'edt_veri_transf','uses'=>'TransferenciaController@confirmar_producto_transferencia']);//Permite editar la cantidad enviada al supervisor
Route::get('buscar',['as'=>'info_ver_transf','uses'=>'TransferenciaController@detalles_del_codigo']);
//Route::get('DeBTran/{codigo}/{id}/{fecha}/{bodega}',['as'=>'DeBTran','uses'=>'TransferenciaController@detalles_transferencias_bodega']);//Muestra los detalles de las transferencias
Route::post('GRTran/{id}',['as'=>'guardar_revision_transf','uses'=>'TransferenciaController@guardar_revision_transferencia']);//Guarda los datos de la revisón del supervisor
Route::get('num_placa',['as'=>'num_placa','uses'=>'TransferenciaController@placas']);//Permite agregar el número de placa a la transferencia
Route::get('VeTran/{id}',['as'=>'VeTran','uses'=>'TransferenciaController@ver_transferencia']);//Permite ver una transferencia
Route::get('anuTrans/{id}',['as'=>'anultransf','uses'=>'TransferenciaController@anular_transferencia']);
Route::post('edencdes/{id}',['as'=>'edit_enca_desp','uses'=>'TransferenciaController@editar_encabezado_transferencia_despachada']);
Route::get('v_tran/{id}',['as'=>'validad_tranf','uses'=>'TransferenciaController@validar_transferencia']);//Ver contenido de transferencia
Route::post('g_ima_tran/{id}',['as'=>'g_imag_tran','uses'=>'TransferenciaController@guardar_imagen_transferencia']);//Guardar imagen de material en transfencia
Route::get('img_trnas/{id}',['as'=>'imag_trans','uses'=>'TransferenciaController@imagenes_de_transferencia']);//Permite ver las imagenes subidas por material dañado
Route::post('edidesimg/{id}',['as'=>'edit_des_img','uses'=>'TransferenciaController@editar_descripcion_imagen']);//Permite modificar la descripción de una imagen
Route::get('rsuc/{id}',['as'=>'regre_sucursal','uses'=>'TransferenciaController@regresar_a_sucursal']);//Permite cambiar el estado de una transferencia
Route::post('Vtrans/{id}',['as'=>'revis_transf','uses'=>'TransferenciaController@revision_de_transferencia']);//Permite marcar de verificada una transferencia
Route::get('lstranmm',['as'=>'lis_transf_mm','uses'=>'TransferenciaController@listado_sucursales_minimo_maximo']);//Muestra el listado de sucursales para transferencias
Route::get('dlstranmm',['as'=>'dlis_transf_mm','uses'=>'TransferenciaController@datos_listado_sucursales_minmax']);//Carga los datos de las sucursales
Route::get('minimax/{sucursal}/{bodega}/{todo}',['as'=>'minimax','uses'=>'TransferenciaController@existencia_productos']);//Muestra las existencias de los productos
Route::get('dat_exist/{sucursal}/{bodega}',['as'=>'dat_exist','uses'=>'TransferenciaController@datos_existencia']);//Carga la existencia dentro de la sucursal
Route::get('dat_exist_min/{sucursal}/{bodega}',['as'=>'dat_exist_min','uses'=>'TransferenciaController@datos_existencia_minimos']);//Carga la existencia abajo del minimo
Route::get('dat_exist_max/{sucursal}/{bodega}',['as'=>'dat_exist_max','uses'=>'TransferenciaController@datos_existencia_maximos']);//Carga la existencia abajo del reorden y arriba del minimo
Route::get('graficado/{sucursal}/{bodega}/{producto}',['as'=>'graficado','uses'=>'TransferenciaController@graficado']);
Route::get('bMaxmin/{sucursal}/{bodega}',['as'=>'bMaxmin','uses'=>'TransferenciaController@existencia_productos_categoria']);//Muestra la vista con los maximos y minimos por categoria
Route::get('dbMaxmin/{sucursal}/{bodega}/{cate}',['as'=>'dbMaxmin','uses'=>'TransferenciaController@datos_categoria']);//Carga los datos de la busqueda por categoria
Route::post('TransN/{sucursal}/{bodega}',['as'=>'TransN','uses'=>'TransferenciaController@crear_transferencia']);//Permite crear una nueva transferencia
Route::get('busq_exi',['as'=>'busq_exi','uses'=>'SemanaController@busqueda_existencias']);//Permite realizar busquedas dentro de las existencias
Route::get('PTran/{id}',['as'=>'PTran','uses'=>'TransferenciaController@imprimir_pdf']);//Permite generar un documento en pdf
Route::get('PTranGru/{id}',['as'=>'PTranGru','uses'=>'TransferenciaController@imprimir_pdf_grupo']);//Permite generar un documento en pdf para un grupo de transferencias
Route::get('lut',['as'=>'list_us_transf','uses'=>'TransferenciaController@listado_usuarios_transferencias']);//Muestra el listado de usuarios para transferencias
Route::post('gnug',['as'=>'guar_nue_us','uses'=>'TransferenciaController@guardar_nuevo_usuario_grupo']);//Permite agregar un nuevo usuario al grupo de carga
Route::post('edus/{id}',['as'=>'edi_usu_grup','uses'=>'TransferenciaController@editar_usuario_grupo']);//Permite modificar la informacion de un integrante del grupo
Route::get('btrans',['as'=>'bod_trasf_bod','uses'=>'TransferenciaController@transferencias_usuarios_bodega']);//Funcion que permite ver las transferencias a usuarios de bodega
Route::get('dbtrans',['as'=>'dabod_transf_bod','uses'=>'TransferenciaController@datos_transferencias_usuarios_bodega']);//Datos para los usuarios de bodega
Route::get('edbodtr/{id}',['as'=>'ed_bod_trans','uses'=>'TransferenciaController@editar_transferencia_en_bodega']);//Permite editar la transferencia a los usuarios de bodega
Route::post('editransbod/{id}',['as'=>'edit_transf_bod','uses'=>'TransferenciaController@editar_estado_de_transferencia_en_bodega']);//Permite a usuarios de bodega modificar estado
Route::get('PTranE/{id}',['as'=>'PTranE','uses'=>'TransferenciaController@productos_transferencia']);//Permite cargar los productos que seran editados
Route::get('tranAnul',['as'=>'tranAnulad','uses'=>'TransferenciaController@transferencias_anuladas']);//
Route::get('dtranAul',['as'=>'dtranAnulad','uses'=>'TransferenciaController@datos_transferencias_anuladas']);
//Permite programar una transferencia por medio del botón programar en la vista de transferencias en cola
Route::get('protrans/{id}',['as'=>'prog_trans','uses'=>'TransferenciaController@programar_transferencia']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Permite ver el listadod de transferencias de exportacion colocadas en bodega
Route::get('bodexpor',['as'=>'bod_expor','uses'=>'TransferenciaController@bodega_exportaciones']);
Route::get('dbodexpor',['as'=>'da_bod_expo','uses'=>'TransferenciaController@datos_bodega_exportacion']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Permite ver las transferencias de exportacion colocadas en despachadas
Route::get('desexpor',['as'=>'desp_expo','uses'=>'TransferenciaController@despachadas_exportacion']);
Route::get('ddesexpor',['as'=>'ddesp_expo','uses'=>'TransferenciaController@datos_despachadas_exportacion']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Permite ver las transferencias de exportación que han sido finalizadas
Route::get('finexpor',['as'=>'fina_expor','uses'=>'TransferenciaController@exportaciones_finalizadas']);
Route::get('dfinexpor',['as'=>'da_fina_expor','uses'=>'TransferenciaController@datos_exportaciones_finalizadas']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Permite ver las transferencias de exportación finalizadas filtradas por un rango de fecha
Route::get('ffinexpor',['as'=>'ffina_expor','uses'=>'TransferenciaController@exportaciones_finalizadas_fecha']);
Route::get('fdfinexpor/{inicio}/{fin}',['as'=>'fda_fina_expor','uses'=>'TransferenciaController@datos_exportaciones_finalizadas_fecha']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Permite ver el listado de todas las transferencias IW en el sistema web -------------------------------------------------------------------------------------
Route::get('RepTran',['as'=>'rep_trans','uses'=>'TransferenciaController@reporte_transferencias']);
Route::get('DRepTran',['as'=>'datos_rep_trans','uses'=>'TransferenciaController@datos_reporte_transferencias']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Permite ver el listado de todas las transferencias IW en el sistema web filtradas por fecha
Route::get('RepTranF',['as'=>'rep_trans_f','uses'=>'TransferenciaController@reporte_transferencias_fecha']);
Route::get('DRepTranF/{inicio}/{fin}',['as'=>'datos_rep_trans_f','uses'=>'TransferenciaController@datos_reporte_transferencias_fecha']);
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//Reporte de las transferencias
Route::get('verPro',['as'=>'buscar','uses'=>'TransferenciaController@ver_producto']);//Carga los detalles de los productos para editarlos en la transferencia
Route::get('rtrans',['as'=>'rep_verf_transf','uses'=>'ReporteTransferenciaController@reporte_verificadores']);//Ruta para la vista de reporte de eficacia por verificador
Route::get('drtrans/{inicio}/{fin}',['as'=>'drep_verf_transf','uses'=>'ReporteTransferenciaController@datos_reporte_verificadores']);//Ruta que carga los datos del reporte por verificador
Route::get('rgtrans',['as'=>'rep_tiem_group_transf','uses'=>'ReporteTransferenciaController@reporte_tiempos_grupos']);//Muestra la vista con los tiempos por transferencia por grupos
Route::get('drgtrans/{inicio}/{fin}',['as'=>'drep_tiem_group_transf','uses'=>'ReporteTransferenciaController@datos_reporte_tiempos_grupos']);//Carga los datos de los tiempos por grupos
Route::get('rstrans',['as'=>'rstrans','uses'=>'ReporteTransferenciaController@reporte_sucursales_transferencias']);//Muestra la vista con el listado de sucursales
Route::get('dasurep/{inicio}/{fin}',['as'=>'dat_suc_rep','uses'=>'ReporteTransferenciaController@datos_sucursales_reporte_transferencias']);
Route::get('rvtrans/{veri}/{inicio}/{fin}',['as'=>'rvtrans','uses'=>'ReporteTransferenciaController@reportes_por_verificador']);//Muestra la vista con las transferencias revisadas por usuario
Route::get('drvtrans/{veri}/{inicio}/{fin}',['as'=>'drvtrans','uses'=>'ReporteTransferenciaController@datos_reportes_por_verificadores']);//Carga los datos de las transferencias por usuario
Route::get('rpgtrans/{gru}/{inicio}/{fin}',['as'=>'rpgtrans','uses'=>'ReporteTransferenciaController@reporte_por_grupo_tiempos']);//Muestra la vista con los tiempos de transferencia por grupo
Route::get('drpgtrans/{gru}/{inicio}/{fin}',['as'=>'drpgtrans','uses'=>'ReporteTransferenciaController@datos_reporte_por_grupo_tiempos']);//Carga los datos con los tiempos del grupo
Route::get('transps/{id}/{bodega}',['as'=>'transps','uses'=>'ReporteTransferenciaController@transferencias_por_sucursal']);//Vista con las transferencias por sucursal
Route::get('dtransps/{id}/{bodega}/{inicio}/{fin}',['as'=>'dtransps','uses'=>'ReporteTransferenciaController@datos_transferencias_por_sucursal']);//Carga los datos de la transferencia por sucursal
Route::get('vrtrans/{id}',['as'=>'vrtrans','uses'=>'ReporteTransferenciaController@ver_transferencia']);//Muestra la vista de una transferencia dentro de los reportes
//Rutas para las funciones de minimo y máximo
Route::get('mimag',['as'=>'mimageneral','uses'=>'GraficaController@minimo_maximo_general']);//Carga el mínimo y máximo de todas las sucursales
Route::get('dmimag',['as'=>'dmimageneral','uses'=>'GraficaController@datos_minimo_maximo_general']);//Carga los datos del minimo y maximo de todas las sucursales
Route::get('exister',['as'=>'exis_terminado','uses'=>'GraficaController@existencia_terminado']);//Muestra la existencia de la bodega de terminado de planta
Route::get('repsulis',['as'=>'rep_suc_lis','uses'=>'GraficaController@listado_de_sucursales']);//Carga el listado de sucursales
Route::get('drepsulis',['as'=>'drep_suc_lis','uses'=>'GraficaController@datos_listado_sucursales']);//Funcion para cargar los datos de las sucursales para minimax
Route::get('repminimax/{sucursal}/{bodega}/{todo}',['as'=>'repminimax','uses'=>'GraficaController@existencia_productos']);//Muestra las existencias de los productos
Route::get('rdat_exist/{sucursal}/{bodega}',['as'=>'rdat_exist','uses'=>'GraficaController@datos_existencia']);//Carga la existencia dentro de la sucursal
Route::get('rdat_exist_min/{sucursal}/{bodega}',['as'=>'rdat_exist_min','uses'=>'GraficaController@datos_existencia_minimos']);//Carga la existencia abajo del minimo
Route::get('rdat_exist_max/{sucursal}/{bodega}',['as'=>'rdat_exist_max','uses'=>'GraficaController@datos_existencia_maximos']);//Carga la existencia abajo del reorden y arriba del minimo
Route::get('rgraficado/{sucursal}/{bodega}/{producto}',['as'=>'rgraficado','uses'=>'GraficaController@graficado']);
Route::get('rbMaxmin/{sucursal}/{bodega}',['as'=>'rbMaxmin','uses'=>'GraficaController@existencia_productos_categoria']);//Muestra la vista con los maximos y minimos por categoria
Route::get('rdbMaxmin/{sucursal}/{bodega}/{cate}',['as'=>'rdbMaxmin','uses'=>'GraficaController@datos_categoria']);//Carga los datos de la busqueda por categoria
Route::get('gClaSuc/{sucursal}/{bodega}',['as'=>'grafClasSuc','uses'=>'GraficaController@grafica_sucursal_clase']);//Grafica de clase por cada sucursal
Route::get('grafClasSucPro/{sucursal}/{bodega}/{clase}',['as'=>'grafClasSucPro','uses'=>'GraficaController@grafica_sucursal_clase_producto']);//Graficas por clases
Route::get('grafClasSucHis/{sucursal}/{bodega}/{clase}',['as'=>'grafClasSucHis','uses'=>'GraficaController@grafica_sucursal_clase_historial']);//Grafica por clase por mes
Route::get('grafClasPsuc/{clase}',['as'=>'grafClasPSuc','uses'=>'GraficaController@Grafica_clases_todas_sucursales']);//Graficas de clases por cada sucursal
Route::get('grafClasTSucPro/{sucursal}/{bodega}/{clase}',['as'=>'grafClasTSucPro','uses'=>'GraficaController@sucursal_clase_producto']);//Carga los datos para la tabla con los productos por clase
Route::get('grafClasSucR',['as'=>'grafClasSucR','uses'=>'GraficaController@grafica_sucursal_clase_random']);//Grafica de clases por sucursal random
Route::get('grafClasSucProRa/{sucursal}/{bodega}/{clase}',['as'=>'grafClasSucProRa','uses'=>'GraficaController@grafica_sucursal_clase_producto_random']);//Graficas por clases random
Route::get('ingreda',['as'=>'ingre_datos','uses'=>'GraficaController@ingresar_datos']);//Ruta para abrir el formulario para el reporte de historial de producto
Route::get('v_ge',['as'=>'v_ge','uses'=>'GraficaController@historial_producto']);//Reporte de historial de existencias por producto
Route::get('producto',['as'=>'producto','uses'=>'GraficaController@producto']);//carga el listado de productos en el select2
Route::get('ingre_dat',['as'=>'ingre_dat','uses'=>'GraficaController@ingresar_datos_sucursal']);//Formulario para ingresar datos para historial por categorias de productos
Route::get('pro_suc',['as'=>'pro_suc','uses'=>'GraficaController@productos_sucursal']);
//Rutas para panel de funciones administrativas de la aplicación
Route::get('iniad',['as'=>'inicio_adm','uses'=>'SemanaController@inicio_administracion']);
Route::get('agr_pro/{semana}',['as'=>'agr_produc','uses'=>'SemanaController@agregar_producto']);
Route::get('prod_semana/{semana}',['as'=>'prod_semana','uses'=>'SemanaController@productos_semana']);//carga los productos de la semana que se visualiza
Route::post('agre_prod/{semana}',['as'=>'agre_prod','uses'=>'SemanaController@semana_producto']);//Permite guardar un nuevo producto a la semana
Route::get('prod_faltante',['as'=>'prod_faltante','uses'=>'SemanaController@productos']);//Permite agregar nuevos productos a la semana
Route::get('actu_se/{id}',['as'=>'actu_se','uses'=>'SemanaController@vista_actualizar_semana']);
Route::post('ac_se/{id}',['as'=>'act_sem','uses'=>'SemanaController@actualizar_semana']);
Route::get('ivs/{id?}',['as'=>'ivs','uses'=>'SemanaController@inventario_sucursales']);
Route::get('cre_se',['as'=>'cre_sem','uses'=>'SemanaController@crear_semana']);
Route::post('g_se',['as'=>'guar_sem','uses'=>'SemanaController@guardar_semana']);
Route::get('lis_us',['as'=>'lis_us','uses'=>'SemanaController@listado_usuarios']);//Muestra el listado de usuarios registrados dentro de la aplicación
Route::get('agu',['as'=>'agu','uses'=>'SemanaController@agregar_usuario']);
Route::get('perus',['as'=>'perm_usu','uses'=>'SemanaController@permisos_usuario']);
Route::post('cru',['as'=>'crear_usuario','uses'=>'SemanaController@crear_usuario']);
Route::get('edius/{id}',['as'=>'edius','uses'=>'SemanaController@vista_editar_usuario']);
Route::post('editus/{id}',['as'=>'editus','uses'=>'SemanaController@editar_usuario']);
Route::get('histo/{id}',['as'=>'histo','uses'=>'SemanaController@historial']);// Muestra la actividad de cada usuario dentro de la aplicación //////////////////////
Route::get('histofe/{id}',['as'=>'histofe','uses'=>'SemanaController@historial_por_fecha']);//Muestra el historial de cada usuario ordenado por fecha
Route::get('v_pro',['as'=>'v_pro','uses'=>'SemanaController@vista_procesos']);//vista para modificar los nombres de las categorias y los productos en la base de datos
Route::get('cat_nom',['as'=>'cat_nom','uses'=>'SemanaController@categoria_nombre']);//Copia el nombre de las categorias a una nueva columna
Route::get('cat_mod',['as'=>'cat_mod','uses'=>'SemanaController@categoria_modificar']);//Elimina los caracteres especiales de los nombres de las categorias
Route::get('pro_nom',['as'=>'pro_nom','uses'=>'SemanaController@productos_nombre']);//Copia el nombre de los productos a una nueva columna
Route::get('pro_mod',['as'=>'pro_mod','uses'=>'Semana@productos_modificar']);//Elimina los caractes especiales a los nombres de los productos
Route::get('vg',['as'=>'vg','uses'=>'SemanaController@vista_carga']);
//Editar camiones
Route::get('edit_cam',['as'=>'edi_cam_adm','uses'=>'SemanaController@editar_camion_administracion']);
Route::get('v_a_camion',['as'=>'v_a_camion','uses'=>'SemanaController@vista_agregar_camion']);//Muestra el formulario para agregar un nuevo camión
Route::post('g_camion',['as'=>'g_camion','uses'=>'SemanaController@guardar_camion']);//Envía los datos del formulario para poder guardar un nuevo camión
Route::get('v_e_camion/{id}',['as'=>'v_e_camion','uses'=>'SemanaController@vista_editar_camion']);//Muestra el formulario para editar datos de un camión
Route::post('e_camion/{id}',['as'=>'editar_datos_camion','uses'=>'SemanaController@editar_camion']);//Envía los datos para editar la información de un camión
//num_placa
Route::get('bus/{id}',['as'=>'bus','uses'=>'InventarioController@busquedas']);//Permite ver las categorias de los productos
Route::get('dat_cd_inve/{encabezado}',['as'=>'dat_cd_inve','uses'=>'InventarioController@datos_inventario_cd']);//Carga los datos para el inventario de centro de distribucion
Route::get('SelSer',['as'=>'SelSer','uses'=>'TransferenciaController@seleccionar_serie']);
Route::get('agre_ex/{id}/{suma}',['as'=>'cat/agre_ex','uses'=>'InventarioController@agregar_existencia_vista']);//Abre la ventana para agregar existencia
Route::get('/select/{id}',['as'=>'select','uses'=>'InventarioController@seleccionar_bodega']);//Permite ver los productos contenidos en una categoria
Route::get('det/{id}/{cod}',['as'=>'det','uses'=>'InventarioController@detalles']);//Permite ver los detalles de un producto, dentro de la vista del inventario
Route::get('por_fe',['as'=>'por_fe','uses'=>'InventarioController@inve_por_fecha']);
Route::get('dat_porfe/{inicio}/{fin}',['as'=>'dat_porfe','uses'=>'InventarioController@datos_inventarios_por_fecha']);//Carga los datos de los iventarios por fecha
Route::get('imprimir/{id}',['as'=>'imprimir','uses'=>'InventarioController@imprimir']);

Route::get('inv_sup/{id}',['as'=>'inv_sup','uses'=>'InventarioController@inve_por_supervisor']);
Route::get('dat_inv_sup/{id}',['as'=>'dat_inv_sup','uses'=>'InventarioController@datos_por_supervisor']);//Permite cargar la información de inventarios de los supervisores
Route::get('por_fesu',['as'=>'por_fesu','uses'=>'InventarioController@inve_por_fecha_supervisor']);
Route::get('suc_in/{id}/{nombre_corto}/{categoria?}',['as'=>'suc_in','uses'=>'InventarioController@sucursal_inventario']);
Route::get('finven/{id}/{nombre_corto}/{categoria?}',['as'=>'finven','uses'=>'InventarioController@formulario_inventario']);//Permite agregar la existencia de un producto
Route::get('tran_su',['as'=>'tran_su','uses'=>'InventarioController@transferencias_pendientes']);//Vista con las transferencias en proceso para la sucursal
Route::get('da_tran_su',['as'=>'da_tran_su','uses'=>'InventarioController@datos_transferencias']);//Datos de las transferencias en proceso para la sucursal
Route::get('f_tran',['as'=>'f_tran','uses'=>'InventarioController@transferencias_finalizadas']);//Carga la vista para las transferencias finalizadas
Route::get('df_tran',['as'=>'df_tran','uses'=>'InventarioController@datos_transferencias_finalizadas']);//Carga los datos de las transferencias finalizadas
Route::post('ed_tran/{id}',['as'=>'ed_tran','uses'=>'InventarioController@editar_transferencia']);//Permite marcar de recibida una transferencia por la sucursal

Route::get('AgreTran/{id}',['as'=>'AgreTran','uses'=>'InventarioController@agregar_producto_transferencia']);//Permite agregar producto a una nueva transferencia

Route::get('actuTranIn/{id}',['as'=>'actuTranIn','uses'=>'InventarioController@actualizar_transferencia']);//Permite actualizar los datos de una transferencia
Route::get('ProAgre/{id}',['as'=>'ProAgre','uses'=>'InventarioController@productos_faltantes']);
/*/*-*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-/*-*/
//Controlador para CRUD de inventario
Route::get('actu/{id}',['as'=>'actu','uses'=>'Crud_InventarioController@actualizar']);
Route::post('agrePro/{id}',['as'=>'agrePro','uses'=>'Crud_InventarioController@agregar_faltante']);
//Rutas para modificar los nombres de los productos incompatibles con la aplicación
Route::get('cat_cod',['as'=>'cat_cod','uses'=>'InventarioController@categoria_codigo']);/*Copia el código de las categorias a los productos, pasa el código de la tabla tipos_prod a la tabla productos_inve
esto para que los productos puedan tener una relacion con las categorias por medio de un ID que une a cada producto con su categoria*/
//Controlador para inventarios semanales
Route::get('us',['as'=>'us','uses'=>'SemanaController@usuarios']);
Route::get('dat_us',['as'=>'dat_us','uses'=>'SemanaController@datos_usuarios']);//Carga los datos principales de los usuarios
Route::get('sucs',['as'=>'sucs','uses'=>'SemanaController@sucursales']);
Route::get('list_sucs',['as'=>'list_sucs','uses'=>'SemanaController@listado_de_sucursales']);
Route::get('grafica',['as'=>'grafica','uses'=>'SemanaController@grafica']);
Route::get('bu/{semana?}',['as'=>'bu','uses'=>'SemanaController@busqueda']);
Route::post('agf',['as'=>'agf','uses'=>'SemanaController@agregar_faltante']);
Route::post('cpro',['as'=>'cpro','uses'=>'SemanaController@cargar_productos']);
Route::get('bus_co',['as'=>'bus_co','uses'=>'SemanaController@buscar_codigo']);
Route::get('bua/{semana?}',['as'=>'bua','uses'=>'SemanaController@busqueda_agregados']);
Route::get('spro/{semana?}/{id?}',['as'=>'spro','uses'=>'SemanaController@agregar_prose']);
Route::get('ingre_gra',['as'=>'ingre_gra','uses'=>'SemanaController@ingresar_datos_grafica']);
Route::get('prod_inve',['as'=>'prod_inve','uses'=>'SemanaController@busqueda_productos_inventario']);//Permite buscar productos por categoria durante el inventario
Route::get('epro/{semana?}/{id?}',['as'=>'epro','uses'=>'SemanaController@eliminar_producto']);
Route::get('scpro/{cat?}/{semana?}',['as'=>'scpro','uses'=>'SemanaController@semana_categoria']);
Route::get('ver_d/{id}',['as'=>'ver_d','uses'=>'SemanaController@ver_productos_con_diferencias']);
Route::get('ecpro/{cat?}/{semana?}',['as'=>'ecpro','uses'=>'SemanaController@eliminar_categoria']);
Route::get('invps/{sucursal}/{bodega}',['as'=>'invps','uses'=>'SemanaController@inventarios_por_sucursal']);
Route::get('dat_invent/{sucursal}/{bodega}',['as'=>'dat_invent','uses'=>'SemanaController@datos_inventarios_sucursales']);//Carga los inventarios realizados por sucursal
Route::get('det_invent/{no_encabezado}/{cod_producto}',['as'=>'det_invent','uses'=>'InventarioController@detalle_inventarios']);//Carga los detalles de cada producto por ingreso
Route::get('ver_d_mas/{id}',['as'=>'ver_d_m','uses'=>'SemanaController@ver_productos_con_diferencias_positivas']);
Route::get('ver_d_menos/{id}',['as'=>'ver_dm','uses'=>'SemanaController@ver_productos_con_diferencias_negativas']);
Route::get('faltante',['as'=>'faltante','uses'=>'SemanaController@faltante']);
Route::get('catMaxMin',['as'=>'catMaxMin','uses'=>'SemanaController@busqueda_max_min']);//Permite buscar por categoria en los maximos y minimos
Route::get('sucursales',['as'=>'sucursales','uses'=>'SolicitudesController@datos_sucursales']);//Muestra Información de las sucursales///////////////////////////////////
Route::post('g_sucursal',['as'=>'g_sucursal','uses'=>'SolicitudesController@agregar_sucursal']);// Envía datos de la sucursal para guardar //////////////////////////////
Route::get('v_sucursales',['as'=>'v_sucursales','uses'=>'SolicitudesController@vista_sucursales']);//Listado de sucursales //////////////////////////////////////////////
Route::patch('e_sucursal/{id}',['as'=>'e_sucursal','uses'=>'SolicitudesController@editar_sucursal']);//Envía datos de edición de sucursal ///////////////////////////////
Route::post('g_n_entrega',['as'=>'g_n_entrega','uses'=>'SolicitudesController@guardar_nueva_entrega']);//Envía datos de entre entre sucursal a guardar //////////////////
Route::get('v_a_sucursal',['as'=>'v_a_sucursal','uses'=>'SolicitudesController@vista_agregar_sucursal']);//Formulario para agregar nueva sucursal ///////////////////////
Route::get('v_e_sucursal/{id}',['as'=>'v_e_sucursal','uses'=>'SolicitudesController@vista_editar_sucursal']);//Formulario para editar sucursal //////////////////////////
Route::get('b_f_entregas/{id?}',['as'=>'b_f_entregas','uses'=>'SolicitudesController@buscar_fecha_entrega']);//Permite filtrar las solicitudes por orden de fecha ///////
Route::get('b_e_entregas/{id?}',['as'=>'b_e_entregas','uses'=>'SolicitudesController@buscar_estados_entrega']);//Permite filtras las solicitudes por su estado //////////
Route::patch('e_e_sucursal/{id}',['as'=>'e_e_sucursal','uses'=>'SolicitudesController@editar_entrega_sucursal']);//Envía datos de entrega a sucursal para editar ////////
Route::get('v_en_sucursal/{id}',['as'=>'v_en_sucursal','uses'=>'SolicitudesController@vista_solicitud_sucursal']);////Formulario para solicitud de envio a sucursal /////
Route::get('b_c_entregas/{id?}',['as'=>'b_c_entregas','uses'=>'SolicitudesController@buscar_comprobante_entrega']);// Permite filtrar las solicitudes por comprobante ///
Route::get('v_ep_sucursal/{id}',['as'=>'v_ep_sucursal','uses'=>'SolicitudesController@vista_entregas_por_sucursal']);//Vista de entregar por cada sucursal //////////////
/////////////////////////////////////// Rutas para manejo de select /////////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/otros/{id}',['as'=>'otros','uses'=>'SolicitudesController@otros']);//Devuelve las aldeas, colonias u otros de un municipio o zona //////////////////////////
Route::get('nue_sol/otros/{id}',['as'=>'otros','uses'=>'SolicitudesController@otros']);//Devuelve las aldeas, colonias u otros de un municipio o zona ///////////////////
Route::get('/muni/{id}',['as'=>'muni','uses'=>'SolicitudesController@municipios']);//Devuelve los municipios de un departamento /////////////////////////////////////////
Route::get('nue_sol/muni/{id}',['as'=>'muni','uses'=>'SolicitudesController@municipios']);//Devuelve los municipios de un departamento //////////////////////////////////
Route::get('v_e_envio/otros/{id}',['as'=>'otros','uses'=>'SolicitudesController@otros']);//Devuelve las aldeas, colonias u otros de un municipio o zona /////////////////
Route::get('n_solicitud/otros/{id}',['as'=>'otros','uses'=>'SolicitudesController@otros']);////Devuelve las aldeas, colonias u otros de un municipio o zona /////////////
Route::get('v_e_cliente/otros/{id}',['as'=>'otros','uses'=>'SolicitudesController@otros']);////Devuelve las aldeas, colonias u otros de un municipio o zona /////////////
Route::get('v_e_sucursal/otros/{id}',['as'=>'otros','uses'=>'SolicitudesController@otros']);////Devuelve las aldeas, colonias u otros de un municipio o zona ////////////
Route::get('v_e_envio/muni/{id}',['as'=>'muni','uses'=>'SolicitudesController@municipios']);//Devuelve los municipios de un departamento ////////////////////////////////
Route::get('n_solicitud/muni/{id}',['as'=>'muni','uses'=>'SolicitudesController@municipios']);//Devuelve los municipios de un departamento //////////////////////////////
Route::get('v_e_cliente/muni/{id}',['as'=>'muni','uses'=>'SolicitudesController@municipios']);//Devuelve los municipios de un departamento //////////////////////////////
Route::get('v_e_sucursal/muni/{id}',['as'=>'muni','uses'=>'SolicitudesController@municipios']);//Devuelve los municipios de un departamento /////////////////////////////
/////////////////////////////////// Rutas para manejo de Solicitud de entregas //////////////////////////////////////////////////////////////////////////////////////////
Route::get('v_e_entrega/{id}',['as'=>'v_e_entrega','uses'=>'SolicitudesController@vista_editar_entrega']);//vista para editar solicitud a cliente ///////////////////////
Route::get('b_e_solicitud/{id?}',['as'=>'b_e_solicitud','uses'=>'SolicitudesController@buscar_estados_solicitud']);//Permite filtras las solicitudes por su estado //////
Route::get('b_c_solicitud/{id?}',['as'=>'b_c_solicitud','uses'=>'SolicitudesController@buscar_comprobante_solicitud']);// Permite filtrar las solicitudes por comprobante
Route::get('p_recibir',['as'=>'p_recibir','uses'=>'SolicitudesController@por_recibir']);//Envíos entre sucursales ///////////////////////////////////////////////////////
///////////////////////////////////////////////////////////Reporte entregas Roberto
Route::get('suclis',['as'=>'suclis','uses'=>'SolicitudesController@sucursales_listado']);//Muestra el listado de sucursales disponibles
Route::get('enpsu/{suc}/{bod}',['as'=>'enpsu','uses'=>'SolicitudesController@entregas_por_sucursal']);//Muestra la vista con las entregas por sucursal
Route::get('denpsu/{suc}/{bod}',['as'=>'denpsu','uses'=>'SolicitudesController@datos_entregas_por_sucursal']);//Carga los datos de las entregas por sucursal
//_____________________________________________________________________________________________________________________________________________________________
//Reporte General solicitudes ---------------------------------------------------------------------------------------------------------------------------------
Route::get('repgens',['as'=>'rep_gen','uses'=>'SolicitudesController@reporte_entregas_g']);
Route::get('drepgens',['as'=>'drep_gen','uses'=>'SolicitudesController@datos_reporte_entregas_g']);
//Reporte General solicitudes por fecha -----------------------------------------------------------------------------------------------------------------------
Route::get('repgenfs',['as'=>'rep_gen_ef','uses'=>'SolicitudesController@reporte_entregas_gf']);
Route::get('drepgenfs/{inicio}/{fin}',['as'=>'drep_gen_ef','uses'=>'SolicitudesController@datos_reporte_entregas_gf']);
//_____________________________________________________________________________________________________________________________________________________________

//Rutas Controllers
Route::get('r_inicio',['as'=>'r_inicio','uses'=>'RutaController@inicio']);//Muestra las solicitudes que no estan agregadas a una ruta ///////////////////////////////////
Route::get('dr_inicio',['as'=>'dr_inicio','uses'=>'RutaController@datos_inicio']);//Carga los datos para las rutas pendientes de asignar a ruta /////////////////////////
Route::get('ve_ruta/{id}',['as'=>'ve_ruta','uses'=>'RutaController@ver']);//Muestra las entregas que contiene una ruta //////////////////////////////////////////////////
Route::get('r_final',['as'=>'r_final','uses'=>'RutaController@finalizadas']);//Muestra las rutas marcadas como finalizadas //////////////////////////////////////////////
Route::get('c_ruta/{id}',['as'=>'c_ruta','uses'=>'RutaController@crear_ruta']);//Muestra la vista para poder crear una nueva ruta ///////////////////////////////////////
Route::get('busqueda/{id}',['as'=>'busqueda','uses'=>'RutaController@busqueda']);////////////////////////////////////////////////////////////////////////////////////////
Route::get('v_e_ruta/p/{id}',['as'=>'v_e_ruta/p','uses'=>'RutaController@ver_modal']);//Muestra la informacion de una entrega en un modal durante la edicion de ruta ////
Route::get('v_c_ruta/p/{id}',['as'=>'v_c_ruta/p','uses'=>'RutaController@ver_modal']);//Muestra la información de una entrega en un modal duarante la creacion de ruta //
Route::get('s_en_ruta',['as'=>'s_en_ruta','uses'=>'RutaController@solicitudes_en_ruta']);//Muestra las solicitudes que están agregadas a una ruta ///////////////////////
Route::get('d_s_e_ruta',['as'=>'d_s_e_ruta','uses'=>'RutaController@d_solicitudes_en_ruta']);//Carga las solicitudes en ruta para la sucursal que asigna las entregas ///
Route::get('r_s_finalizar',['as'=>'r_s_finalizar','uses'=>'RutaController@rutas_sin_finalizar']);//Muestra las rutas que aún no han sido finalizadas ////////////////////
Route::get('b_p_camion/{id?}',['as'=>'b_p_camion','uses'=>'RutaController@busqueda_por_camion']);//Muestra los resuldados de busqueda dentro de las entregas de un camion
Route::get('a_e_ruta/{id}/{ruta}',['as'=>'a_e_ruta','uses'=>'RutaController@agregar_envio_a_ruta']);//Permite agregar una entrega o envío a una ruta ////////////////////
Route::get('s_finalizadas',['as'=>'s_finalizadas','uses'=>'RutaController@solicitudes_finalizadas']);//Muestra las solicitudes y entregas que han sido finalizadas //////
Route::get('d_s_finalizadas',['as'=>'d_s_finalizadas','uses'=>'RutaController@d_solicitudes_finalizadas']);
Route::get('e_e_ruta/{id}/{ruta}',['as'=>'e_e_ruta','uses'=>'RutaController@eliminar_envio_de_ruta']);//Permite eliminar una entrega o envío de una ruta ////////////////
Route::get('r_can_sol/{id}',['as'=>'r_cancelar_solicitud','uses'=>'RutaRouteController@cancelar_solicitud']);//Vista para la edición de solicitud de entregas /////////////
//Piloto Controllers
Route::get('p_inicio',['as'=>'p_inicio','uses'=>'PilotoController@inicio']);//Muestra las rutas pendientes por realizar de un piloto ////////////////////////////////////
Route::get('v_en_ruta/p/{id}',['as'=>'v_en_ruta/p','uses'=>'RutaController@ver_modal']);//Muestra la informacion de una entrega en un modal durante la edicion de ruta //
Route::put('ed_entrega/{id}',['as'=>'ed_entrega','uses'=>'PilotoController@editar_entrega']);//Ruta para editar entrega por parte del piloto y crear bitacora ///////////
Route::get('v_en_ruta/{id}',['as'=>'v_en_ruta','uses'=>'PilotoController@vista_editar_entregas']);//Vista para editar las entregas en una ruta //////////////////////////
Route::get('v_ed_entrega/{id}',['as'=>'v_ed_entrega','uses'=>'PilotoController@vista_editar_entrega']);//Vista para editar una entrega asignada a una ruta //////////////
Route::get('v_final',['as'=>'v_final','uses'=>'PilotoController@vista_rutas_finalizadas']);//Perminte ver al piloto las rutas que han finalizado ////////////////////////
Route::get('f_r_pi/{id}',['as'=>'f_r_pi','uses'=>'PilotoController@finalizar_ruta']);//Pérmite a los pilotos finalizar las rutas que tienen asignadas ///////////////////
//Bitacora Controller
Route::get('entreAlde/{id}',['as'=>'entreAlde','uses'=>'BitacoraController@vista_entregas_por_aldea']);//Vista con las entregas por aldea o zona
Route::get('datAlde/{id}',['as'=>'datAlde','uses'=>'BitacoraController@todas_las_entregas_aldeas']);//Carga los datos de las entregas por aldea o zona
Route::get('detAlde/{id_cliente}/{id_otros}',['as'=>'detAlde','uses'=>'BitacoraController@detalles_entregas_por_aldeas']);//Carga los detalles de las entregas
Route::get('veEntre/{id}',['as'=>'veEntre','uses'=>'BitacoraController@ver_entrega']);//Permite ver el detalle de la entrega
Route::get('pClient',['as'=>'pClient','uses'=>'BitacoraController@por_cliente']);//Muestra todos los clientes ingresados al sistema
Route::get('enCli/{id}',['as'=>'enCli','uses'=>'BitacoraController@entregas_cliente']);//Muestra todas las entregas realizadas a un solo cliente
Route::get('pCami',['as'=>'pCami','uses'=>'BitacoraController@listado_camiones']);//Muestra el listado de camiones para entregas
Route::get('vEnCami/{id}',['as'=>'vEnCami','uses'=>'BitacoraController@entregas_por_camion']);//Muestra las entregas realizadas por camion
//Importar datos de Excel
Route::get('excel',['as'=>'excel','uses'=>'ExcelController@ImportarExcel']);//Ruta para la vista en la que se pueden cargar los archivos de excel ///////////////////////
Route::post('importar',['as'=>'importar_aldeas','uses'=>'ExcelController@ImportarAldeas']);//Ruta en la que se cargan las aldeas a la base de datos /////////////////////
Route::post('importar_m',['as'=>'importar_municipios','uses'=>'ExcelController@ImportarMunicipios']);// Ruta en la que se cargan los municipios a la base de datos //////
Route::post('importar_rs',['as'=>'importar_reportesat','uses'=>'ExcelController@ImportarReporteSat']);// Ruta en la que se cargan los reportes enviados por la sat //////
//Routas Historial
//Rutas para los asesores
Route::get('vis_as',['as'=>'vista_asesores','uses'=>'AsesoresController@inicio']);//Muestra la vista inicial para los asesores al ingresar a la aplicación //////////////
Route::get('listado_sucursales',['as'=>'listado_sucursales','uses'=>'AsesoresController@datos_inicio']);//Devuelve el listado de sucursales dentro de la apliación //////
Route::get('existencia_sucursal/{sucursal}/{bodega}/{todo}',['as'=>'existencia_sucursal','uses'=>'AsesoresController@existencia_sucursal']);//mínimos y máximos /////////
Route::get('dat_ase_existencia/{sucu}/{bod}',['as'=>'dat_ase_existecia','uses'=>'AsesoresController@datos_existencia']);//Carga los datos de la existencia en una sucursal
Route::get('dat_ase_exmin/{sucu}/{bod}',['as'=>'dat_ase_exmin','uses'=>'AsesoresController@datos_existencia_minimos']);//Carga los datos de la existencia minima
Route::get('dat_ase_exreo/{sucu}/{bod}',['as'=>'dat_ase_exreo','uses'=>'AsesoresController@datos_existencia_reorden']);//Carga los datos de la existencia reorden
Route::get('vis_ru/{sucursal}',['as'=>'vista_rutas','uses'=>'AsesoresController@vista_rutas']);//Muestra la vista con el listado de camiones en cada sucursal ///////////
Route::get('lis_ru/{sucursal}',['as'=>'listado_rutas','uses'=>'AsesoresController@listado_rutas']);//Listado de camiones por sucusal ////////////////////////////////////
Route::get('nue_sol/{id}',['as'=>'nueva_solicitud','uses'=>'AsesoresController@nueva_solicitud']);//Muestra la vista para solicitar una entrega a un cliente ////////////
Route::post('guar_sol',['as'=>'guardar_solicitud','uses'=>'AsesoresController@guardar_solicitud']);//Permite guardar una nueva solicitud de entrega /////////////////////
Route::get('edit_cli/{id}/{suc}',['as'=>'editar_cliente','uses'=>'AsesoresController@vista_editar_cliente']);//Muestra la vista para editar la información de los clientes ////
Route::post('g_cliente_editado/{id}',['as'=>'guardar_edit_cliente','uses'=>'AsesoresController@editar_cliente']);//Permite guardar los cambios a un cliente existente ///
Route::get('entregas_cliente/{id}/{suc}',['as'=>'entregas_clientes','uses'=>'AsesoresController@vista_entregas']);//Muestra la vista del total de entregas por cliente ////////
Route::get('nuevo_cliente',['as'=>'nuevo_cliente','uses'=>'AsesoresController@nuevo_cliente']);//Formulario para crear un nuevo cliente /////////////////////////////////
Route::post('guardar_nuevo_cliente',['as'=>'guardar_nuevo_cliente','uses'=>'AsesoresController@guardar_cliente']);//Permite guardar a un nuevo cliente //////////////////
Route::get('vista_entregas/{sucursal}',['as'=>'vista_entregas','uses'=>'AsesoresController@vista_lista_entregas']);//Vista de las entregas solicitadas por el asesor ////
Route::get('listado_entregas/{sucursal}',['as'=>'listado_entregas','uses'=>'AsesoresController@listado_entregas']);//Muestra el listado de entregas sin entregar de cada asesor
Route::get('guardSol/{id}',['as'=>'guardSol','uses'=>'AsesoresController@vista_editar_solicitud']);
Route::patch('guaSol/{id}',['as'=>'guaSol','uses'=>'AsesoresController@editar_solicitud']);
//Rutas para CD
Route::get('propV',['as'=>'propV','uses'=>'TransferenciaController@propietarios']);//Permite agregar el propietario que hará la transferencia
Route::post('actuTran',['as'=>'actuTran','uses'=>'TransferenciaController@actualizar_transferencia']);//Permite actualizar los datos de una transferencia
Route::post('EdETran/{id}',['as'=>'EdETran','uses'=>'TransferenciaController@editar_encabezado_transferencia']);//Permite editar el encabezado de la transferencia
Route::post('agreFac',['as'=>'agreFac','uses'=>'TransferenciaController@agregar_factura']);//Permite cargar datos de factura para transferencia
//Rutas para usuario supervisor
Route::get('STran',['as'=>'STran','uses'=>'STransferenciaController@transferencias_pendientes']);//Carga los datos a la vista con las transferencias en bodega
Route::get('FRTran/{id}',['as'=>'FRTran','uses'=>'STransferenciaController@finalizar_revision_transferencia']);//Permite finalizar la revisión de una transferencia
Route::get('DSFTran',['as'=>'DSFTran','uses'=>'STransferenciaController@transferencias_finalizadas_datos']);//Carga los datos de las ordenes en camino o finalizadas
Route::get('VSTran/{id}',['as'=>'VSTran','uses'=>'STransferenciaController@ver_transferencia_finalizada']);//Permite ver los datos de una transferencia finalizada
Route::get('ISTran/{id}',['as'=>'ISTran','uses'=>'STransferenciaController@imprimir_transferencia']);//Permite imprimir una transferencia finalizada
//Rutas para el usuario de bodega
Route::get('BDBTran',['as'=>'BDBTran','uses'=>'BodegaTransferenciaController@datos_transferencias_en_bodega']);//Carga los datos para la vista inicial de los usuario de bodega
Route::get('EBTran/{id}',['as'=>'EBTran','uses'=>'BodegaTransferenciaController@editar_transferencia']);//Muestra la vista para editar el estado de una transferencia
Route::post('GETran/{id}',['as'=>'GETran','uses'=>'BodegaTransferenciaController@editar_estado_de_transferencia']);//Guarda el nuevo estado de la transferencia
Route::get('FBTran',['as'=>'FBTran','uses'=>'BodegaTransferenciaController@transferencias_finalizadas']);//Muestra la vista con las transferencias finalizadas
Route::get('DFBTran',['as'=>'DFBTran','uses'=>'BodegaTransferenciaController@datos_transferencias_finalizadas']);//Datos de transferencias finalizadas
Route::get('BVTran/{id}',['as'=>'BVTran','uses'=>'BodegaTransferenciaController@ver_transferencia']);//Permite ver los datos de una transferencia

//rtranPF
//Grupo de rutas para el control de transferencias entre sucursales -------------------------------------------------------------------------------------------
Route::get('transp',['as'=>'transferencias_en_espera','uses'=>'TransferenciasSucursales@mis_transferencias_espera']);//Permite ver las transferencias en espera
Route::get('dtransp',['as'=>'datos_transferencias_en_espera','uses'=>'TransferenciasSucursales@datos_mis_transferencias_espera']);//Datos de transferencias en espera
Route::post('crnts',['as'=>'crear_transferencia_sucursales','uses'=>'TransferenciasSucursales@crear_transferencia_sucursales']);//Permite crear transferencias entre sucursales
Route::get('edtrsc/{id}',['as'=>'editar_trans_sucursal','uses'=>'TransferenciasSucursales@editar_transferencias_sucursales']);//Muestra la vista para editar una transferencia
Route::get('nottrans/{id}',['as'=>'noti_transf','uses'=>'TransferenciasSucursales@notificar_transferencia_atrasada']);//Notifica transferencia sin autorizar
Route::post('EdETranS/{id}',['as'=>'enca_trans_sucursal','uses'=>'TransferenciasSucursales@editar_encabezado_transferencia']);//Permite editar el encabezado de la transferencia
Route::post('agpm/{id}',['as'=>'agregar_producto_msucursal','uses'=>'TransferenciasSucursales@agregar_producto_manual_sucursal']);//Permite agregar productos de forma manual a la transferencia
Route::get('protras/{id}',['as'=>'productos_transferencia_sucursales','uses'=>'TransferenciasSucursales@productos_en_transferencia']);//Carga los productos de una sucursal
Route::put('guaprtrasu',['as'=>'guardar_pro_transferencia_sucursales','uses'=>'TransferenciasSucursales@guardar_producto_transferencia']);//Permite guardar productos en una transferencia
Route::get('eletras/{id}',['as'=>'eliminar_pro_transferencia_sucursales','uses'=>'TransferenciasSucursales@eliminar_producto_transferencia']);//
Route::get('detprotrasu',['as'=>'detalle_producto_sucursal','uses'=>'TransferenciasSucursales@detalle_producto_transferencia']);
Route::get('VeTranS/{id}',['as'=>'VeTranSuc','uses'=>'TransferenciasSucursales@ver_transferencia']);//Permite ver una transferencia
Route::get('PTranS/{id}',['as'=>'PTranSu','uses'=>'TransferenciasSucursales@imprimir_pdf']);//Permite imprimir las transferencias por medio de un PDF
Route::get('TrPeS',['as'=>'tran_pendientes_suc','uses'=>'TransferenciasSucursales@transferencias_pendientes_sucursales']);//Se muestran las transferencias pendientes de autorización
Route::get('DTrPeS',['as'=>'dtran_pendientes_suc','uses'=>'TransferenciasSucursales@datos_tran_pendientes_sucursales']);//Carga los datos de las transferencias sin autorización
Route::post('auTran/{id}',['as'=>'auto_transferencia','uses'=>'TransferenciasSucursales@autorizar_transferencia']);//Permite autorizar la transferencia solicitada
Route::get('TrFnS',['as'=>'tran_final_suc','uses'=>'TransferenciasSucursales@trans_finalizadas_sucursales']);//Mis transferencias finalizadas
Route::get('dTrFnS',['as'=>'dtran_fin_suc','uses'=>'TransferenciasSucursales@datos_tran_sucursales_fin']);//Datos mis transferencias finalizadas
Route::get('trnmB',['as'=>'tran_mi_bodega','uses'=>'TransferenciasSucursales@transferencias_a_mi_bodega']);//Vista de las transferencias realizadas a la bodega del usuario
Route::get('dtrnmB',['as'=>'dtran_mi_modega','uses'=>'TransferenciasSucursales@datos_trans_a_mi_bodega']);//Datos de las transferencias a mi bodega
Route::get('agreMaS',['as'=>'buscar_pro_manual_suc','uses'=>'TransferenciasSucursales@buscar_producto_manual']);//Permite buscar productos de forma manual
//------- Rutas para visualizar los reportes de transferencias ------------------------------------------------------------------------------------------------
Route::get('reptraSc',['as'=>'rep_tra_Sucursales','uses'=>'TransferenciasSucursales@reporte_transferencias_sucursales']);//Permite ver el listado de sucursales
Route::get('dreptraSc',['as'=>'drep_tra_Sucursales','uses'=>'TransferenciasSucursales@datos_reporte_transferencia_sucursales']);//Datos de las sucursales reporte
Route::get('traRlSc/{suc}/{bod}',['as'=>'tran_re_su','uses'=>'TransferenciasSucursales@transferencias_realizadas_por_sucursal']);//Listado de transferencias realizadas por sucursal
Route::get('dtraRlSc/{suc}/{bod}',['as'=>'dtran_re_su','uses'=>'TransferenciasSucursales@datos_tran_realizadas_sucursal']);//Datos de transferencias finalizadas por sucursal
Route::get('traRlScF/{suc}/{bod}',['as'=>'tra_resu_fecha','uses'=>'TransferenciasSucursales@transferencias_realizadas_por_fecha_sucursal']);//Vistra reporte por fecha sucursal
Route::get('dtraRsScF/{suc}/{bod}/{inicio}/{fin}',['as'=>'dtrarsf','uses'=>'TransferenciasSucursales@datos_tran_realizadas_sucursal_fecha']);//Datos reporte por fecha sucursal
Route::get('rtranPF',['as'=>'rep_tran_pfecha','uses'=>'TransferenciasSucursales@reporte_transferencias_por_fecha']);//Vista del listado de transferncias realizadas durante un periodo de tiempo
Route::get('drtranPF/{inicio}/{fin}',['as'=>'drtran_pfecha','uses'=>'TransferenciasSucursales@datos_reporte_transferencias_por_fecha']);//Datos del listado de transferncias realizadas
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//------- Rutas para visualizar los reportes de transferencias general-----------------------------------------------------------------------------------------
Route::get('trarep',['as'=>'tra_reporte','uses'=>'TransferenciasSucursales@transferencias_reporte']);
Route::get('dtrarep',['as'=>'datos_tra_reporte','uses'=>'TransferenciasSucursales@datos_trans_reporte']);
//_____________________________________________________________________________________________________________________________________________________________
//------- Rutas para visualizar los reportes de transferencias general fecha ----------------------------------------------------------------------------------
Route::get('trarepf',['as'=>'tra_reporte_fecha','uses'=>'TransferenciasSucursales@transferencias_reporte_fecha']);
Route::get('dtrarepf/{inicio}/{fin}',['as'=>'datos_tra_reporte_fecha','uses'=>'TransferenciasSucursales@datos_trans_reporte_fecha']);
//_____________________________________________________________________________________________________________________________________________________________

//--------------------------- Rutas para el reporte de compras ------------------------------------------------------------------------------------------------
Route::get('rdCop',['as'=>'rep_compras','uses'=>'ComprasController@inicio']);//Vista del reporte de compras realizadas
Route::get('drdCop',['as'=>'drep_compras','uses'=>'ComprasController@datos_inicio']);//Carga los datos del reporte de compras realizadas
Route::get('dreCp/{orden}/{empresa}',['as'=>'detre_compras','uses'=>'ComprasController@detalles_reporte_compras']);//Carga los detalles del reporte de compras
Route::get('rdCoF',['as'=>'rep_compras_f','uses'=>'ComprasController@reporte_compras_por_fecha']);//Vista del reporte de compras filtrado por fecha
Route::get('drdCoF/{inicio}/{fin}',['as'=>'drep_compras_f','uses'=>'ComprasController@datos_reporte_compras_fecha']);//Carga los datos de la compras por fecha
Route::get('vrcpp',['as'=>'vrcp_produ','uses'=>'ComprasController@vista_reporte_compras_por_producto']);//Muestra la vista con el listado de productos
Route::get('dvrcpp',['as'=>'dvrcp_produ','uses'=>'ComprasController@datos_reporte_compras_por_producto']);//Carga los datos del reporte de compras por productos
Route::get('ddrcpp/{producto}',['as'=>'ddrcp_pro','uses'=>'ComprasController@ddetalles_reporte_compras_por_producto']);//Carga los detalles del reporte por productos
Route::get('fdvc',['as'=>'fdvc_prod','uses'=>'ComprasController@vista_compras_por_producto_fecha']);//Vista reporte por pro producto filtrado por fecha
Route::get('dfdvc/{inicio}/{fin}',['as'=>'fdvc_pro','uses'=>'ComprasController@datos_reporte_compras_por_producto_fecha']);//Carga los detalles de productos por fechas
Route::get('ddfvc/{producto}/{inicio}/{fin}',['as'=>'ddfvc_prod','uses'=>'ComprasController@ddetalles_reporte_compras_por_producto_fecha']);

//anultransf
//Transferencias de bodega de compras-------------------------------------
Route::get('trcin',['as'=>'transc_inicio','uses'=>'TransCompras@inicio']);
Route::get('trcin_dat',['as'=>'transc_datos_inicio','uses'=>'TransCompras@datos_inicio']);
//----------------------------------------------------------------------------------------
//Crear transferencia de bodega de compras ------------------------------------------------
Route::post('trcin_cre',['as'=>'transc_crear','uses'=>'TransCompras@crear_transferencia']);
//-----------------------------------------------------------------------------------------
//Permite editar los datos de una nueva transferencia -----------------------------------------
Route::get('trc_edi/{id}',['as'=>'transc_editar','uses'=>'TransCompras@editar_transferencia']);
//---------------------------------------------------------------------------------------------
//Permite modificar el encabezado de una transferencia de compra----------------------------------------------
Route::post('trc_edec/{id}',['as'=>'transc_edi_enca','uses'=>'TransCompras@editar_encabezado_transferencia']);
//------------------------------------------------------------------------------------------------------------
//Permite verificar la descarga del producto en una transferencia---------------------------------
Route::get('trc_vertra/{id}',['as'=>'transc_ver','uses'=>'TransCompras@verificar_transferencia']);
Route::post('trc_guavet/{id}',['as'=>'transc_guartran','uses'=>'TransCompras@guardar_revision_transferencia']);
//-------------------------------------------------------------------------------------------------------------
//Permite imprimir en PDF------------------------------------------------------------------
Route::get('trc_impdf/{id}',['as'=>'transc_impriPDF','uses'=>'TransCompras@imprimir_pdf']);
//-----------------------------------------------------------------------------------------
//Muestra las transferencias por compras finalizadas-------------------------------------------------
Route::get('trc_fin',['as'=>'transc_finalizadas','uses'=>'TransCompras@transferencias_finalizadas']);
Route::get('trc_find',['as'=>'transc_datos_finalizadas','uses'=>'TransCompras@datos_transferencias_finalizadas']);
//-----------------------------------------------------------------------------------------------------------------
//Permite ver la información de una transferencia por compra finalizada -----------------------------
Route::get('trc_vrfin/{id}',['as'=>'transc_ver_transfina','uses'=>'TransCompras@ver_transferencia']);
//---------------------------------------------------------------------------------------------------
//Permite ver las transferencias por compras finalizadas por filtradas por pecho----------
Route::get('trc_finfe',['transc_final_fec','uses'=>'TransCompras@transferencias_finalizadas_fecha']);//BTran
Route::get('trc_dfinfe/{inicio}/{fin}',['as'=>'transc_dafife','uses'=>'TransCompras@datos_transf_final_fecha']);
//--------------------------------------------------------------------------------------------------------------
//Permite validar las transferencias por compras luego de despachadas -------------------------------------------------------
Route::post('trc_mod_enca/{id}',['as'=>'transc_modenc','uses'=>'TransCompras@editar_encabezado_transferencia_despachada']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite anular una transferencia de ingreso por compras
Route::get('anuTransCom/{id}',['as'=>'anultransfcom','uses'=>'TransCompras@anular_transferencia']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de productos marcados con producto en mal estado
Route::get('repprodmes',['as'=>'rep_pro_m_est','uses'=>'TransCompras@reporte_producto_mal_estado']);
Route::get('dreppromes',['as'=>'drep_pro_m_est','uses'=>'TransCompras@datos_producto_mal_estado']);
Route::get('ddrepromes/{cod_producto}',['as'=>'de_dat_rep_m_est','uses'=>'TransCompras@detalle_datos_producto_mal_estado']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el detalle del reporte de producto en mal estado con imagenes
Route::get('drepromeim/{cod_producto}',['uses'=>'de_re_pro_ima','uses'=>'TransCompras@detalles_productos_con_imagenes']);
Route::get('printpdfdrepromeim/{cod_producto}',['uses'=>'printpdf_de_re_pro_ima','uses'=>'TransCompras@pdf_detalles_productos_con_imagenes']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de productos marcados en mal estado por fecha
Route::get('ferepprodmes',['as'=>'fe_rep_pro_mest','uses'=>'TransCompras@reporte_producto_mal_estado_fecha']);
Route::get('daferepromes/{inicio}/{fin}',['as'=>'d_fe_rep_pro_mes','uses'=>'TransCompras@fecha_reporte_producto_mal_estado_fecha']);
Route::get('fecddrepromes/{cod_producto}/{inicio}/{fin}',['as'=>'de_dat_rep_m_est_fe','uses'=>'TransCompras@detalle_datos_producto_mal_estado_fecha']);
Route::get('printpdffechas/{cod_producto}/{inicio}/{fin}',['uses'=>'printpdf_fechas','uses'=>'TransCompras@pdf_detalles_productos_con_imagenes_fechas']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el detalle del reporte de un producto en mal estado con imagenes filtrado por fechas
Route::get('fdreppromein/{cod_producto}/{inicio}/{fin}',['as'=>'de_re_pro_ima_fe','uses'=>'TransCompras@detalles_productos_con_imagenes_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite marcar una transferencia cuando se le ha aplicado una corrección
Route::get('marcorrec/{id}',['as'=>'marcar_correccion','uses'=>'TransCompras@marcar_correccion_transferencia']);
//_____________________________________________________________________________________________________________________________________________________________
//datos_inve
//--------------------------- Rutas para visualizar el reporte de actividades por vendedor --------------------------------------------------------------------
//Permite ver el listado de vendedores registrados en el sistema
Route::get('lisVen',['as'=>'listado_vendedores','uses'=>'ReporteActividades@inicio']);
Route::get('daVen',['as'=>'dat_vendedores','uses'=>'ReporteActividades@datos_inicio']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de reportes realizadas por usuario ------------------------------------------------------------------------------------------------
Route::get('repUs/{id}',['as'=>'reportes_usuarios','uses'=>'ReporteActividades@reportes_por_usuario']);
Route::get('darUs/{id}',['as'=>'datos_rep_usuarios','uses'=>'ReporteActividades@datos_reportes_de_usuarios']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver las ubicaciones de las visitas realizadas por un usuario por medio de la aplicación movil -------------------------------------------------------
Route::get('mpVi/{id}',['as'=>'mapa_visitas','uses'=>'ReporteActividades@mapa_con_visitas_usuario']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de reportes realizadas por un usuario filtrado por fechas -------------------------------------------------------------------------
Route::get('repUsF/{id}',['as'=>'rep_usuarios_fecha','uses'=>'ReporteActividades@reportes_por_usuario_fecha']);
Route::get('drepUsF/{id}/{inicio}/{fin}',['as'=>'da_rep_usuarios_fecha','uses'=>'ReporteActividades@datos_reportes_po_usuario_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver las ubicaciones de las visitas realizadas por un usuario por medio de la aplicación movil filtrado por fechas -----------------------------------
Route::get('mpVisF/{id}/{inicio}/{fin}',['as'=>'mp_vis_fecha','uses'=>'ReporteActividades@mapa_visitas_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el detalle de los reportes realizados por un usuario en especifico ------------------------------------------------------------------------------
Route::get('deRepUs/{id}',['as'=>'det_repor_usuario','uses'=>'ReporteActividades@detalles_reporte_usuario']);
Route::get('ddeRepUs/{id}',['as'=>'ddet_repor_usuario','uses'=>'ReporteActividades@datos_detalles_reporte_usuario']);
Route::get('det_verif/{id}',['as'=>'det_rep_verif','uses'=>'ReporteActividades@detalles_reporte_verificado']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el mapa con las ubicaciones visitas por un reporte de usuario generado --------------------------------------------------------------------------
Route::get('maPRU/{id}',['as'=>'map_re_usua','uses'=>'ReporteActividades@mapa_visitas_por_reporte']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver las actividades de seguimiento de las actividades por vendedor ----------------------------------------------------------------------------------
Route::get('detSegV/{id}',['as'=>'det_seg_ven','uses'=>'ReporteActividades@detalle_seguimiento_usuario']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de todas las actividades realizadas por todos los usuarios ---------------------------------------------------------------------------
Route::get('lisTdAc',['as'=>'lis_td_ac','uses'=>'ReporteActividades@listado_todas_las_actividades']);
Route::get('dlisTdAc',['as'=>'dlis_td_ac','uses'=>'ReporteActividades@datos_listado_todas_las_actividades']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el mapa con las ubicaciones de todas las actividades de los asesores ----------------------------------------------------------------------------
Route::get('mapActGe',['as'=>'map_act_ge','uses'=>'ReporteActividades@mapa_todas_visitas_usuarios']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de días de actividades registrados de todos los usuarios filtrados por fecha ---------------------------------------------------------
Route::get('lisTdAcFe',['as'=>'lis_td_ac_fe','uses'=>'ReporteActividades@listado_de_todas_las_actividades_por_fecha']);
Route::get('litdafed/{inicio}/{fin}',['as'=>'litdafe','uses'=>'ReporteActividades@datos_listado_actividades_por_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de actividades realizadas por todos los usuarios filtrados por fecha -----------------------------------------------------------------
Route::get('lisTdAcReFe',['as'=>'lis_td_ac_re_fe','uses'=>'ReporteActividades@listado_de_todas_las_actividades_registradas_por_fecha']);
Route::get('litdaRefed/{inicio}/{fin}/{rol}',['as'=>'litdarefe','uses'=>'ReporteActividades@datos_listado_actividades_registradas_por_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver las ubicaciones de todos los asesores filtrados por fecha ---------------------------------------------------------------------------------------
Route::get('mapactFe/{inicio}/{fin}',['as'=>'map_acti_fech','uses'=>'ReporteActividades@mapa_todas_las_actividades_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver las ubicaciones de todos los asesores filtrados por fecha ---------------------------------------------------------------------------------------
Route::get('mapactFeRe/{inicio}/{fin}/{rol}',['as'=>'map_acti_fech_re','uses'=>'ReporteActividades@mapa_todas_las_actividades_registradas']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado detallado de actividades realizadas por un unico usuario -----------------------------------------------------------------------------
Route::get('lisdetActiUs/{id}',['as'=>'lis_det_acti_us','uses'=>'ReporteActividades@listado_detallado_actividades_por_usuario']);
Route::get('datlisdetActiUs/{id}',['as'=>'da_lis_det_acti_us','uses'=>'ReporteActividades@datos_listado_detallado_actividades_por_usuario']);
Route::get('mapDetActiUs/{id}',['as'=>'map_det_acti_us','uses'=>'ReporteActividades@mapa_detalle_actividad_usuario']);
Route::get('exportar/{id}',['as'=>'exportar','uses'=>'ReporteActividades@datos_exportar']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado detallado de actividades realizadas por un unico usuario filtradas por un rango de fechas --------------------------------------------
Route::get('lisdetActiUsFe/{id}',['as'=>'lis_det_acti_us_fe','uses'=>'ReporteActividades@listado_detallado_actividades_por_usuario_fecha']);
Route::get('datlisdetActiUsFe/{id}/{inicio}/{fin}',['as'=>'da_lis_det_acti_us_fe','uses'=>'ReporteActividades@datos_listado_detallado_actividades_por_usuario_fecha']);
Route::get('mapDetActiUsFe/{id}/{inicio}/{fin}',['as'=>'map_det_acti_us_fe','uses'=>'ReporteActividades@mapa_detalle_actividad_usuario_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//--------------------------- Rutas para el control de cotizaciones -------------------------------------------------------------------------------------------
//Permite ver el listado de cotizaciones creadas por usuario --------------------------------------------------------------------------------------------------
Route::get('inicot',['as'=>'inicio_cotizaciones','uses'=>'CotizacionesController@inicio']);
Route::get('dinicot',['as'=>'datos_inicio_cotizaciones','uses'=>'CotizacionesController@datos_inicio']);
Route::get('codclie',['as'=>'codigo_cliente','uses'=>'CotizacionesController@codigo_cliente']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite crear una nueva cotización --------------------------------------------------------------------------------------------------------------------------
Route::post('crcot',['as'=>'crear_cotizacion','uses'=>'CotizacionesController@crear_nueva_cotizacion']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite editar una cotización -------------------------------------------------------------------------------------------------------------------------------
Route::get('edicot/{num_movi}',['as'=>'editar_cotizacion','uses'=>'CotizacionesController@editar_cotizacion']);
Route::get('listaprod',['as'=>'listaprod','uses'=>'CotizacionesController@listado_de_productos']);
Route::get('encacot/{num_movi}',['as'=>'enca_cotizacion','uses'=>'CotizacionesController@encabezado_cotizacion_edicion']);
Route::get('formenca',['as'=>'form_enca_cot','uses'=>'CotizacionesController@form_encabezado_cotizacion']);
Route::put('guencot',['as'=>'gu_enc_cot','uses'=>'CotizacionesController@guardar_cambios_encabezado']);
Route::post('agrepro/{id}',['as'=>'agre_produc','uses'=>'CotizacionesController@agregar_producto_cotizacion']);
Route::get('prodcot/{id}',['as'=>'prod_coti','uses'=>'CotizacionesController@productos_en_cotizacion']);
Route::get('dproedi/{cod_producto}/{num_movi}',['as'=>'da_produc_edit','uses'=>'CotizacionesController@datos_producto_editar']);
Route::put('gudatproco',['as'=>'gu_da_prod_cot','uses'=>'CotizacionesController@guardar_datos_producto_cotizacion']);
Route::get('elimproco/{cod_producto}/{num_movi}',['as'=>'eli_prod_cot','uses'=>'CotizacionesController@eliminar_producto_cotizacion']);
Route::get('printcot/{id}/{tipo}',['as'=>'print_coti','uses'=>'CotizacionesController@imprimir_cotizacion_pdf']);
Route::get('sucur',['as'=>'sucursales','uses'=>'CotizacionesController@sucursales_form']);
Route::get('busProd',['as'=>'buscar_pro_manual','uses'=>'CotizacionesController@buscar_producto_manual']);//Permite buscar productos de forma manual
//_____________________________________________________________________________________________________________________________________________________________
//Reporte general de cotizaciones -----------------------------------------------------------------------------------------------------------------------------
Route::get('repcot',['as'=>'reporte_cotizaciones','uses'=>'CotizacionesController@reporte_cotizacion']);
Route::get('drepcot',['as'=>'datos_reporte_cotizaciones','uses'=>'CotizacionesController@datos_reporte_cotizacion']);
//Rutas para ver el reporte general de cotizaciones filtradas por un rango de fechas --------------------------------------------------------------------------
Route::get('repcotf',['as'=>'reporte_cotizaciones_fecha','uses'=>'CotizacionesController@reporte_cotizacion_fecha']);
Route::get('drepcotf/{inicio}/{fin}',['as'=>'datos_reporte_cotizaciones_fecha','uses'=>'CotizacionesController@datos_reporte_cotizacion_fecha']);
//_________________________________________________________________________________________________________________________________________________
//ver_informacion_gasto
//--------------------------- Rutas para el control de gastos de las sucursales -------------------------------------------------------------------------------
//Permite ver el listado de gastos creados pendientes de autorización -----------------------------------------------------------------------------------------
Route::get('inigas',['as'=>'inicio_gastos_espera','uses'=>'GastosController@inicio']);
Route::get('datinig',['as'=>'datos_gastos_inicio','uses'=>'GastosController@datos_gastos_inicio']);
Route::get('codprove',['as'=>'codigo_proveedor','uses'=>'GastosController@codigo_proveedores']);
Route::get('codCUI',['as'=>'codigo_cui','uses'=>'GastosController@codigo_cui_gastos']);
Route::get('lisuscpen',['as'=>'lis_us_gasp','uses'=>'GastosController@listado_usuarios_gastos_pendientes']);
Route::get('gaspenau/{id}',['as'=>'gas_pen_aut','uses'=>'GastosController@gastos_pendientes_autorizacion']);//Gastos de usuarios que pueden autorizar otros gastos
Route::get('dgaspenau/{id}',['as'=>'dgas_pen_aut','uses'=>'GastosController@datos_gastos_pendientes_autorizacion']);//datos de gastos, usuarios con permiso de auto
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver un gasto en estado de espera y modificar información del mismo ----------------------------------------------------------------------------------
Route::get('vergasto/{id}',['as'=>'ver_gasto','uses'=>'GastosController@ver_informacion_gasto']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite guardar una nueva solicitud de gasto para una sucursal ----------------------------------------------------------------------------------------------
Route::post('gua_gast',['as'=>'guardar_gasto','uses'=>'GastosController@crear_nuevo_gasto']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite editar el estado de una solicitud de gasto para ser autorizada o denegada ---------------------------------------------------------------------------
Route::get('autogas/{id}',['as'=>'autorizar_gasto','uses'=>'GastosController@autorizar_gastos']);
Route::post('gua_autogas/{id}',['as'=>'guardar_auto_gasto','uses'=>'GastosController@guardar_autorizacion_gastos']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de gastos autorizados ----------------------------------------------------------------------------------------------------------------
Route::get('gas_auto',['as'=>'gastos_auto','uses'=>'GastosController@mis_gastos_autorizados']);
Route::get('dgas_auto',['as'=>'dgastos_auto','uses'=>'GastosController@datos_mis_gastos_autorizados']);
Route::get('lisucgas',['as'=>'lis_suc_gas','uses'=>'GastosController@listado_sucursales_gastos']);//Muestra el listado de usuarios que han solicitado gastos
Route::get('dlisucgas',['as'=>'dlis_suc_gas','uses'=>'GastosController@datos_listado_sucursales_gastos']);
Route::get('dlisucliq',['as'=>'dlis_suc_liq','uses'=>'GastosController@datos_listado_sucursales_liquidaciones']);
Route::get('mgast_auto/{id}',['as'=>'mgast_auto','uses'=>'GastosController@gastos_autorizados']);//Gastos de usuarios que pueden autorizar otros gastos
Route::get('dmgast_auto/{id}',['as'=>'dmgast_auto','uses'=>'GastosController@datos_gastos_autorizados']);//datos de gastos de usuarios que autorizarn otros gastos
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver los detalles de un gasto que ya fue operado -----------------------------------------------------------------------------------------------------
Route::get('vdgasop/{id}',['as'=>'vegas_op','uses'=>'GastosController@ver_gastos_operados']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite filtrar los gastos de una sucursal por un rango de fecha --------------------------------------------------------------------------------------------
Route::get('fgast_auto/{id}',['as'=>'fgast_auto','uses'=>'GastosController@gastos_autorizados_por_fecha']);
Route::get('dfgast_auto/{id}/{inicio}/{fin}',['as'=>'dfgast_auto','uses'=>'GastosController@datos_gastos_autorizados_por_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de liquidaciones dentor del sistema --------------------------------------------------------------------------------------------------
Route::get('liquid',['as'=>'liquidaciones','uses'=>'GastosController@liquidaciones']);
Route::get('dliquid',['as'=>'dliquidaciones','uses'=>'GastosController@datos_liquidaciones']);
Route::get('lisliqus',['as'=>'lisusliq','uses'=>'GastosController@listado_usuarios_liquidaciones']);//Muestra el listado de usuarios que realizan liquidaciones
Route::get('autliqui/{id}',['as'=>'autor_liquida','uses'=>'GastosController@liquidaciones_otras_liquidaciones']);//Liquidaciones de usuarios que pueden autorizar liquidaciones
Route::get('dautoliqui/{id}',['as'=>'dautoliqui','uses'=>'GastosController@datos_otras_liquidaciones']);//Datos de usuarios que puden autorizar liquidaciones
//_____________________________________________________________________________________________________________________________________________________________
//Permite filtrar las liquidaciones dentro de un rango de fechas seleccionado por parte del usuario -----------------------------------------------------------
Route::get('fautliqui/{id}',['as'=>'fautor_liquida','uses'=>'GastosController@liquidaciones_otras_liquidaciones_fecha']);
Route::get('fdautoliqui/{id}/{inicio}/{fin}',['as'=>'fdautoliqui','uses'=>'GastosController@datos_otras_liquidaciones_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite guardar el registro de una persona para solicitud de gastos por medio de CUI-------------------------------------------------------------------------
Route::post('guar_perso',['as'=>'guar_persona','uses'=>'GastosController@guardar_persona']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite generar un nuevo encabezado de liquidacion para las sucursales --------------------------------------------------------------------------------------
Route::post('nue_liq',['as'=>'nue_liquid','uses'=>'GastosController@nueva_liquidacion']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite editar una liquidación para agregar o eliminar gastos de la misma -----------------------------------------------------------------------------------
Route::get('ed_liquid/{id}',['as'=>'edit_liquid','uses'=>'GastosController@editar_liquidacion']);
Route::get('lis_gasliq/{id}',['as'=>'lis_gas_liq','uses'=>'GastosController@listado_de_gastos_en_liquidacion']);
Route::get('lis_gastliqp',['as'=>'lis_gastliqp','uses'=>'GastosController@listado_gastos_pendientes']);
Route::get('agre_gas_li/{id}/{liqui}',['as'=>'agre_gas_liq','uses'=>'GastosController@agregar_gastos_a_liquidacion']);
Route::get('eli_gas_li/{id}',['as'=>'eli_gas_liq','uses'=>'GastosController@eliminar_gastos_de_liquidacion']);
Route::get('cam_est/{id}',['as'=>'cam_estado','uses'=>'GastosController@cambiar_estado_liquidacion']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el detalle de una liquidación con los gasto que la conforman ------------------------------------------------------------------------------------
Route::get('ve_dliqui/{id}',['as'=>'ve_dliqui','uses'=>'GastosController@ver_detalles_liquidacion']);
Route::get('d_dliqui/{id}',['as'=>'da_dliqui','uses'=>'GastosController@datos_ver_detalles_liquidacion']);
Route::get('rev_liqui/{id}',['as'=>'rev_liquid','uses'=>'GastosController@revision_de_liquidacion']);
Route::get('impriLiq/{id}',['as'=>'impri_liqui','uses'=>'GastosController@imprimir_liquidacion']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite modificar la información del encabezado de una liquidación en estado creada -------------------------------------------------------------------------
Route::post('edi_enca_li/{id}',['as'=>'edit_enca_li','uses'=>'GastosController@editar_encabezado_liquidacion']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado de gastos que han sido rechazados y no podran ser incluidos dentro de una liquidacion de gastos
Route::get('gasrech',['as'=>'gast_recha','uses'=>'GastosController@gastos_rechazados']);
Route::get('dgastrech',['as'=>'dagast_rech','uses'=>'GastosController@datos_gastos_rechazados']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado detallado de todas las liquidaciones
Route::get('repliqui',['as'=>'rep_liquida','uses'=>'GastosController@rep_liquidaciones_otras_liquidaciones']);//Liquidaciones de usuarios que pueden autorizar liquidaciones
Route::get('repdautoliqui',['as'=>'repdautoliqui','uses'=>'GastosController@rep_datos_otras_liquidaciones']);//Datos de usuarios que puden autorizar liquidaciones
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado detallado de todos los gastos---------------------------------------------------------------------------------------------------------
Route::get('rep_mgast_auto',['as'=>'rep_mgast_auto','uses'=>'GastosController@rep_gastos_autorizados']);//Gastos de usuarios que pueden autorizar otros gastos
Route::get('rep_dmgast_auto',['as'=>'rep_dmgast_auto','uses'=>'GastosController@rep_datos_gastos_autorizados']);//datos de gastos de usuarios que autorizarn otros gastos
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el listado detallado de todos los gastos filtrados por fecha ------------------------------------------------------------------------------------
Route::get('rep_mgast_autof',['as'=>'rep_mgast_auto_f','uses'=>'GastosController@rep_gastos_autorizados_fecha']);
Route::get('rep_dmgast_autof/{inicio}/{fin}',['as'=>'rep_dmgast_auto_f','uses'=>'GastosController@rep_datos_gastos_autorizados_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el istado de liquidaciones de todos los usuarios filtrados por un rango de fechas
Route::get('repliquif',['as'=>'rep_liquida_f','uses'=>'GastosController@rep_liquidaciones_otras_liquidaciones_fecha']);
Route::get('repdautoliquif/{inicio}/{fin}',['as'=>'repdautoliquif','uses'=>'GastosController@rep_datos_otras_liquidaciones_fecha']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el resumen de gastos por monto total autorizado en un mes
Route::get('resgas',['as'=>'resumen_de_gastos','uses'=>'GastosController@resumen_de_gastos']);
Route::get('dresgas',['as'=>'datos_resumen_de_gastos','uses'=>'GastosController@datos_resumen_de_gastos']);
Route::get('dtg/{keym}',['as'=>'detre_gastos','uses'=>'GastosController@detalle_resumen_de_gastos']);
//_____________________________________________________________________________________________________________________________________________________________
//Permite ver el resumen de gastos total autorizado en un mes
Route::get('resgast',['as'=>'resumen_total_de_gastos','uses'=>'GastosController@resumen_total_de_gastos']);
Route::get('dresgast',['as'=>'datos_resumen_total_de_gastos','uses'=>'GastosController@datos_resumen_total_de_gastos']);
//_____________________________________________________________________________________________________________________________________________________________
