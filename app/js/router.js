import Vue from 'vue';
import VueRouter from 'vue-router';
import ScreenAddress from './components/screen-address.vue';
import ScreenFilm from './components/screen-film.vue';
import ScreenHome from './components/screen-home.vue';
import ScreenVenue from './components/screen-venue.vue';

Vue.use(VueRouter);

export default new VueRouter({
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