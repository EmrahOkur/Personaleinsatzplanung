import './bootstrap';
import * as Popper from '@popperjs/core';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.Popper = Popper;

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.bootstrap5Plugin = bootstrap5Plugin;