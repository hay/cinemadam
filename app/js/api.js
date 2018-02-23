import { getJson } from './util.js';

export function apiCall(method, argument = null) {
    let url = `api/${method}`;

    if (argument) {
        url = `${url}/${argument}`;
    }

    return getJson(url);
}

export function getMap(city) {
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

export function getWikidataEntity(qid) {
    return new Promise((resolve, reject) => {
        const url = `https://api.haykranen.nl/wikidata/entity?q=${qid}`;

        getJson(url).then((data) => {
            const entity = data.response[data.params.q];
            let image, video;

            if (entity.image) {
                image = entity.image;
                image.imageUrl = entity.image.full.replace('Special:Redirect/file/', 'File:');
            }

            entity.claims.forEach((claim) => {
                if (claim.property_id === 'P10') {
                    video = claim.values[0];
                    video.src = `https://commons.wikimedia.org/wiki/Special:Redirect/file/${video.value}`;
                }
            });

            resolve({ image, video });
        });
    });
}