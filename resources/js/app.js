import Vue from 'vue';
import App from './App.vue';
import './registerServiceWorker';
import router from './router';
import store from './store';
import VueI18n from 'vue-i18n';

Vue.config.productionTip = false;

import messages from '@kirschbaum-development/laravel-translations-loader!@kirschbaum-development/laravel-translations-loader';

console.log(messages);

Vue.use(VueI18n);

// Create VueI18n instance with options
const i18n = new VueI18n({
    locale: 'es', // set locale
    messages, // set locale messages
  })

new Vue({
    router,
    store,
    i18n,
    render: h => h(App)
}).$mount('#app');
