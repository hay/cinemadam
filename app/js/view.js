import Vue from 'vue';

export default class View {
    constructor(model) {
        this.setup();
    }

    setup(data) {
        this.view = new Vue({
            el : "main",

            data : {

            }
        });
    }
}