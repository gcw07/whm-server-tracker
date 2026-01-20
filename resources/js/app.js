import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import '../../vendor/wire-elements/modal/resources/js/modal';

import ToastComponent from '../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts';
import SearchComponent from './search';

Alpine.plugin(ToastComponent)

Alpine.data('SearchComponent', SearchComponent);

Livewire.start();
