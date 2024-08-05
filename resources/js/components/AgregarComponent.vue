<template>
    <div class="container-fluid container-task">
        <section v-if="errored">
            <div class="alert alert-danger" role="alert">
                Parece que algo fallo, refresca la página para solucionarlo...!
            </div>
        </section>
        <div class="row">
            <div class="table-responsive">
                <vue-good-table
                :columns="columns"
                :rows="arrayTasks"
                max-height="700px"
                :fixed-header="false"
                @on-row-click="deleteTask" 
                styleClass="vgt-table condensed"
                :search-options="{
                    placeholder: 'Buscar productos',
                    enabled: true
                }">
            </vue-good-table>
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
                errored: false,

                columns: [
                    {
                        label: 'Categoría',
                        field: 'nombre',
                        sortable: false,
                    },
                    {
                        label: 'Código',
                        field: 'nombre_corto',
                        sortable: true,
                        firstSortType: 'asc'
                    },
                    {
                        label: 'Producto',
                        field: 'nombre_fiscal',
                    },
                    {
                        label: 'Mi bodega',
                        field: 'existencia',
                        type: 'number',
                        tdClass: this.tdClassExistencia
                    },
                    {
                        label: 'Sucursal',
                        field: 'sucursal',
                        type: 'number',
                        tdClass: this.tdClassSucursal
                    },
                    {
                        label: 'Mínimo',
                        field: 'minimo',
                        type: 'number',
                        tdClass: this.tdClassFaltante
                    },
                    {
                        label: 'Reorden',
                        field: 'piso_sugerido',
                        type: 'number',
                        tdClass: this.tdClassFaltante
                    },
                    {
                        label: 'Máximo',
                        field: 'maximo',
                        type: 'number',
                        tdClass: this.tdClassFaltante
                    }
                ]
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
            tdClassExistencia(row) {
                if (row.existencia > -1000) {
                    return 'alert alert-warning';
                }
                return 'alert alert-warning';
            },
            tdClassSucursal(row) {
                if (row.sucursal > -1000) {
                    return 'alert alert-info';
                }
                return 'alert alert-info';
            },
            tdClassFaltante(row) {
                if((row.sucursal / row.maximo) * 100 >= 75 && (row.sucursal / row.maximo) * 100 <= 100 ){
                    return 'alert alert-success';
                }
                if((row.sucursal / row.maximo) * 100 >= 50 && (row.sucursal / row.maximo) * 100 < 75 ){
                    return 'precaucion-class';
                }
                if((row.sucursal / row.maximo) * 100 >= 30 && (row.sucursal / row.maximo) * 100 < 50 ){
                    return 'atencion-class';
                }
                if((row.sucursal / row.maximo) * 100 < 30){
                    return 'urgente-class';
                }
            },

            getTasks(){
                let me =this;
                let url = url_global+'/DnueTra/'+ server_data  //Ruta que hemos creado para que nos devuelva todas las tareas
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
            deleteTask(params){//Esta nos abrirá un alert de javascript y si aceptamos borrará la tarea que hemos elegido
                this.id_dato = params.row.id
                let me = this;
                let task_id = params.row.id
                axios.get(url_global+'/agrTran/'+task_id
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