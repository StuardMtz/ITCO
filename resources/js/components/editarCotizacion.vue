<template>
    <div class="container-fluid">
        <section v-if="errored">
            <div class="alert alert-danger" role="alert">
                Parece que algo fallo, refresca la página para solucionarlo...!
            </div>
        </section>
        <div class="card encabezado" v-if="arrayEncabezado.length">
            <ul class="list-inline text-monospace text-wrap" v-for="item of arrayEncabezado" :key="item.num_movi">
                <li class="list-inline-item"><b>Número: </b><p v-text="item.num_movi"></p></li>
                <li class="list-inline-item"><b>Cliente:</b> <p v-text="item.Nombre_cliente+ '('+item.cod_cliente+')'"></p></li>
                <li class="list-inline-item"><b>Tipo de cambio:</b> <p v-text="item.quetzales_por_dolar"></p></li>
                <li class="list-inline-item"><b>Saldo pendiente:</b> <p v-text="'Q.'+item.saldo"></p></li>
                <li class="list-inline-item"><b>Referencia:</b> <p v-text="item.referencia"></p></li>
                <li class="list-inline-item"><b>Descripción:</b> <p v-text="item.descripcion1"></p></li>
                <li class="list-inline-item"><b>Sucursal</b> <p v-text="item.sucursal"></p></li>
                <li class="list-inline-item"><b>Fecha</b> <p v-text="format_date(item.fecha)"></p></li>
                <li class="list-inline-item"><b>Observación:</b> <p v-text="item.descripcion2"></p></li>
                <li class="list-inline-item"><button class="btn btn-dark btn-sm" @click="loadFieldsUpdate(item)" data-toggle="modal" data-target="#actualizar"><i class="fas fa-edit"></i> Editar</button></li>
            </ul>
        </div>
        <form v-on:submit.prevent="agregarProducto()">
            <div class="input-group">
                <input class="form-control form-control-sm" type="text" v-model="searchProducto" placeholder="Buscar y seleccionar producto">
                <select class="form-control" v-model="producto" required size="3">
                    <option v-for="pro of filtrarCodigos" :key="pro.id" :value="pro.id" >{{ pro.nombre_corto }} {{ pro.nombre_fiscal }}</option>
                </select>
                <input type="number" placeholder="ingrese la cantidad" class="form-control" v-model="cantidad_enviar" required>
                <button type="submit" class="btn btn-sm btn-success">Agregar</button>
            </div>
        </form>
        <div class="table-responsive-sm">
            <input class="form-control form-control-sm" type="text" v-model="searchQuery" placeholder="Filtrar productos agregados">
            <table class="table table-sm table-borderless" v-if="arrayProductos.length">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Producto</th> 
                        <th>Cantidad</th>
                        <th>Precio normal</th> 
                        <th>Precio cotización</th>
                        <th>Descuento %</th>
                        <th>Monto descuento</th>
                        <th>Total</th>
                        <th>Editar</th>
                        <th>Eliminar</th> 
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in filteredResources" :key="item.cod_producto">
                        <td v-text="item.orden"></td>
                        <td v-text="item.nombre_corto"></td>
                        <td v-text="item.nombre_fiscal"></td>
                        <td v-text="item.cantidad"></td>
                        <template v-if="item.lis_precio > 0">
                            <td v-text="parseFloat(item.lis_precio).toFixed(2)"></td>
                        </template>
                        <template v-else>
                            <td v-text="parseFloat(item.precio_normal).toFixed(2)"></td>
                        </template>
                        <td v-text="parseFloat(item.precio).toFixed(2)"></td>
                        <td v-text="item.descuento+'%'"></td>
                        <td style="text-transform: capitalize;" v-text="item.Moneda + parseFloat(item.monto_descuento).toFixed(2)"></td>
                        <td v-text="item.total"></td>
                        <td><button class="badge badge-dark" @click="loadProductosUpdate(item)" data-toggle="modal" data-target="#actualizar_producto">Editar</button></td>
                        <td><button class="badge badge-danger" @click="deleteTask(item)">Eliminar</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background-color: darkseagreen;">
                        <td></td>
                        <td></td> 
                        <td></td> 
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b><ins>Total</ins></b></td>
                        <td><b>{{ total }}</b></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>     
            </table>
        </div>
        <div class="modal fade" id="actualizar">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form v-on:submit.prevent="updateTasks()">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Nombre cliente</label>
                                    <input v-model="nombre_cliente" type="text" class="form-control" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Descripción</label>
                                    <textarea v-model="descripcion" class="form-control" required></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Observación</label>
                                    <textarea v-model="observacion" required class="form-control"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Referencia</label>
                                    <textarea v-model="referencia" class="form-control"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Sucursal</label>
                                    <select name="sucursales" class="form-control" v-model="sucursal" required >
                                        <option v-for="suc in arraySucursales" :key="suc.cod_unidad" v-bind:value="suc.cod_unidad">{{suc.nombre}}</option>
                                    </select>
                                    <span>Unidad seleccionada: {{ sucursal }}</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="form-group col-md-12">
                                        <!-- Botón que modifica la tarea que anteriormente hemos seleccionado, solo se muestra si la variable update es diferente a 0-->
                                    <button  @click="updateTasks()" class="btn btn-success btn-sm btn-block" type="submit" data-dismiss="modal">Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="actualizar_producto">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive-sm">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Existencia</th>
                                        <th>Precio</th>
                                        <th>Descuento %</th>
                                        <th>Monto descuento</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td v-text="nombre_corto"></td>
                                        <td v-text="nombre_fiscal"></td> 
                                        <td v-text="cantidad"></td> 
                                        <td style="background-color:#F78D3D8F" v-text="existencia"></td>
                                        <td v-text="parseFloat(precio).toFixed(2)"></td>
                                        <td v-text="descuento+'%'"></td>
                                        <td style="text-transform: capitalize;" v-text="moneda+monto_descuento"></td>
                                        <td v-text="parseFloat((cantidad * precio)-((cantidad * precio) * (descuento/100))).toFixed(2)"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <form class="needs-validation" novalidate>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Cantidad</label>
                                    <input v-model.number="cantidad" id="cantidad" type="number" min="1" class="form-control" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Precio</label>
                                    <input v-model.number="precio" id="precio" type="number" min="0.01" step="0.01" class="form-control" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Descuento</label>
                                    <input v-model.number="descuento" min="0" type="number" class="form-control">
                                </div>
                                <template v-if="(existencia - cantidad) <= 0">
                                    <div class="form-group col-md-12">
                                        <div class="alert alert-danger" role="alert">
                                        ¡ La cantidad cotizada es mayor a la existencia en la sucursal!
                                        </div>
                                    </div>
                                </template>
                                <template v-if="cantidad > 0  && precio > 0 && descuento >= 0">
                                    <div class="form-group col-md-12">
                                        <!-- Botón que modifica la tarea que anteriormente hemos seleccionado, solo se muestra si la variable update es diferente a 0-->
                                        <button type="submit"  @click="updateProducto()" class="btn btn-success btn-sm btn-block" data-dismiss="modal">Guardar</button>
                                    </div>
                                </template>
                                <template v-else>
                                </template>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>          
</template>
<script type="application/json" name="server-data">
       {{ $id }}
</script>
<script type="application/javascript">
       var json = document.getElementsByName("server-data")[0].innerHTML;
       var server_data = JSON.parse(json);
</script>
<script>
    export default { 
        data(){
            return{
                searchQuery: null,
                searchProducto: null,
                nombre_cliente:"", //Esta variable, mediante v-model esta relacionada con el input del formulario
                descripcion:"",
                observacion:"",
                nombre_corto:"",
                nombre_fiscal:"",
                cantidad:0,
                producto: "",
                cantidad_enviar:"",
                precio:0,
                descuento:0,
                existencia:0,
                precio_minimo:"",
                referencia: "",
                monto_descuento:0,
                moneda:"",
                sucursal:"",
                cod_unidad: "",
                update:0, /*Esta variable contrarolará cuando es una nueva tarea o una modificación, si es 0 significará que no hemos seleccionado
                          ninguna tarea, pero si es diferente de 0 entonces tendrá el id de la tarea y no mostrará el boton guardar sino el modificar*/
                arrayProductos:[], //Este array contendrá las tareas de nuestra bd
                arrayEncabezado:[],
                arraySucursales:[],
                arrayAgregarProductos:[],
                loading: true,
                errored: false
            }
        },
        _computed: {
                'server_data': function() {
                return window.server_data;
            }
        },
        computed: {
            total: function(){
                let total = 0;
                Object.values(this.arrayProductos).forEach(
                    (item)=>(total += parseFloat(item.total)),
                   
                );
                return parseFloat(total).toFixed(2);
            },
            dolar: function(){
                let dolar = 0;
                Object.values(this.arrayProductos).forEach(
                    (vol)=>(dolar += (parseFloat(vol.iva*vol.total)+parseFloat(vol.total)))
                );
                return parseFloat(dolar).toFixed(2);
            },
            filteredResources (){
                if(this.searchQuery){
                    return this.arrayProductos.filter((item)=>{
                        return item.nombre_corto.startsWith(this.searchQuery) || item.nombre_fiscal.startsWith(this.searchQuery);
                    })
                }else{
                    return this.arrayProductos;
                }
            },
            filtrarCodigos() {
                if(this.searchProducto) {
                    return this.arrayAgregarProductos.filter((pro) => {
                        return pro.nombre_corto.startsWith(this.searchProducto) || pro.nombre_fiscal.startsWith(this.searchProducto); 
                    })
                }else {
                    return this.arrayAgregarProductos;
                }
            }
        },
        methods:{
            format_date(value){
                if (value) {
                    return moment(String(value)).format('DD/MM/YYYY')
                }
            },
            async getEncabezado(){
                let me =this;
                let url = url_global+'/encacot/'+ server_data  //Ruta que hemos creado para que nos devuelva todos los productos 
                axios.get(url).then(function (response) {
                    //creamos un array y guardamos el contenido que nos devuelve el response
                    me.arrayEncabezado = response.data;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            async getSucursales(){
                let me =this;
                let url = url_global+'/sucur'  //Ruta que hemos creado para que nos devuelva todos los productos 
                axios.get(url).then(function (response) {
                    //creamos un array y guardamos el contenido que nos devuelve el response
                    me.arraySucursales = response.data;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            async getProductos(){
                let me =this;
                let url = url_global+'/prodcot/'+ server_data  //Ruta que hemos creado para que nos devuelva todos los productos 
                axios.get(url).then(function (response) {
                    //creamos un array y guardamos el contenido que nos devuelve el response
                    me.arrayProductos = response.data;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            async cargarProductos(){
                let me = this; 
                let url = url_global + '/listaprod'
                axios.get(url).then(function(response) {
                    me.arrayAgregarProductos = response.data;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            updateTasks(){/*Esta funcion, es igual que la anterior, solo que tambien envia la variable update que contiene el id de la
                tarea que queremos modificar*/
                let me = this;
                axios.put(url_global+'/guencot',{
                    'num_movi':this.update,
                    'nombre_cliente':this.nombre_cliente,
                    'descripcion':this.descripcion,
                    'observacion':this.observacion,
                    'referencia':this.referencia,
                    'sucursal': this.sucursal,
                }).then(function (response) {
                   //llamamos al metodo getTask(); para que refresque nuestro array y muestro los nuevos datos
                   me.getEncabezado();
                   me.clearFields();//Limpiamos los campos e inicializamos la variable update a 0
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            agregarProducto() {
                let me = this;
                axios.post(url_global+'/agrepro/'+ server_data, {
                    'producto':this.producto,
                    'cantidad_enviar':this.cantidad_enviar,
                }).then(function(response) {
                    me.getProductos();
                    me.clearFields();
                })
            },
            updateProducto(){/*Esta funcion, es igual que la anterior, solo que tambien envia la variable update que contiene el id de la
                tarea que queremos modificar*/
                let me = this;
                axios.put(url_global+'/gudatproco',{
                    'cod_producto':this.update,
                    'cantidad':this.cantidad,
                    'precio':this.precio,
                    'descuento':this.descuento,
                    'num_movi':this.num_movi,
                }).then(function (response) {
                   //llamamos al metodo getTask(); para que refresque nuestro array y muestro los nuevos datos
                   me.getProductos();
                   me.clearFields();//Limpiamos los campos e inicializamos la variable update a 0
                   //me.loadProductosUpdate();
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
            deleteTask(data){//Esta nos abrirá un alert de javascript y si aceptamos borrará la tarea que hemos elegido
                let me = this;
                let del_cod_producto = data.cod_producto
                let del_num_movi = data.num_movi
                axios.get(url_global+'/elimproco/'+del_cod_producto+'/'+del_num_movi
                    ).then(function (response) {
                        me.getProductos();
                    })
                    .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
            loadFieldsUpdate(data){ //Esta función rellena los campos y la variable update, con la tarea que queremos modificar
                this.update = data.num_movi
                let me =this;
                let url = url_global+'/formenca?num_movi='+this.update;
                axios.get(url).then(function (response) {
                    me.nombre_cliente = response.data.nombre_cliente;
                    me.descripcion = response.data.descripcion;
                    me.observacion = response.data.descripcion2;
                    me.referencia = response.data.referencia;
                    me.sucursal = response.data.sucursal;
                    me.cod_unidad = response.data.cod_unidad;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
            loadProductosUpdate(data){ //Esta función rellena los campos y la variable update, con la tarea que queremos modificar
                this.update = data.cod_producto
                this.num_movi = data.num_movi
                let me =this;
                let url = url_global+'/dproedi/'+this.update+'/'+this.num_movi;
                axios.get(url).then(function (response) {
                    me.nombre_corto = response.data.nombre_corto;
                    me.nombre_fiscal = response.data.nombre_fiscal;
                    me.cantidad = response.data.cantidad;
                    me.precio = response.data.precio;
                    me.descuento = response.data.descuento;
                    me.existencia = response.data.existencia;
                    me.precio_minimo = response.data.precio_minimo;
                    me.monto_descuento = response.data.monto_descuento;
                    me.moneda = response.data.moneda;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
            clearFields(){/*Limpia los campos e inicializa la variable update a 0*/
                this.nombre_cliente = "";
                this.descripcion = "";
                this.observacion = "";
                this.referencia = "";
                this.producto = "";
                this.cantidad_enviar = 0;
            }
        },
        mounted() {
           this.getEncabezado();
           this.getProductos();
           this.getSucursales();
           this.cargarProductos()
        }
    }
</script>