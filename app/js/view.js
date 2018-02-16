import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import CmFooter from './components/cm-footer.vue';
import CmMenu from './components/cm-menu.vue';
import ScreenAddress from './components/screen-address.vue';
import ScreenFilm from './components/screen-film.vue';
import ScreenHome from './components/screen-home.vue';
import ScreenVenue from './components/screen-venue.vue';

const DEFAULT_SCREEN = 'map';

Vue.use(Vuex);
Vue.use(VueRouter);

const router = new VueRouter({
    routes : [
        {
            name : 'home',
            path : '/',
            component : ScreenHome
        },
        {
            name : 'address',
            path : '/address/:address',
            component : ScreenAddress
        },
        {
            name : 'film',
            path : '/film/:film',
            component : ScreenFilm
        },
        {
            name : 'venue',
            path : '/venue/:venue',
            component : ScreenVenue
        }
    ]
});

export default class View {
    constructor(store) {
        router.beforeEach((to, from, next) => {
            store.dispatch('loadData', to).then(next);
        });

        this.setup(store);
    }

    setup(store) {
        this.view = new Vue({
            el : "main",

            router,

            store,

            components : {
                CmFooter,
                CmMenu
            },

            methods : {
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
                'state' : function(state) {
                    console.log(state);
                },

                'screen' : function(screen) {
                    if (screen === 'address' && this.address.links.wikidata_id) {
                        const qid = this.address.links.wikidata_id;
                        this.populateWikidata(qid);
                    }

                    if (screen === 'film' && this.film.wikidata) {
                        this.populateWikidata(this.film.wikidata);
                    }
                }
            }
        });
    }
}