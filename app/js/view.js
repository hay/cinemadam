import Vue from 'vue';

const DEFAULT_SCREEN = 'map';

export default class View {
    constructor(model) {
        this.setup(model);
    }

    setup(model) {
        this.view = new Vue({
            el : "main",

            mounted() {
                window.addEventListener('hashchange', this.go.bind(this));
                this.go();
            },

            methods : {
                getData(path) {
                    model.apiCall(path).then((data) => {
                        console.log(data);
                    });
                },

                go() {
                    const path = window.location.hash.slice(1);
                    const parts = path.split(':');
                    const screen = parts[0];
                    const id = parts[1];

                    if (!screen || screen === DEFAULT_SCREEN) {
                        model.getMap(this.city).then((data) => {
                            this.addresses = data;
                            this.screen = DEFAULT_SCREEN;
                        });
                    } else {
                        model.apiCall(screen, id).then((data) => {
                            this.screen = screen;
                            this[screen] = data;
                        });
                    }

                    window.scrollTo(0, 0);
                }
            },

            data : {
                address : {},
                addresses : [],
                city : 'Amsterdam',
                film : {},
                screen : 'map',
                venue : {}
            }
        });
    }
}