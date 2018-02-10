import Vue from 'vue';
import { MAPS_API_KEY } from './conf.js';
import * as Maps from 'vue2-google-maps'

const DEFAULT_SCREEN = 'map';

Vue.use(Maps, {
    load: {
        key: MAPS_API_KEY
    }
});

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
                clickMarker(marker) {
                    this.mapCenter = marker.position;
                    this.marker = marker;
                    // window.location.hash = `address:${marker.address_id}`;
                },

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
                mapCenter : { lat : 52.3710755 , lng: 4.8840252},
                marker : {},
                screen : 'map',
                venue : {}
            }
        });
    }
}