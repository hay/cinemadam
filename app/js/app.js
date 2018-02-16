import View from './view.js';
import Store from './store.js';

const IS_DEBUG = window.location.href.indexOf('debug') !== -1;

const store = new Store({
    debug : IS_DEBUG
});
window.__view__ = new View(store.getInstance());