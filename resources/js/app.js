import '../../vendor/wire-elements/modal/resources/js/modal';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Tooltip from "@ryangjchandler/alpine-tooltip";
import ToastComponent from '../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts';
import SearchComponent from './search';

Alpine.plugin(focus);
Alpine.plugin(Tooltip);

Alpine.data('ToastComponent', ToastComponent);
Alpine.data('SearchComponent', SearchComponent);

window.Alpine = Alpine;
Alpine.start();
