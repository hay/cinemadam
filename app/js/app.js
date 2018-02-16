import View from './view.js';
import Model from './model.js';
import Store from './store.js';

const IS_DEBUG = window.location.href.indexOf('debug') !== -1;

const model = new Model();
const store = new Store({
    debug : IS_DEBUG,
    model
});
window.__view__ = new View(store.getInstance());