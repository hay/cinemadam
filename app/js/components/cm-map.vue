<template>
    <div class="gmap">
        <gmap-map
            class="gmap__map"
            v-bind:center="center"
            map-type-id="terrain"
            v-bind:zoom="zoom"
        >
            <gmap-marker
                v-for="(marker, index) in addresses"
                v-on:click="clickMarker(marker)"
                v-bind:key="index"
                v-bind:position="marker.position">
            </gmap-marker>
        </gmap-map>

        <div class="gmap__info"
             v-if="marker">
            <button
                class="gmap__close"
                v-on:click="marker = null">&times;</button>

            <h2 class="gmap__street">
                <a v-bind:href="'#address:' + marker.address_id">
                    {{marker.street_name}}
                </a>
            </h2>

            <p>Deze bioscopen hebben op deze locatie gezeten</p>

            <ul class="gmap__list">
                <li v-for="venue in marker.venues">
                    <a v-bind:href="'#venue:' + venue.venue_id">{{venue.name}}</a>
                    <span v-if="venue.active.length > 0">
                        ({{venue.active[0].date_opened}} - {{venue.active[0].date_closed}})
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import { MAPS_API_KEY } from '../conf.js';
    import * as Maps from 'vue2-google-maps'

    Vue.use(Maps, {
        load: {
            key: MAPS_API_KEY
        }
    });


    export default {
        data() {
            return {
                marker : null
            };
        },

        methods : {
            clickMarker(marker) {
                this.marker = marker;
            }
        },

        props : {
            addresses : Array,
            center : Object,
            zoom : {
                type : Number
            }
        }
    }
</script>