<template>
    <div class="container-fluid container-task">
        <section v-if="errored">
            <div class="alert alert-danger" role="alert">
                Parece que algo fallo, refresca la página para solucionarlo...!
            </div>
        </section>
        <button class="btn btn-sm btn-success" @click="downloadExl()"> Exportar a excel</button>
        <input class="form-control" type="text" v-model="searchQuery" placeholder="Search">
        <div class="table-responsive-sm" id="tableId">
            <table class="table table-sm table-borderless">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Prospecto</th> 
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Detalles</th>
                        <th>Revisado</th> 
                    </tr>
                </thead>
                <tbody  v-if="arrayTasks.length">
                    <tr v-for="item in filteredResources" :key="item.id">
                        <td v-text="item.orden"></td>
                        <td v-text="item.name"></td>
                        <td v-text="item.Descripcion"></td>
                        <td v-text="item.nombre"></td>
                        <td v-text="format_date(item.Fecha)"></td>
                        <td v-text="item.Hora"></td> 
                        <td><button class="btn btn-dark btn-sm" @click="loadFieldsUpdate(item)" data-toggle="modal" data-target="#actualizar"><i class="fas fa-edit"></i> Detalles</button></td>
                        <template v-if="item.verificado != null">
                            <td><button class="btn btn-primary btn-sm" ><i class="fas fa-check"></i></button></td>
                        </template>
                        <template v-if = "item.verificado == null">
                            <td><button class="btn btn-danger btn-sm" @click="deleteTask(item)"><i class="fas fa-times"></i></button></td>
                        </template>
                    </tr>
                </tbody>  
            </table>
        </div>
        <div class="modal fade" id="actualizar">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="table-responsive-sm">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Descripción</th>
                                        <th>Acción</th>
                                        <th>Prospecto</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arraySeguimiento.length">
                                    <tr v-for="seg in arraySeguimiento" :key="seg.id">
                                        <td v-text="format_date(seg.Fecha)"></td>
                                        <td v-text="seg.Hora"></td> 
                                        <td v-text="seg.Descripcion"></td>
                                        <td v-text="seg.ACCION"></td>
                                        <td v-text="seg.Nombre_Seguimiento"></td>
                                    </tr>
                                </tbody>
                            </table>
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
<script type="application/json" name="fecha-inicio">
        {{ $inicio }}
</script>
<script type="application/json" name="fecha-fin">
        {{ $fin }}
</script>
<script type="application/javascript">
        var json = "{{$id}}";
        var json_inicio = "{{$inicio}}";
        var json_fin = "{{$fin}}";
        var id = JSON.parse(json);
        var inicio = JSON.parse(json_inicio);
        var fin = JSON.parse(json_fin);
</script>
<script >
var XLSX = require('xlsx')
var FileSaver = require('file-saver')
    export default { 
        data(){
            return{
                searchQuery: null,
                arraySeguimiento: [],
                update:0, /*Esta variable contrarolará cuando es una nueva tarea o una modificación, si es 0 significará que no hemos seleccionado
                          ninguna tarea, pero si es diferente de 0 entonces tendrá el id de la tarea y no mostrará el boton guardar sino el modificar*/
                arrayTasks:[], //Este array contendrá las tareas de nuestra bd
                loading: true,
                errored: false
            }
        },
        _computed: {
            'id': function() {
                return window.id;
            },
            'inicio': function() {
                return window.inicio;
            },
            'fin': function() {
                return window.fin;
            }
        },
        computed: {
            filteredResources (){
                if (this.searchQuery) {
                    return this.arrayTasks.filter(item => {
                        return this.searchQuery
                        .toLowerCase()
                        .split(" ")
                        .every(v => item.Descripcion.toLowerCase().includes(v) || item.nombre.toLowerCase().includes(v));
                    });
                } else {
                    return this.arrayTasks;
                }
            }
        },
        methods:{
            format_date(value){
            if (value) {
                return moment(String(value)).format('DD/MM/YYYY')
                }
            },
            getTasks(){
                let me = this;
                let url = url_global+'/datlisdetActiUsFe/'+id +'/'+inicio+ '/'+fin //Ruta que hemos creado para que nos devuelva todas las tareas
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
                axios.get(url_global+'/det_verif/'+task_id
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
                let me =this;
                let det_ID = data.id
                let url = url_global+'/detSegV/'+det_ID;
                axios.get(url).then(function (response) {
                    //creamos un array y guardamos el contenido que nos devuelve el response
                    me.arraySeguimiento = response.data;
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
            },
            downloadExl() {
                let wb = XLSX.utils.table_to_book(document.getElementById('tableId')),
                    wopts = {
                        bookType: 'xlsx',
                        bookSST: false,
                        type: 'binary'
                    },
                    wbout = XLSX.write(wb, wopts);
 
               FileSaver.saveAs(new Blob([this.s2ab(wbout)], {
                    type: "application/octet-stream;charset=utf-8"
                                 }), "Reporte de actividades.xlsx");
            },
            s2ab(s) {
                if (typeof ArrayBuffer !== 'undefind') {
                    var buf = new ArrayBuffer(s.length);
                    var view = new Uint8Array(buf);
                    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                    return buf;
                } else {
                    var buf = new Array(s.length);
                    for (var i = 0; i != s.length; ++i) buf[i] = s.charCodeAt(i) & 0xFF;
                    return buf;
                }
            }
        },
        mounted() {
           this.getTasks();
        }
    }
</script>