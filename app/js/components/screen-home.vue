<template>
    <section class="screen map">
        <cm-menu variation="large"></cm-menu>

        <cm-map
            v-if="addresses"
            v-bind:addresses="addresses"
            v-bind:center="mapCenter"
            v-bind:zoom="mapZoom"></cm-map>

        <h2>Alle films met een video</h2>
        <ul class="map__list" v-if="filmswithvideo">
            <li v-for="film in filmswithvideo">
                <a v-bind:href="'#film:' + film.film_id">
                    {{film.title}}
                </a>
            </li>
        </ul>

        <h2>Verdwenen bioscopen</h2>
        <ul>
            <li>
                <a href="#address:Perc27">Nieuwendijk 186</a>
            </li>

            <li>
                <a href="#address:Perc26">Nieuwendijk 69</a>
            </li>

            <li>
                <a href="#address:Perc2">Nieuwendijk 154</a>
            </li>

            <li>
                <a href="#address:Perc105">Jodenbreestraat 23</a>
            </li>

            <li>
                <a href="#address:Perc48">A. Allebéplein 4</a>
            </li>
        </ul>

        <h2>Alle locaties in {{city}}</h2>
        <ul class="map__list" v-if="addresses">
            <li v-for="address in addresses">
                <a v-bind:href="'#address:' + address.address_id">
                    {{address.street_name}}
                </a>
            </li>
        </ul>
    </section>
</template>

<script>
    import CmMap from './cm-map.vue';
    import CmMenu from './cm-menu.vue';
    import { CITY, MAPS_CENTER, MAPS_ZOOM } from '../conf.js';
    import { getMap } from '../api.js';
    import { getJson } from '../util.js';

    export default {
        name : 'ScreenHome',

        created() {
            getMap(CITY).then(d => this.addresses = d);
            getJson('api/film/_videos').then(d => this.filmswithvideo = d);
        },

        components : {
            CmMap,
            CmMenu
        },

        data() {
            return {
                addresses : null,
                city : CITY,
                filmswithvideo : null,
                mapCenter : MAPS_CENTER,
                mapZoom : MAPS_ZOOM
            };
        }
    }
</script>