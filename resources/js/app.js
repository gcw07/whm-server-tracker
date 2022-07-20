require('../../vendor/wire-elements/modal/resources/js/modal');

import Alpine from 'alpinejs';
import ToastComponent from '../../vendor/usernotnull/tall-toasts/dist/js/tall-toasts'
import SearchComponent from './search';

Alpine.data('ToastComponent', ToastComponent);
Alpine.data('SearchComponent', SearchComponent);

window.Alpine = Alpine;
Alpine.start();
