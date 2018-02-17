import Vue from 'vue';
import router from './router.js';
import CmFooter from './components/cm-footer.vue';
import './filters.js';

export default class View {
    constructor(store) {
        this.setup(store);
    }

    setup(store) {
        this.view = new Vue({
            el : "main",

            router,

            store,

            components : {
                CmFooter
            }
        });
    }
}