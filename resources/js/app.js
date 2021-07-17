require('./bootstrap');

window.Vue = require('vue').default;

import vuetify from './vuetify';

import AxiosWrapper from 'sb-axios-wrapper'

import Notifications from 'vue-notification'
Vue.mixin(AxiosWrapper)
Vue.use(Notifications)

Vue.component('lesson-list', require('./components/LessonListComponent.vue').default);
Vue.component('lesson-component', require('./components/LessonComponent.vue').default);
Vue.component('notification', require('./components/NotificationComponent.vue').default);


const app = new Vue({
    el: '#app',
    vuetify
});

app.$root.$on('notify',(options) => {
    app.$notify({
        type: options.status,
        text: options.message
    })
})
