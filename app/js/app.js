import Vue from 'vue';
import router from './router.js';
import CmFooter from './components/cm-footer.vue';
import './filters.js';

window.__app__ = new Vue({
    el : "main",

    router,

    components : {
        CmFooter
    }
});