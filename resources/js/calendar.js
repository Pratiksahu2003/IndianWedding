import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

const badgeColors = {
    Project: { bg: '#e8ddd4', border: '#7a5c38', text: '#7a5c38' },
    Consultation: { bg: '#dde8e4', border: '#2e5e52', text: '#2e5e52' },
    Availability: { bg: '#dde4e8', border: '#2e4a5e', text: '#2e4a5e' },
    Schedule: { bg: '#e8e4dd', border: '#5e522e', text: '#5e522e' },
    Lead: { bg: '#e8dde8', border: '#5e2e5e', text: '#5e2e5e' },
};

function colorForType(type) {
    return badgeColors[type] ?? { bg: '#f6f1ea', border: '#9b7b4b', text: '#9b7b4b' };
}

function mapEvents(events) {
    return events.map((event) => {
        const colors = colorForType(event.type);

        return {
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end || undefined,
            allDay: event.allDay ?? false,
            url: event.url || undefined,
            backgroundColor: colors.bg,
            borderColor: colors.border,
            textColor: colors.text,
            extendedProps: {
                type: event.type,
                meta: event.meta,
                deletable: event.deletable,
                recordType: event.recordType,
                recordId: event.recordId,
                scheduleId: event.scheduleId,
            },
        };
    });
}

export function initStudioCalendar(el, options = {}) {
    const {
        events = [],
        initialDate = null,
        canAdd = false,
        onDateClick = null,
        onDatesChange = null,
        onDelete = null,
    } = options;

    let calendar = null;

    calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        initialDate: initialDate || undefined,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
        },
        height: 'auto',
        firstDay: 1,
        nowIndicator: true,
        dayMaxEvents: 3,
        eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
        slotLabelFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
        events: mapEvents(events),
        dateClick(info) {
            if (!canAdd || !onDateClick) {
                return;
            }

            onDateClick(info.dateStr.slice(0, 10));
        },
        datesSet(info) {
            if (onDatesChange) {
                onDatesChange(info.startStr.slice(0, 7));
            }
        },
        eventClick(info) {
            info.jsEvent.preventDefault();

            const { extendedProps } = info.event;
            const lines = [
                extendedProps.type,
                extendedProps.meta,
            ].filter(Boolean);

            if (extendedProps.deletable && onDelete) {
                const confirmed = window.confirm(`Remove "${info.event.title}"?`);
                if (confirmed) {
                    onDelete(extendedProps.recordType, extendedProps.recordId);
                }

                return;
            }

            if (info.event.url) {
                window.location.href = info.event.url;
                return;
            }

            if (lines.length) {
                window.alert(`${info.event.title}\n\n${lines.join('\n')}`);
            }
        },
        eventDidMount(info) {
            const { extendedProps } = info.event;
            const tooltip = [extendedProps.type, extendedProps.meta].filter(Boolean).join(' · ');
            if (tooltip) {
                info.el.setAttribute('title', tooltip);
            }
        },
    });

    calendar.render();

    return {
        calendar,
        setEvents(newEvents) {
            calendar.removeAllEvents();
            mapEvents(newEvents).forEach((event) => calendar.addEvent(event));
        },
        gotoDate(date) {
            calendar.gotoDate(date);
        },
        destroy() {
            calendar.destroy();
        },
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('studioCalendar', (config) => ({
        instance: null,
        events: config.events ?? [],
        canAdd: config.canAdd ?? false,
        month: config.month,

        init() {
            this.instance = initStudioCalendar(this.$refs.root, {
                events: this.events,
                initialDate: `${this.month}-01`,
                canAdd: this.canAdd,
                onDateClick: (date) => {
                    this.$wire.set('date', date);
                    this.$dispatch('open-schedule-form');
                },
                onDatesChange: (month) => {
                    if (month !== this.month) {
                        this.month = month;
                        this.$wire.set('month', month);
                    }
                },
                onDelete: (recordType, recordId) => {
                    this.$wire.deleteCalendarEvent(recordType, recordId);
                },
            });

            this.$wire.on('calendar-refreshed', ({ events }) => {
                this.events = events;
                this.instance?.setEvents(events);
            });
        },

        destroy() {
            this.instance?.destroy();
        },
    }));
});
