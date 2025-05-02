import axios from 'axios';
window.axios = axios;
import './echo';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
