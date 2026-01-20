import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import SearchComponent from './search';

Alpine.data('SearchComponent', SearchComponent);

Livewire.start();
