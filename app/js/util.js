export function getJson(path) {
    return new Promise(function(resolve, reject) {
        fetch(path).then(function(response) {
            return response.json();
        }).then(function(json) {
            resolve(json);
        }).catch(function(err) {
            reject(err);
        });
    });
};