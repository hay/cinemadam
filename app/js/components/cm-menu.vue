<template>
    <menu class="menu">
        <template v-if="variation === 'normal'">
            <h1 class="menu__title">
                <a href="#map">Cinémadam</a>
            </h1>
        </template>

        <template v-if="variation === 'large'">
            <img src="img/logo.jpg" class="map__logo" />

            <h1 class="map__title">Cinémadam</h1>

            <h2 class="map__lead">
                Een overzicht van alle historische bioscopen in {{city}} op
                basis van de data van <strong><a href="http://cinemacontext.nl/">Cinema Context</a></strong>.
            </h2>
        </template>
    </menu>
</template>

<script>
    export default {
        props : {
            variation : {
                type : String,
                validator: (val) => ['normal', 'large'].includes(val),
                default : 'normal'
            }
        }
    }
</script>