<template>
    <div class="container-fluid">
        <section v-if="errored">
            <div class="alert alert-danger" role="alert">
                Parece que algo fallo, refresca la página para solucionarlo...!
            </div>
        </section>
        <button class="btn btn-sm btn-success" @click="downloadExl()"> Exportar a excel</button>
        <div class="progress">
            <div class="progress-bar bg-info" role="progressbar" :style="{width: porcentaje + '%'}"  aria-valuemin="0" aria-valuemax="100">{{ porcentaje }} %</div>
        </div>
        <div class="table-responsive-sm" id="tableId">
            <vue-good-table
                :columns="columns"
                :rows="datProductos"
                max-height="700px"
                :fixed-header="false"
                @on-row-click="cargarDetallesProducto" @on-cell-click="historialProducto" 
                styleClass="vgt-table condensed"
                :search-options="{
                    placeholder: 'Buscar productos',
                    enabled: true
                }">
            </vue-good-table>
        </div>    
        <div class="modal fade" id="create">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">Cerrar &times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="editarExistencia-tab" data-toggle="tab" data-target="#editarExistencia" type="button" role="tab" 
                                aria-controls="editarExistencia" aria-selected="true">Editar existencia</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="verHistorial-tab" data-toggle="tab" data-target="#verHistorial" type="button" role="tab" 
                                aria-controls="verHistorial" aria-selected="false">Historial conteo</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="editarExistencia" role="tabpanel" aria-labelledby="editarExistencia-tab">
                                <div class="table table-responsive-sm">
                                    <table class="table table-sm table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Kardex</th>
                                                <th>Existencia teorica</th>
                                                <th>Existencia fisica</th>
                                                <th>Mal estado</th>
                                                <th>Diferencia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td v-text="nombre_corto+' '+nombre_fiscal"></td>
                                                <td v-text="parseInt(existencia_real)"></td>
                                                <td v-text="parseInt(existencia_teorica)"></td>
                                                <td v-text="parseInt(existencia_fisica)"></td>
                                                <td v-text="parseInt(mal_estado)"></td>
                                                <td v-text="parseInt(existencia_fisica - existencia_teorica)"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <form v-on:keyup.enter="submit" id="actualizar_Producto">
                                    <div class="form-row">
                                        <input class="form-control" placeholder="0" v-model="existencia_real_g" type="number" step="1" required style="display: none;">
                                        <div class="form-group col-md-4"><!-- Formulario para la creación o modificación de nuestras tareas-->
                                            <label>Existencia fisica</label>
                                            <input class="form-control" v-model="existencia_fisica_g" type="number" step="1" placeholder="0" required>
                                        </div>
                                        <div class="form-group col-md-4"><!-- Formulario para la creación o modificación de nuestras tareas-->
                                            <label>Observaciones</label>
                                            <input class="form-control" v-model="observaciones" type="text" placeholder="Puede ingresar una observación" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Marcar como dañado</label>
                                            <br>
                                            <input class="form-control" type="radio" v-model="mal_estado_g" id="inlineRadio1" placeholder="0" value="2">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <button @click="guardaExistencia()" class="btn btn-success btn-sm btn-block" data-dismiss="modal"><i class="fas fa-save"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="verHistorial" role="tabpanel" aria-labelledby="verHistorial-tab">
                                <div class="table-responsive-sm">
                                    <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Teorico</th>
                                            <th>Fisico</th>
                                            <th>Diferencia</th>
                                            <th>Detalle</th>
                                            <th>Mal estado</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="his in hisProducto" :key="his.id">
                                            <td v-text="his.nombre_corto+''+his.nombre_fiscal"></td>
                                            <td v-text="parseInt(his.existencia,0)"></td>
                                            <td v-text="parseInt(his.existencia_fisica)"></td>
                                            <td v-text="parseInt(his.diferencia)"></td>
                                            <td v-text="his.descripcion"></td>
                                            <td v-text="his.mal_estado"></td>
                                            <td v-text="format_date(his.created_at)"></td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>          
</template>
<script type="application/json" name="server-data">
       {{ $pro }}
</script>

<script>
var XLSX = require('xlsx')
var FileSaver = require('file-saver')
export default { 
    data(){
        return{
            searchQuery: null,
            message: "",
            nombre_corto:'',
            nombre_fiscal:'',
            existencia_fisica:0,
            existencia_fisica_g:0,
            existencia_real:0,
            existencia_real_g:0,
            existencia_teorica:0,
            mal_estado:0,
            mal_estado_g:'',
            observaciones:'',
            no_rollo:0,
            cor_existencia_real:0,
            cor_existencia_teorica:0,
            chatarra:0,
            maquina:0,
            operario:0,
            mal_estado:0,
            update:0, /*Esta variable contrarolará cuando es una nueva tarea o una modificación, si es 0 significará que no hemos seleccionado
                ninguna tarea, pero si es diferente de 0 entonces tendrá el id de la tarea y no mostrará el boton guardar sino el modificar*/
            detProducto:[], //Este array contendrá las tareas de nuestra bd
            datProductos:[],
            hisProducto:[],
            arrayResumen:[],
            arrayMaquina:[],
            arrayOperarios:[],
            arrayHistorial:[],
            loading: true,
            errored: false,
            porcentaje: 0,
            
            columns: [
            {
                    label: 'Opciones',
                    field: 'btn',
                    html: 'true',
                    sortable: false,
                },
                {
                    label: 'Categoria',
                    field: 'categoria',
                    sortable: true,
                    firstSortType: 'asc'
                },
                {
                    label: 'Código',
                    field: 'nombre_corto',
                },
                {
                    label: 'Nombre',
                    field: 'nombre_fiscal',
                },
                {
                    label: 'Teorico',
                    field: 'existencia_teorica',
                    type: 'number',
                },
                {
                    label: 'Fisico',
                    field: 'existencia_fisica',
                    type: 'number',
                },
                {
                    label: 'Diferencia',
                    field: 'diferencias',
                    type: 'number',
                },
                {
                    label: 'Dañado',
                    field: 'mal_estado',
                    type: 'number',
                }
            ]
        }
    },
    computed: {
        'server_data': function() {
            return window.server_data;
        }
    },
    computed: {
        totalp: function(){
            let totalp = 0;
            Object.values(this.detProducto).forEach(
                (item)=>(totalp += parseFloat(item.existencia)),     
            );
            return totalp.toFixed(2);
        },
        vtotal: function(){
            let vtotal = 0;
            Object.values(this.detProducto).forEach(
                (vol)=>(vtotal += parseFloat(vol.cortes_existencia_teorica))
            );
            return vtotal;
        },
        total: function(){
            let total = 0;
            Object.values(this.detProducto).forEach(
                (vol)=>(total += parseFloat(vol.cortes_existencia_real))
            );
            return total;
        },
        filteredResources (){
            if(this.searchQuery){
                return this.datProductos.filter((item)=>{
                    return item.nombre_corto.startsWith(this.searchQuery) || item.nombre_fiscal.startsWith(this.searchQuery);
                })
            }else{
                return this.datProductos;
            }
        }
    },
    methods:{
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD/MM/YYYY hh:mm')
            }
        },
        getProductos()
        {
            let me=this;
            let url = url_global+'/dat_inve/'+ server_data
            axios.get(url).then(function(response)
            {
                me.datProductos = response.data;
            })
            .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
        guardaExistencia(){/*Esta funcion, es igual que la anterior, solo que tambien envia la variable update que contiene el id de la
            tarea que queremos modificar*/
            let me = this;
            let params = 0;
            axios.put(url_global+'/gu_expro',{
                'id':this.id_dato,
                'existencia_real_g':this.existencia_real,
                'mal_estado_g':this.mal_estado_g,
                'existencia_fisica_g':this.existencia_fisica_g,
                'observaciones':this.observaciones,
            }).then((response) => {
                me.limpiarCampos();
                me.getProductos();
                me.cargarDetallesProductoUp(params = this.id_dato);
                me.historialProductoUp(params = this.id_dato);
                me.porcentajeInve();
              })
              .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

        cargarDetallesProducto(params){ //Esta función rellena los campos y la variable update, con la tarea que queremos modificar
            this.id_dato = params.row.id
            let me =this;
            let url = url_global+'/det_pro/'+ this.id_dato  //Ruta que hemos creado para que nos devuelva todas las tareas
            axios.get(url).then(function (response) {//creamos un array y guardamos el contenido que nos devuelve el response
                me.nombre_corto = response.data.nombre_corto;
                me.nombre_fiscal = response.data.nombre_fiscal;
                me.existencia_real = response.data.existencia_real;
                me.existencia_teorica = response.data.existencia_teorica;
                me.existencia_fisica = response.data.existencia_fisica;
                me.mal_estado = response.data.mal_estado;
            })
            .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
        cargarDetallesProductoUp(params){ //Esta función rellena los campos y la variable update, con la tarea que queremos modificar
            this.id_dato = params
            let me =this;
            let url = url_global+'/det_pro/'+ this.id_dato  //Ruta que hemos creado para que nos devuelva todas las tareas
            axios.get(url).then(function (response) {//creamos un array y guardamos el contenido que nos devuelve el response
                me.nombre_corto = response.data.nombre_corto;
                me.nombre_fiscal = response.data.nombre_fiscal;
                me.existencia_real = response.data.existencia_real;
                me.existencia_teorica = response.data.existencia_teorica;
                me.existencia_fisica = response.data.existencia_fisica;
                me.mal_estado = response.data.mal_estado;
            })
            .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
        historialProducto(params)
        {
            this.id_dato = params.row.id
            let me =this;
            let url = url_global+'/his_prod/'+ this.id_dato
            axios.get(url).then(function(response)
            {
                me.hisProducto = response.data;
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            })
            .finally(() => this.loading = false)
        },
        historialProductoUp(params)
        {
            this.id_dato = params
            let me =this;
            let url = url_global+'/his_prod/'+ this.id_dato
            axios.get(url).then(function(response)
            {
                me.hisProducto = response.data;
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            })
            .finally(() => this.loading = false)
        },
        porcentajeInve()
        {
            let me =this;
            let url = url_global+'/porce_inve/'+ server_data
            axios.get(url).then(function(response)
            {
                me.porcentaje = response.data;
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            })
            .finally(() => this.loading = false)
        },
        limpiarCampos(){
            this.existencia_real_g        = '';
            this.mal_estado_g           = '';
            this.existencia_fisica_g    = '';
            this.observaciones          = '';
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
            }), "Inventario.xlsx");
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
        this.getProductos();
        this.porcentajeInve();
    }
}
</script>