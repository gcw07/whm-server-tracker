require('../../vendor/wire-elements/modal/resources/js/modal');

import Alpine from 'alpinejs';
import ToastComponent from '../../vendor/usernotnull/tall-toasts/dist/js/tall-toasts'

Alpine.data('ToastComponent', ToastComponent);

window.Alpine = Alpine;
Alpine.start();
