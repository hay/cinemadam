import { getJson } from './util.js';

export default class Model {
    constructor() {
        this.data = {};
    }

    apiCall(method, id) {
        return new Promise((resolve, reject) => {
            getJson(`api/${method}/${id}`).then((data) => {
                resolve(data);
            })
        });
    }

    getMap(city) {
        return new Promise((resolve, reject) => {
            getJson(`api/address/?city=${city}`).then((data) => {
                data = data.map((a) => {
                    const pos = a.geodata.split(',');

                    a.position = {
                        lat : Number(pos[0]),
                        lng : Number(pos[1])
                    };

                    return a;
                });

                resolve(data);
            });
        });
    }

    getWikidataEntity(qid) {
        return new Promise((resolve, reject) => {
            const url = `https://api.haykranen.nl/wikidata/entity?q=${qid}`;
            getJson(url).then((data) => {
                resolve(data.response[data.params.q]);
            });
        });
    }
};