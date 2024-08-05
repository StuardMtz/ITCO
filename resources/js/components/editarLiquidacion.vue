<template>
    <div class="container-fluid container-gastos">
        <section v-if="errored">
            <div class="alert alert-danger" role="alert">
                Parece que algo fallo, refresca la página para solucionarlo...!
            </div>
        </section>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-sm" v-if="arrayGastos.length" >
                    <thead>
                        <tr>
                            <th colspan="5" style="text-align: center;">Gastos a liquidar</th>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <th>Documento</th>
                            <th>Proveedor</th> 
                            <th>Descripción</th>
                            <th>Monto</th> 
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in arrayGastos" :key="item.id" @click="eliminarGasto(item)" class="table-success">
                            <td v-text="format_date(item.fecha_documento)"></td>
                            <td v-text="item.no_documento"></td>
                            <td v-text="item.proveedor"></td>
                            <td v-text="item.descripcion"></td> 
                            <td v-text="item.monto"></td> 
                        </tr>
                    </tbody> 
                </table>
            </div>
        </div>

        <hr>
        <div class="row">
            <input class="form-control" type="text" v-model="searchQueryGastospen" placeholder="Buscar gastos">
            <div class="table-responsive">
                <table class="table table-sm" v-if="arrayGastospen.length" >
                    <thead>
                        <tr class="bg-danger">
                            <th colspan="5" style="text-align: center;">Todos los gastos pendientes de liquidar</th>
                        </tr>
                        <tr class="bg-danger">
                            <th>Fecha</th>
                            <th>Documento</th>
                            <th>Proveedor</th> 
                            <th>Descripción</th>
                            <th>Monto</th> 
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pend in filteredResourcespen" :key="pend.id" @click="agregarGasto(pend)" class="table-danger">
                            <td v-text="format_date(pend.fecha_documento)"></td>
                            <td v-text="pend.no_documento"></td>
                            <td v-text="pend.proveedor"></td>
                            <td v-text="pend.descripcion"></td> 
                            <td v-text="pend.monto"></td> 
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
                searchQueryGastospen: null,
                arrayGastos:[], //Este array contendrá los gastos que estáran dentro de la cotización
                arrayGastospen:[],//Este array contendrá los gastos que estáran pendientes de agregar a la cotización
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
                    return this.arrayGastos.filter((item)=>{
                        return item.no_documento.startsWith(this.searchQuery) || item.proveedor.startsWith(this.searchQuery);
                    })
                }else{
                    return this.arrayGastos;
                }
            }
        },

        computed: {
            filteredResourcespen (){
                if(this.searchQueryGastospen){
                    return this.arrayGastospen.filter((item)=>{
                        return item.no_documento.startsWith(this.searchQueryGastospen) || item.proveedor.startsWith(this.searchQueryGastospen);
                    })
                }else{
                    return this.arrayGastospen;
                }
            }
        },

        methods:{
            format_date(value){
            if (value) {
                return moment(String(value)).format('DD/MM/YYYY')
                }
            },

            getGastos(){
                let me =this;
                let url = url_global+'/lis_gasliq/'+ server_data  //Ruta que hemos creado para que nos devuelva todas las tareas
                axios.get(url).then(function (response) {
                    //creamos un array y guardamos el contenido que nos devuelve el response
                    me.arrayGastos = response.data;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            getGastospen(){
                let me =this;
                let url = url_global+'/lis_gastliqp' //Ruta que hemos creado para que nos devuelva todas las tareas
                axios.get(url).then(function (response) {
                    //creamos un array y guardamos el contenido que nos devuelve el response
                    me.arrayGastospen = response.data;
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            agregarGasto(data){//Esta nos abrirá un alert de javascript y si aceptamos borrará la tarea que hemos elegido
                let me = this;
                let gasto_id = data.id
                axios.get(url_global+'/agre_gas_li/'+gasto_id+'/'+server_data
                    ).then(function (response) {
                        me.getGastos();
                        me.getGastospen();
                    })
                    .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },

            eliminarGasto(data){//Esta nos abrirá un alert de javascript y si aceptamos borrará la tarea que hemos elegido
                let me = this;
                let gasto_id = data.id
                axios.get(url_global+'/eli_gas_li/'+gasto_id
                    ).then(function (response) {
                        me.getGastos();
                        me.getGastospen();
                    })
                    .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => this.loading = false)
            },
        },
        mounted() {
           this.getGastos();
           this.getGastospen();
        }
    }
</script>