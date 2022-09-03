import Vue from 'vue'
import ProductManagement from './ProductManagement.vue'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

Vue.config.productionTip = false
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
import 'bootstrap/dist/css/bootstrap.min.css';
new Vue({
  render: h => h(ProductManagement),
}).$mount('#ProductManagement')
