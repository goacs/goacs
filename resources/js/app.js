import CoreuiVue from '@coreui/vue'
import { freeSet as icons } from "@coreui/icons";
import axios from 'axios';
import VueAxios from 'vue-axios'
import vSelect from 'vue-select'
import VueAuth, { DEFAULT_OPTIONS } from '@d0whc3r/vue-auth-plugin';
import VueRouter from 'vue-router';
import {ValidationObserver, ValidationProvider, extend} from 'vee-validate'
import * as rules from 'vee-validate/dist/rules';
import App from "./App.vue";
import Vue from "vue";
import moment from 'moment';
import router from "./router";
import store from "./store";
import errorsMixin from './helpers/errors.mixin'
import JsonTree from 'vue-json-tree'
import Echo from "laravel-echo"

window.Pusher = require('pusher-js');
window.Vue = require('vue').default;

Vue.config.productionTip = false;
Vue.config.silent = process.env.NODE_ENV === 'production';

axios.defaults.baseURL = process.env.VUE_APP_API_URL;
axios.defaults.headers['Authorization'] = 'Bearer {auth_token}';

moment.defaultFormat = "YYYY-MM-DD HH:mm:ss";
Vue.use(CoreuiVue)
Vue.use(VueRouter);
Vue.use(require('vue-moment'), { moment })
Vue.use(VueAxios, axios);
Vue.component('v-select', vSelect)
Vue.component('json-tree', JsonTree)


Object.keys(rules).forEach(rule => {
  extend(rule, rules[rule]);
});

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);
Vue.mixin(errorsMixin);

const authPluginOptions = { ...DEFAULT_OPTIONS,
  ...{
    authRedirect: '/auth/login',
    loginData: {
      url: '/api/auth/login',
      method: 'POST',
      redirect: '/',
      customToken: (response) => response.data['token'],
      fetchUser: false,
      fetchData: (response) => response.data['user'],
    },
  },
  logoutData: {
    url: '/auth/logout',
    method: 'POST',
    redirect: '/auth/login',
    makeRequest: false,
  },
}

Vue.use(VueAuth, authPluginOptions);


window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  wsHost: window.location.hostname,
  wsPort: process.env.MIX_PUSHER_APP_PORT,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  forceTLS: false,
  disableStats: true,
});

const app = new Vue({
  el: '#app',
  render: h => h(App),
  router,
  store,
  icons,
});
