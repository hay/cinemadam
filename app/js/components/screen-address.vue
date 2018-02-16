<template>
    <section class="screen address">
        <h2 class="address__title">Alle bioscopen op {{address.street_name}}</h2>

        <figure class="address__image"
                v-if="wikidata && wikidata.image">
            <img v-bind:src="wikidata.image.full" />
            <figcaption>Via <a target="_blank" v-bind:href="wikidata.imageUrl">Wikimedia Commons</a></figcaption>
        </figure>

        <p v-if="address.links && address.links.vgebouwen_id">
            Dit is een <a target="_blank" v-bind:href="'http://verdwenengebouwen.nl/gebouw/' + address.links.vgebouwen_id">
            verdwenen gebouw</a>
        </p>

        <ul class="address_list">
            <li v-for="venue in address.venues">
                <a v-bind:href="'#venue:' + venue.venue_id">{{venue.name}}</a>
            </li>
        </ul>
    </section>
</template>

<script>
    export default {
        name : 'ScreenAddress'
    }
</script>