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
                <table class="table table-sm" v-if="arrayTasks.length" >
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Código</th>
                            <th>Producto</th> 
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in filteredResources" :key="item.id" @click="deleteTask(item)">
                            <td v-text="item.nombre"></td>
                            <td v-text="item.nombre_corto"></td>
                            <td v-text="item.nombre_fiscal"></td> 
                        </tr>
                    </tbody> 
                </table>
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
                cantidad1:"", //Esta variable, mediante v-model esta relacionada con el input del formulario
                cantidadSolicitada:"",
                nombre_corto:"",
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
                let url = url_global+'/ProAgre/'+ server_data  //Ruta que hemos creado para que nos devuelva todas las tareas
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
                axios.put(url_global+'/EdPro',{
                    'id':this.update,
                    'cantidad1':this.cantidad1,
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
                let url = url_global+'/buscar?id='+this.update;
                axios.get(url).then(function (response) {
                    me.cantidad1 = response.data.cantidad1;
                    me.nombre_corto = response.data.nombre_corto;
                    me.cantidadSolicitada = response.data.cantidadSolicitada;
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
                axios.get(url_global+'/actuTranIn/'+task_id
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