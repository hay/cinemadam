<template>
    <section class="screen venue">
        <h2 class="venue__title">{{venue.name}}</h2>
        <p class="venue__lead">{{venue.info}}</p>

        <ul>
            <li v-if="venue.venue_type">Type: {{venue.venue_type}}</li>
            <li v-if="venue.address">
                <a v-bind:href="'#address:' + venue.address.address_id">
                    {{venue.address.street_name}}
                </a>
            </li>
        </ul>

        <h3>Films gedraaid in {{venue.name}}</h3>
        <ul class="venue__programmes">
            <li v-for="programme in venue.programmes">
                <a v-if="programme.film"
                   v-bind:href="'#film:' + programme.film.film_id">
                   <span v-if="programme.film.hasvideo">🎞</span>
                    {{programme.film.title}} ({{programme.date.programme_date}})
                </a>
            </li>
        </ul>
    </section>
</template>

<script>
    export default {
        name : 'ScreenVenue'
    }
</script>