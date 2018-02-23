<template>
    <section class="screen address">
        <cm-menu></cm-menu>

        <template v-if="address">
            <h2 class="address__title">
                Alle bioscopen op {{address.street_name | trim}}, {{city}}
            </h2>

            <figure class="address__image"
                    v-if="image">
                <img v-bind:src="image.full" />

                <figcaption>
                    Via <a target="_blank" v-bind:href="image.imageUrl">Wikimedia Commons</a>
                </figcaption>
            </figure>

            <p v-if="address.links && address.links.vgebouwen_id">
                Dit is een <a target="_blank" v-bind:href="'http://verdwenengebouwen.nl/gebouw/' + address.links.vgebouwen_id">
                verdwenen gebouw</a>
            </p>

            <ul class="address_list">
                <li v-for="venue in address.venues">
                    <a v-bind:href="'#/venue/' + venue.venue_id">{{venue.name}}</a>
                </li>
            </ul>
        </template>
    </section>
</template>

<script>
    import CmMenu from './cm-menu.vue';
    import { CITY } from '../conf.js';
    import { apiCall, getWikidataEntity } from '../api.js';

    export default {
        name : 'ScreenAddress',

        components : {
            CmMenu
        },

        created() {
            apiCall('address', this.$route.params.address).then((a) => {
                this.address = a;

                if (a.links && a.links.wikidata_id) {
                    const qid = a.links.wikidata_id;
                    getWikidataEntity(qid).then(i => this.image = i.image);
                }
            });
        },

        data() {
            return {
                address : null,
                city : CITY,
                image : null
            }
        }
    }
</script>