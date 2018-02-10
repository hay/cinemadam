import View from './view.js';
import Model from './model.js';

const CITY = 'Amsterdam';

const model = new Model();
window.__view__ = new View(model);