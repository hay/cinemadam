import Vue from 'vue';
import VueRouter from 'vue-router';
import CmFooter from './components/cm-footer.vue';
import ScreenAddress from './components/screen-address.vue';
import ScreenFilm from './components/screen-film.vue';
import ScreenHome from './components/screen-home.vue';
import ScreenVenue from './components/screen-venue.vue';
import './filters.js';

Vue.use(VueRouter);

const router = new VueRouter({
    routes : [
        {
            name : 'home',
            path : '/',
            component : ScreenHome
        },
        {
            name : 'address',
            path : '/address/:address',
            component : ScreenAddress
        },
        {
            name : 'film',
            path : '/film/:film',
            component : ScreenFilm
        },
        {
            name : 'venue',
            path : '/venue/:venue',
            component : ScreenVenue
        }
    ]
});

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