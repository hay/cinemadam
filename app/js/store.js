import Vue from 'Vue';
import Vuex from 'vuex';
import { CITY, MAPS_CENTER, MAPS_ZOOM } from './conf.js';
import { apiCall, getMap, getFilmWithVideo, getWikidataEntity } from './api.js';

Vue.use(Vuex);

export default class {
    constructor({ model, debug = false }) {
        this.model = model;
        this.debug = debug;
        this.instance = this.getStore();
    }

    getStore() {
        return new Vuex.Store({
            strict : this.debug,

            state : this.getInitialState(),

            mutations : {
                addresses(state, addresses) {
                    state.addresses = addresses;
                }
            },

            actions : {
                loadData({ state, commit }, { name, params }) {
                    if (name === 'home') {
                        return new Promise((resolve, reject) => {
                            getMap(CITY).then((data) => {
                                commit('addresses', data);
                                resolve();
                            });
                        });
                    }
                }
            }
        });
    }

    getInstance() {
        return this.instance;
    }

    getInitialState() {
        return {
            addresses : [],
            city : CITY,
            filmswithvideo : [],
            mapCenter : MAPS_CENTER,
            mapZoom : MAPS_ZOOM,
            model : this.model
        };
    }
};