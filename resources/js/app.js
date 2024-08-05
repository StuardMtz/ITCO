
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueGoodTablePlugin from 'vue-good-table';

// import the styles
import 'vue-good-table/dist/vue-good-table.css'

Vue.use(VueGoodTablePlugin);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('verificar-component', require('./components/verificarTransferenciaComponent.vue'));
Vue.component('agregar-component', require('./components/AgregarComponent.vue'));
Vue.component('sucursal-component', require('./components/SucursalComponent.vue'));
Vue.component('agregarsucursal-component', require('./components/AgregarSucursalComponent.vue'));
Vue.component('plantagregar-component',require('./components/agregarPlantaComponent.vue'));
Vue.component('productostransferencia-component',require('./components/editarTransferenciaComponent.vue'));
Vue.component('productossucursales-component',require('./components/productosSucursalesComponent.vue'));
Vue.component('editarcomprastransferencia-component',require('./components/editarTransCompras.vue'));
Vue.component('verificarcomprastransferencia-component',require('./components/verificarTransCompras.vue'));
Vue.component('actividades-component',require('./components/actividades.vue'));
Vue.component('editarcotizacion-component',require('./components/editarCotizacion.vue'));
Vue.component('editarliquidacion-component',require('./components/editarLiquidacion.vue'));
Vue.component('detalleactividades-component',require('./components/detalleActividades.vue'));
Vue.component('detalleactividadesfecha-component',require('./components/detalleActividadesfecha.vue'));
Vue.component('inventario-component',require('./components/inventario.vue'));
Vue.component('cuadrado-component',require('./components/inventarioCuadrado.vue'));

const app = new Vue({
    el: '#app'
});
