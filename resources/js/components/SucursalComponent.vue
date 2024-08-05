<template>
    <div class="container-fluid container-task">
        <section v-if="errored">
            <div class="alert alert-danger" role="alert">
                Parece que algo fallo, refresca la página para solucionarlo...!
            </div>
        </section>
        <div class="row">
            <input class="form-control" type="text" v-model="searchQuery" placeholder="Search">
            <div class="table-responsive">
            <table class="table table-sm table-borderless" v-if="arrayTasks.length" >
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Cantidad enviada</th> 
                        <th>Cantidad recibida</th>
                        <th>Mal estado</th>
                        <th>Editar</th> 
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in filteredResources" :key="item.id">
                        <template v-if="item.cantidad1 == item.cantidad">
                            <td v-text="item.nombre_corto"></td>
                            <td v-text="item.nombre_fiscal"></td>
                            <td v-text="item.cantidad1"></td> 
                            <td v-text="item.cantidad"></td>
                            <td v-text="item.mal_estado"></td> 
                            <td><button class="btn btn-danger btn-sm" @click="loadFieldsUpdate(item)" data-toggle="modal" data-target="#create">Editar</button></td>
                        </template>
                        <template v-else>
                            <td style="background-color:#F3393C52;" v-text="item.nombre_corto"></td>
                            <td style="background-color:#F3393C52;" v-text="item.nombre_fiscal"></td>
                            <td style="background-color:#F3393C52;" v-text="item.cantidad1"></td> 
                            <td style="background-color:#F3393C52;" v-text="item.cantidad"></td> 
                            <td style="background-color:#F3393C52;" v-text="item.mal_estado"></td>
                            <td><button class="btn btn-dark btn-sm" @click="loadFieldsUpdate(item)" data-toggle="modal" data-target="#create">Editar</button></td>
                        </template>
                        <template v-if="item.cantidad1 == null & item.cantidad == null">
                            <td><button class="btn btn-danger btn-sm" @click="deleteTask(item)">Eliminar</button></td>
                        </template>
                        <template v-else>
                            <td></td>
                        </template>
                    </tr>
                </tbody> 
            </table>
        </div>
        </div>
        <div class="modal fade" id="create">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><!-- Formulario para la creación o modificación de nuestras tareas-->
                            <h5 v-text="nombre_corto"></h5>
                        </div>
                        <div class="form-group">
                            <label>Cantidad enviada</label>
                            <input v-model="cantidad1" type="number" class="form-control" disabled>
                        </div>
                        <div class="form-group"><!-- Formulario para la creación o modificación de nuestras tareas-->
                            <label>Cantidad recibida</label>
                            <input v-model="cantidad" type="number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Cantidad dañada</label>
                            <input v-model="mal_estado" type="number" class="form-control" required>
                        </div>
                        <div class="container-buttons">
                            <!-- Botón que modifica la tarea que anteriormente hemos seleccionado, solo se muestra si la variable update es diferente a 0-->
                            <button v-if="update != 0" @click="updateTasks()" class="btn btn-success btn-sm btn-block" data-dismiss="modal">Guardar</button>
                        </div>
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
                cantidad:0, //Esta variable, mediante v-model esta relacionada con el input del formulario
                cantidad1:"",
                nombre_corto:"",
                mal_estado:0,
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
            filteredResources (){
                if(this.searchQuery){
                    return this.arrayTasks.filter((item)=>{
                        return item.nombre_corto.startsWith(this.searchQuery) || item.nombre_fiscal.startsWith(this.searchQuery);
                    })
                }else{
                    return this.arrayTasks;
                }
            }
        },

        methods:{
            getTasks(){
                let me =this;
                let url = url_global+'/PTranE/'+ server_data  //Ruta que hemos creado para que nos devuelva todas las tareas
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
                axios.put(url_global+'/RePro',{
                    'id':this.update,
                    'cantidad':this.cantidad,
                    'mal_estado': this.mal_estado,
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
            loadFieldsUpdate(data){ //Esta función rellena los campos y la variable update, con la tarea que queremos modificar
                this.update = data.id
                let me =this;
                let url = url_global+'/verPro?id='+this.update;
                axios.get(url).then(function (response) {
                    me.cantidad1 = response.data.cantidad1;
                    me.nombre_corto = response.data.nombre_corto;
                    me.cantidad = response.data.cantidad;
                    me.mal_estado = response.data.mal_estado;
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
                axios.get(url_global+'/AcTran/'+task_id
                    ).then(function (response) {
                        me.getTasks();
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
