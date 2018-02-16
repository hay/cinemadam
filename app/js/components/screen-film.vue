<template>
    <section class="screen film">
        <h2 class="film__title">{{film.title}}</h2>

        <p v-if="wikidata.descriptions">{{wikidata.descriptions}}</p>

        <video v-if="video"
               class="film__video"
               v-bind:src="video.src"
               v-bind:poster="video.image.thumb"
               controls></video>

        <ul class="film__details">
            <li v-if="film.film_year">Jaar: {{film.film_year}}</li>
            <li v-if="film.country">Land: {{film.country}}</li>
            <li v-if="film.film_length">Lengte: {{film.film_length}}</li>
            <li v-if="film.film_gauge">Filmtype: {{film.film_gauge}}</li>
            <li v-if="film.imdb">
                <a target="_blank"
                   v-bind:href="'http://www.imdb.com/title/tt' + film.imdb">
                   IMDB
                </a>
            </li>
            <li v-if="film.wikidata">
                Via <a target="_blank"
                       v-bind:href="'https://www.wikidata.org/wiki/' + film.wikidata">
                    Wikidata</a>
            </li>
        </ul>

        <h3>Bioscopen waar deze film is gedraaid</h3>
        <ul class="film__venues">
            <li v-for="venue in film.venues">
                <a v-bind:href="'#venue:' + venue.venue_id">
                    {{venue.name}} ({{venue.address.city_name}})
                </a>
            </li>
        </ul>

        <h2>Filmkeuring</h2>
        <ul>
            <li v-for="c in film.censorship">
                {{c.censorship_date}}: {{c.rating}}, {{c.comment_by_censor}},
                via
                <a target="_blank"
                   v-bind:href="'http://proxy.handle.net/10648/' + c.source[0].info">Nationaal Archief</a>
            </li>
        </ul>
    </section>
</template>

<script>
    export default {
        name : 'ScreenFilm'
    }
</script>