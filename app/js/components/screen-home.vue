<template>
    <section class="screen map">
        <cm-map
            v-bind:addresses="addresses"
            v-bind:center="mapCenter"
            v-bind:zoom="13"></cm-map>

        <h2>Alle films met een video</h2>
        <ul class="map__list" v-if="filmwithvideo">
            <li v-for="film in filmwithvideo">
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
        <ul class="map__list" v-show="addresses.length">
            <li v-for="address in addresses">
                <a v-bind:href="'#address:' + address.address_id">
                    {{address.street_name}}
                </a>
            </li>
        </ul>
    </section>
</template>

<script>
    import { mapState } from 'vuex';
    import CmMap from '../components/cm-map.vue';

    export default {
        name : 'ScreenHome',

        components : {
            CmMap
        },

        computed : Object.assign(
            mapState([
                'addresses',
                'filmswithvideo',
                'mapCenter',
                'mapZoom'
            ])
        )
    }
</script>