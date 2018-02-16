import Vue from 'Vue';
import Vuex from 'vuex';
import { CITY, MAPS_CENTER, MAPS_ZOOM } from './conf.js';
import { apiCall, getMap, getFilmWithVideo, getWikidataEntity } from './api.js';

Vue.use(Vuex);

export default class {
    constructor({ debug = false }) {
        this.debug = debug;
        this.instance = this.getStore();
    }

    getStore() {
        return new Vuex.Store({
            strict : this.debug,

            state : this.getInitialState(),

            mutations : {
                addresses(state, addresses) {
                    Vue.set(state, 'addresses', addresses);
                },

                filmswithvideo(state, films) {
                    Vue.set(state, 'filmswithvideo', films);
                }
            },

            actions : {
                getHome({ commit }) {
                    getMap(CITY).then(d => commit('addresses', d))
                    getFilmWithVideo().then(d => commit('filmswithvideo', d));
                }
            }
        });
    }

    getInstance() {
        return this.instance;
    }

    getInitialState() {
        return {
            addresses : null,
            city : CITY,
            filmswithvideo : null,
            mapCenter : MAPS_CENTER,
            mapZoom : MAPS_ZOOM
        };
    }
};