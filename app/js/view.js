import Vue from 'vue';
import { MAPS_API_KEY } from './conf.js';
import * as Maps from 'vue2-google-maps'
import CmFooter from '../components/cm-footer.vue';
import CmMap from '../components/cm-map.vue';
import CmMenu from '../components/cm-menu.vue';

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

                model.getFilmWithVideo().then((data) => {
                    this.filmwithvideo = data;
                });
            },

            components : {
                CmFooter,
                CmMap,
                CmMenu
            },

            methods : {
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
                },

                populateWikidata(qid) {
                    model.getWikidataEntity(qid).then((data) => {
                        this.wikidata = data;

                        if (data.image) {
                            data.imageUrl = data.image.full.replace('Special:Redirect/file/', 'File:');
                        }

                        data.claims.forEach((claim) => {
                            if (claim.property_id === 'P10') {
                                this.video = claim.values[0];
                                this.video.src = `https://commons.wikimedia.org/wiki/Special:Redirect/file/${this.video.value}`;
                            }
                        });
                    });
                }
            },

            watch : {
                'screen' : function(screen) {
                    if (screen === 'address' && this.address.links.wikidata_id) {
                        const qid = this.address.links.wikidata_id;
                        this.populateWikidata(qid);
                    }

                    if (screen === 'film' && this.film.wikidata) {
                        this.populateWikidata(this.film.wikidata);
                    }
                }
            },

            data : {
                address : {},
                addresses : [],
                city : 'Amsterdam',
                film : {},
                filmwithvideo : [],
                mapCenter : { lat : 52.3710755 , lng: 4.8840252},
                marker : {},
                screen : 'map',
                venue : {},
                video : null,
                wikidata : {}
            }
        });
    }
}