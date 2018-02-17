<template>
    <div class="cm-toggler"
         v-bind:open="open">
        <button class="cm-toggler__text"
                v-on:click="toggle">
            {{text}}
            <span class="cm-toggler__button">🔼</span>
        </button>

        <div class="cm-toggler__content">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        methods : {
            toggle() {
                this.open = !this.open;
            }
        },

        data() {
            return {
                open : false
            };
        },

        props : {
            text : String
        }
    }
</script>