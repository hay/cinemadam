<template>
    <menu v-bind:class="'cm-menu cm-menu--' + variation">
        <template v-if="variation === 'normal'">
            <h1 class="cm-menu--normal__title">
                <a href="#/">Cinémadam</a>
            </h1>
        </template>

        <template v-if="variation === 'large'">
            <img src="img/logo.jpg" class="cm-menu--large__logo" />

            <a href="#/" class="cm-menu--large__title">Cinémadam</a>

            <h2 class="cm-menu--large__lead">
                Een overzicht van alle historische bioscopen in {{city}} op
                basis van de data van <strong><a href="http://cinemacontext.nl/">Cinema Context</a></strong>.
            </h2>
        </template>
    </menu>
</template>

<script>
    import { CITY } from '../conf.js';

    export default {
        data() {
            return {
                city : CITY
            }
        },

        props : {
            variation : {
                type : String,
                validator: (val) => ['normal', 'large'].includes(val),
                default : 'normal'
            }
        }
    }
</script>