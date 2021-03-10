import CoreuiVue from '@coreui/vue'
import { freeSet as icons } from "@coreui/icons";
import axios from 'axios';
import VueAxios from 'vue-axios'
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

window.Vue = require('vue').default;

Vue.config.productionTip = false;

axios.defaults.baseURL = process.env.VUE_APP_API_URL;
axios.defaults.headers['Authorization'] = 'Bearer {auth_token}';

moment.defaultFormat = "YYYY-MM-DD HH:mm:ss";
Vue.use(CoreuiVue)
Vue.use(VueRouter);
Vue.use(require('vue-moment'), { moment })
Vue.use(VueAxios, axios);

Object.keys(rules).forEach(rule => {
  extend(rule, rules[rule]);
});


Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);
Vue.mixin(errorsMixin);

const authPluginOptions = { ...DEFAULT_OPTIONS,
  ...{
    authRedirect: '/api/auth/login',
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
    url: '/api/auth/logout',
    method: 'POST',
    redirect: '/api/auth/login',
    makeRequest: false,
  },
}

Vue.use(VueAuth, authPluginOptions);

const app = new Vue({
  el: '#app',
  render: h => h(App),
  router,
  store,
  icons,
});
