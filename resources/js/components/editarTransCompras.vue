<template>
    <div class="container-fluid container-task">
        <section v-if="errored">
            <div class="alert alert-danger" role="alert">
                Parece que algo fallo, refresca la página para solucionarlo...!
            </div>
        </section>
        <input class="form-control" type="text" v-model="searchQuery" placeholder="Search">
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" v-if="arrayTasks.length" >
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Código</th>
                        <th>Producto</th> 
                        <th>Cantidad</th>
                        <th>Bultos</th>
                        <th>Peso</th>
                        <th>Volumen</th> 
                        <th>Editar</th>
                        <th>Eliminar</th> 
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in filteredResources" :key="item.id">
                        <td v-text="item.nombre"></td>
                        <td v-text="item.nombre_corto"></td>
                        <td v-text="item.nombre_fiscal"></td>
                        <td v-text="item.cantidad"></td>
                        <td v-text="item.costo"></td> 
                        <td v-text="parseFloat(item.peso).toFixed(3)"></td> 
                        <td v-text="parseFloat(item.volumen).toFixed(3)"></td>
                        <td><button class="btn btn-dark btn-sm" @click="loadFieldsUpdate(item)" data-toggle="modal" data-target="#actualizar"><i class="fas fa-edit"></i> Editar</button></td>
                        <td><button class="btn btn-danger btn-sm" @click="deleteTask(item)">Eliminar</button></td>
                    </tr>
                </tbody> 
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th></th> 
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{ total }}</th>
                        <th>{{ vtotal}}</th>
                        <th></th>
                        <th></th>
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
                        <div class="table-responsive-sm">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td v-text="nombre_corto"></td>
                                        <td v-text="nombre_fiscal"></td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <form v-on:submit.prevent="onSubmit">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Cantidad a enviar</label>
                                    <input v-model="cantidad" type="number" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6"><!-- Formulario para la creación o modificación de nuestras tareas-->
                                    <label>Bultos</label>
                                    <input v-model="bultos" type="number" step="1" class="form-control">
                                </div>
                                <div class="form-group col-md-12">
                                    <!-- Botón que modifica la tarea que anteriormente hemos seleccionado, solo se muestra si la variable update es diferente a 0-->
                                    <button  @click="updateTasks()" class="btn btn-success btn-sm btn-block" data-dismiss="modal">Guardar</button>
                                </div>
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
<script >
    export default { 
        data(){
            return{
                searchQuery: null,
                cantidad:"", //Esta variable, mediante v-model esta relacionada con el input del formulario
                bultos:"",
                nombre_corto:"",
                nombre_fiscal:"",
                existencia:"",
                sucursal:"",
                min:"",
                reo:"",
                max:"",
                cantidadSu:"",
                update:0, /*Esta variable contrarolará cuando es una nueva tarea o una modificación, si es 0 significará que no hemos seleccionado
                          ninguna tarea, pero si es diferente de 0 entonces tendrá el id de la tarea y no mostrará el boton guardar sino el modificar*/
                arrayTasks:[], //Este array contendrá las tareas de nuestra bd
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
                this.filteredResources.forEach((item)=>{
                    total += parseFloat(item.peso,2);
                });
                return parseFloat(total/1000).toFixed(3);
            },
            vtotal: function(){
                let vtotal = 0;
                this.filteredResources.forEach((item)=>{
                    vtotal += parseFloat(item.volumen);
            });
                return parseFloat(vtotal).toFixed(3);
            },
            filteredResources (){
                if(this.searchQuery){
                    return this.arrayTasks.filter((item)=>{
                        return item.nombre_corto.startsWith(this.searchQuery) || item.nombre_fiscal.startsWith(this.searchQuery)
                        || item.nombre.startsWith(this.searchQuery);
                    })
                }else{
                    return this.arrayTasks;
                }
            }
        },
        methods:{
            getTasks(){
                let me =this;
                let url = url_global+'/proentra/'+ server_data  //Ruta que hemos creado para que nos devuelva todas las tareas
                axios.get(url).then(function (response) {
                    //creamos un array y guardamos el contenido que nos devuelve el response
                    me.arrayTasks = response.data;
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
                axios.put(url_global+'/guaprotra',{
                    'id':this.update,
                    'cantidad':this.cantidad,
                    'bultos':this.bultos,
                }).then(function (response) {
                   me.getTasks();//llamamos al metodo getTask(); para que refresque nuestro array y muestro los nuevos datos
                   me.clearFields();//Limpiamos los campos e inicializamos la variable update a 0
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
            deleteTask(data){//Esta nos abrirá un alert de javascript y si aceptamos borrará la tarea que hemos elegido
                let me = this;
                let task_id = data.id
                axios.get(url_global+'/eletra/'+task_id
                    ).then(function (response) {
                        me.getTasks();
                    })
                    .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
            loadFieldsUpdate(data){ //Esta función rellena los campos y la variable update, con la tarea que queremos modificar
                this.update = data.id
                let me =this;
                let url = url_global+'/detprotra?id='+this.update;
                axios.get(url).then(function (response) {
                    me.nombre_corto = response.data.nombre_corto;
                    me.nombre_fiscal = response.data.nombre_fiscal;
                    me.existencia = response.data.existencia;
                    me.sucursal = response.data.sucursal;
                    me.min = response.data.min;
                    me.reo = response.data.reo;
                    me.max = response.data.max;
                    me.cantidad = response.data.cantidad;
                    me.bultos = response.data.bultos;
                    me.cantidadSu = response.data.cantidadSu;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
            clearFields(){/*Limpia los campos e inicializa la variable update a 0*/
                this.name = "";
                this.description = "";
                this.content = "";
                this.update = 0;
            }
        },
        mounted() {
           this.getTasks();
        }
    }
</script>