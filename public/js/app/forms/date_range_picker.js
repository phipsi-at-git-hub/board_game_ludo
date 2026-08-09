/* ------------------------------
   DATE RANGE PICKER
------------------------------ */
function initDateRangePickers() {
    document
        .querySelectorAll('input[data-ui="date-range"]')
        .forEach(input => {

            if (input.dataset.dateRangeInitialized === '1') {
                return;
            }

            input.dataset.dateRangeInitialized = '1';

            /*
             * Original input is used for real from value
             */
            input.type = 'hidden';

            const wrapper = document.createElement('div');
            wrapper.className = 'date-range-picker';

            /* ------------------------------
               TRIGGER
            ------------------------------ */
            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'badge-date-range-trigger';

            const triggerLabel = document.createElement('span');
            triggerLabel.className = 'badge-date-range-label';

            const triggerArrow = document.createElement('span');
            triggerArrow.className = 'badge-date-range-arrow';
            triggerArrow.textContent = '▼';

            trigger.append(triggerLabel, triggerArrow);

            /* ------------------------------
               DROPDOWN
            ------------------------------ */
            const dropdown = document.createElement('div');
            dropdown.className = 'date-range-dropdown';

            /* ------------------------------
               CALENDAR HEADER
            ------------------------------ */
            const calendarHeader = document.createElement('div');
            calendarHeader.className = 'date-range-calendar-header';

            const calendarTitle = document.createElement('div');
            calendarTitle.className = 'date-range-calendar-title';

            const calendarNavigation = document.createElement('div');
            calendarNavigation.className = 'date-range-calendar-navigation';

            const previousButton = document.createElement('button');
            previousButton.type = 'button';
            previousButton.setAttribute('aria-label', 'Previous month');
            previousButton.textContent = '‹';

            const nextButton = document.createElement('button');
            nextButton.type = 'button';
            nextButton.setAttribute('aria-label', 'Next month');
            nextButton.textContent = '›';

            calendarNavigation.append(previousButton, nextButton);

            calendarHeader.append(calendarTitle, calendarNavigation);

            /* ------------------------------
               WEEKDAYS
            ------------------------------ */
            const weekdays = document.createElement('div');
            weekdays.className = 'date-range-calendar-weekdays';

            [
                'Mo',
                'Di',
                'Mi',
                'Do',
                'Fr',
                'Sa',
                'So'
            ].forEach(day => {
                const weekday = document.createElement('div');
                weekday.className = 'date-range-calendar-weekday';
                weekday.textContent = day;
                weekdays.appendChild(weekday);
            });

            /* ------------------------------
               DAYS
            ------------------------------ */

            const days = document.createElement('div');
            days.className = 'date-range-calendar-days';

            /* ------------------------------
               STATUS
            ------------------------------ */

            const status = document.createElement('div');
            status.className = 'date-range-picker-status';
            status.textContent = 'Pick date range'; // German: 'Zeitraum auswählen';

            dropdown.append(
                calendarHeader,
                weekdays,
                days,
                status
            );

            /* ------------------------------
               CALENDAR STATE
            ------------------------------ */
            let displayedMonth;
            if (input.value) {
                const parsed = parseDateRangeValue(input.value);

                if (parsed && parsed.start) {
                    displayedMonth = new Date(
                        parsed.start.getFullYear(),
                        parsed.start.getMonth(),
                        1
                    );
                }
            }

            if (!displayedMonth) {
                const now = new Date();
                displayedMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            }

            let selectedStart = null;
            let selectedEnd = null;

            if (input.value) {
                const parsed = parseDateRangeValue(input.value);

                if (parsed) {
                    selectedStart = parsed.start;
                    selectedEnd = parsed.end;
                }
            }

            /* ------------------------------
               UPDATE TRIGGER
            ------------------------------ */
            const updateTrigger = () => {
                if (!selectedStart || !selectedEnd) {
                    triggerLabel.textContent = 'Pick date range'; // German: 'Zeitraum auswählen';
                    return;
                }o
                triggerLabel.textContent = `${formatDateTime(selectedStart)} - ${formatDateTime(selectedEnd)}`;
            };

            /* ------------------------------
               UPDATE STATUS
            ------------------------------ */

            const updateStatus = () => {
                status.classList.remove('error');

                if (!selectedStart) {
                    status.textContent = 'Pick start date'; // German: 'Startdatum auswählen';
                    return;
                }

                if (!selectedEnd) {
                    status.textContent = 'Pick end date'; // German: 'Enddatum auswählen';
                    return;
                }

                status.textContent = 'Date range picked'; // German: 'Zeitraum ausgewählt';
            };

            /* ------------------------------
               RENDER CALENDAR
            ------------------------------ */
            const renderCalendar = () => {
                days.innerHTML = '';

                const year = displayedMonth.getFullYear();
                const month = displayedMonth.getMonth();

                calendarTitle.textContent =
                    new Intl.DateTimeFormat(
                        'de-DE',
                        {
                            month: 'long',
                            year: 'numeric'
                        }
                    ).format(displayedMonth);

                /*
                 * Monday = 0 ... Sunday = 6
                 */
                const firstDay = new Date(year, month, 1);
                const firstWeekday = (firstDay.getDay() + 6) % 7;
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPreviousMonth = new Date(year, month, 0).getDate();
                const totalCells = 42;

                for (let index = 0; index < totalCells; index++) {
                    let date;
                    let isOtherMonth = false;

                    if (index < firstWeekday) {
                        const day = daysInPreviousMonth - firstWeekday + index + 1;
                        date = new Date(year, month - 1, day);
                        isOtherMonth = true;
                    } else if (index >= firstWeekday + daysInMonth) {
                        const day = index - firstWeekday - daysInMonth + 1;
                        date = new Date(year, month + 1, day);
                        isOtherMonth = true;
                    } else {
                        const day = index - firstWeekday + 1;
                        date = new Date(year, month, day);
                    }

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'date-range-calendar-day';
                    button.textContent = date.getDate();

                    if (isOtherMonth) {
                        button.classList.add('other-month');
                    }

                    /*
                     * Today
                     */
                    const today = new Date();

                    if (
                        date.getFullYear() === today.getFullYear() &&
                        date.getMonth() === today.getMonth() &&
                        date.getDate() === today.getDate()
                    ) {
                        button.classList.add('today');
                    }

                    /*
                     * Pick status
                     */
                    if (selectedStart && isSameDay(date, selectedStart)
                    ) {
                        button.classList.add('range-start');
                    }

                    if (selectedEnd && isSameDay(date, selectedEnd)) {
                        button.classList.add('range-end');
                    }

                    if (
                        selectedStart &&
                        selectedEnd &&
                        date > selectedStart &&
                        date < selectedEnd
                    ) {
                        button.classList.add('in-range');
                    }

                    /*
                     * Click at date
                     */
                    button.addEventListener(
                        'click',
                        () => {
                            /*
                             * First click:
                             * Set start date.
                             */
                            if (!selectedStart || (selectedStart && selectedEnd)) {
                                selectedStart =new Date(date);
                                selectedEnd = null;

                                updateTrigger();
                                updateStatus();
                                renderCalendar();

                                return;
                            }

                            /*
                             * Second Click:
                             * Set end date.
                             */
                            selectedEnd = new Date(date);

                            /*
                             * If second date occurs before 
                             * the first date: switch them
                             */
                            if (selectedEnd < selectedStart) {
                                const temporary = selectedStart;
                                selectedStart = selectedEnd;
                                selectedEnd = temporary;
                            }

                            /*
                             * Set times. 
                             * Start: 00:00:00
                             * End:   23:59:59
                             */
                            selectedStart.setHours(0, 0, 0, 0);
                            selectedEnd.setHours(23, 59, 59, 999);

                            updateTrigger();
                            updateStatus();
                            renderCalendar();

                            /*
                             * Write complete picks to hidden input field
                             */
                            input.value = `${formatDateTime(selectedStart)} - ${formatDateTime(selectedEnd)}`;
                            input.dispatchEvent(
                                new Event(
                                    'change',
                                    {
                                        bubbles: true
                                    }
                                )
                            );

                            /*
                             * Close Calendar.
                             */
                            wrapper.classList.remove('open');
                            trigger.classList.remove('active');
                        }
                    );
                    days.appendChild(button);
                }
            };

            /* ------------------------------
               MONTH NAVIGATION
            ------------------------------ */
            previousButton.addEventListener(
                'click',
                event => {
                    event.stopPropagation();

                    displayedMonth =
                        new Date(
                            displayedMonth.getFullYear(),
                            displayedMonth.getMonth() - 1,
                            1
                        );

                    renderCalendar();
                }
            );

            nextButton.addEventListener(
                'click',
                event => {
                    event.stopPropagation();

                    displayedMonth =
                        new Date(
                            displayedMonth.getFullYear(),
                            displayedMonth.getMonth() + 1,
                            1
                        );

                    renderCalendar();
                }
            );

            /* ------------------------------
               TRIGGER
            ------------------------------ */
            trigger.addEventListener(
                'click',
                event => {
                    event.stopPropagation();

                    wrapper.classList.toggle('open');
                    trigger.classList.toggle(
                        'active'
                    );
                }
            );

            /* ------------------------------
               INITIAL RENDER
            ------------------------------ */
            updateTrigger();
            updateStatus();
            renderCalendar();

            /* ------------------------------
               INSERT
            ------------------------------ */
            wrapper.append(trigger, dropdown);

            input.insertAdjacentElement('afterend', wrapper);
        });

    /* ------------------------------
       CLOSE ON OUTSIDE CLICK
    ------------------------------ */
    document.addEventListener(
        'click',
        event => {
            document.querySelectorAll('.date-range-picker.open').forEach(picker => {
                if (!picker.contains(event.target)) {
                    picker.classList.remove(
                        'open'
                    );

                    const trigger =
                        picker.querySelector(
                            '.badge-date-range-trigger'
                        );

                    if (trigger) {
                        trigger.classList.remove(
                            'active'
                        );
                    }
                }
            });
        }
    );
}

function initDateRangePickers() {
    document.querySelectorAll('input[data-ui="date-range"]').forEach(input => {
            if (input.classList.contains('date-range-enhanced')) {
                return;
            }

            const localization = (input.dataset.uiLocalization || 'en-us').toLowerCase();

            const locale = localization.startsWith('de') ? 'de-DE' : 'en-US';

            const wrapper = document.createElement('div');
            wrapper.className = 'date-range-picker';

            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'badge-date-range-trigger';

            const triggerLabel = document.createElement('span');
            triggerLabel.className = 'badge-date-range-label';

            const triggerArrow = document.createElement('span');
            triggerArrow.className = 'badge-date-range-arrow';
            triggerArrow.textContent = '▼';

            trigger.append(triggerLabel, triggerArrow);

            const dropdown = document.createElement('div');
            dropdown.className = 'date-range-dropdown';

            /* ------------------------------
               CALENDAR HEADER
            ------------------------------ */
            const calendarHeader = document.createElement('div');
            calendarHeader.className = 'date-range-calendar-header';

            const calendarTitle = document.createElement('div');
            calendarTitle.className = 'date-range-calendar-title';

            const calendarNavigation = document.createElement('div');
            calendarNavigation.className = 'date-range-calendar-navigation';

            const previousButton = document.createElement('button');
            previousButton.type = 'button';
            previousButton.setAttribute('aria-label', 'Previous month');
            previousButton.textContent = '‹';

            const nextButton = document.createElement('button');
            nextButton.type = 'button';
            nextButton.setAttribute('aria-label', 'Next month');
            nextButton.textContent = '›';

            calendarNavigation.append(previousButton, nextButton);
            calendarHeader.append(calendarTitle, calendarNavigation);

            /* ------------------------------
               WEEKDAYS
            ------------------------------ */
            const weekdays = document.createElement('div');
            weekdays.className = 'date-range-calendar-weekdays';

            const weekdayNames =
                locale === 'de-DE'
                    ? ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
                    : ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];

            weekdayNames.forEach(name => {
                const weekday = document.createElement('div');
                weekday.className = 'date-range-calendar-weekday';
                weekday.textContent = name;
                weekdays.appendChild(weekday);
            });

            /* ------------------------------
               DAYS
            ------------------------------ */
            const calendarDays = document.createElement('div');
            calendarDays.className = 'date-range-calendar-days';

            /* ------------------------------
               STATUS
            ------------------------------ */
            const status = document.createElement('div');
            status.className = 'date-range-picker-status';
            status.textContent = 'Pick date range'; // German: 'Zeitraum auswählen';

            dropdown.append(
                calendarHeader,
                weekdays,
                calendarDays,
                status
            );

            /* ------------------------------
               STATE
            ------------------------------ */
            let displayedMonth;
            let rangeStart = null;
            let rangeEnd = null;
            let selectingRange = false;
            const withTime = input.dataset.withTime === 'true';

            /* ------------------------------
               DATE HELPERS
            ------------------------------ */
            const pad = value => String(value).padStart(2, '0');
            const cloneDate = date => new Date(date.getTime());
            const startOfDay = date => {
                const result = cloneDate(date);
                result.setHours(0, 0, 0, 0);

                return result;
            };

            const endOfDay = date => {
                const result = cloneDate(date);
                result.setHours(23, 59, 59, 0);

                return result;
            };

            const isSameDay = (a, b) => {
                if (!a || !b) {
                    return false;
                }

                return (
                    a.getFullYear() === b.getFullYear() &&
                    a.getMonth() === b.getMonth() &&
                    a.getDate() === b.getDate()
                );
            };

            /* ------------------------------
               INTERNAL FORMAT

               yyyy-mm-dd hh:mm:ss
            ------------------------------ */
            const formatInternalDate = date => {
                return (
                    date.getFullYear() +
                    '-' +
                    pad(date.getMonth() + 1) +
                    '-' +
                    pad(date.getDate()) +
                    ' ' +
                    pad(date.getHours()) +
                    ':' +
                    pad(date.getMinutes()) +
                    ':' +
                    pad(date.getSeconds())
                );
            };

            /* ------------------------------
               DISPLAY FORMAT
            ------------------------------ */
            const formatDisplayDate = date => {
                const datePart =
                    locale === 'de-DE'
                        ? `${pad(date.getDate())}.${pad(date.getMonth() + 1)}.${date.getFullYear()}`
                        : `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

                return (
                    datePart +
                    ' ' +
                    pad(date.getHours()) +
                    ':' +
                    pad(date.getMinutes()) +
                    ':' +
                    pad(date.getSeconds())
                );
            };

            /* ------------------------------
               INPUT RANGE PARSER
            ------------------------------ */
            const parseInputRange = value => {
                if (!value) {
                    return null;
                }

                const parts = value.split(/\s+-\s+/);

                if (parts.length !== 2) {
                    return null;
                }

                const parseDate = value => {
                    const match = value.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):(\d{2})$/);

                    if (!match) {
                        return null;
                    }

                    const date = new Date(
                        Number(match[1]),
                        Number(match[2]) - 1,
                        Number(match[3]),
                        Number(match[4]),
                        Number(match[5]),
                        Number(match[6])
                    );

                    if (isNaN(date.getTime())) {
                        return null;
                    }

                    return date;
                };

                const start = parseDate(parts[0]);
                const end = parseDate(parts[1]);

                if (!start || !end) {
                    return null;
                }

                return {
                    start,
                    end
                };
            };

            /* ------------------------------
               UPDATE INPUT + LABEL
            ------------------------------ */
            const updateValue = () => {
                if (!rangeStart || !rangeEnd) {
                    return;
                }

                const start = cloneDate(rangeStart);
                const end = cloneDate(rangeEnd);

                start.setHours(0, 0, 0, 0);
                end.setHours(23, 59, 59);

                input.value = `${formatInternalDate(start)} - ${formatInternalDate(end)}`;

                triggerLabel.textContent = `${formatDisplayDate(start)} - ${formatDisplayDate(end)}`;

                input.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true
                        }
                    )
                );
            };

            /* ------------------------------
               UPDATE TRIGGER
            ------------------------------ */
            const updateTrigger = () => {
                if (rangeStart && rangeEnd) {
                    triggerLabel.textContent = `${formatDisplayDate(rangeStart)} - ${formatDisplayDate(rangeEnd)}`;
                } else if (rangeStart) {
                    triggerLabel.textContent = formatDisplayDate(rangeStart);
                } else {
                    triggerLabel.textContent = 'Select date range';
                }
            };

            /* ------------------------------
               CALENDAR RENDER
            ------------------------------ */
            const renderCalendar = () => {
                calendarDays.innerHTML = '';

                calendarTitle.textContent =
                    new Intl.DateTimeFormat(
                        locale,
                        {
                            month: 'long',
                            year: 'numeric'
                        }
                    ).format(displayedMonth);

                const year = displayedMonth.getFullYear();
                const month = displayedMonth.getMonth();
                const firstDay = new Date(year, month, 1);

                /*
                 * JavaScript:
                 * Sunday = 0
                 *
                 * Calendar:
                 * Monday = 0
                 */
                let firstWeekday = firstDay.getDay();

                firstWeekday = firstWeekday === 0 ? 6 : firstWeekday - 1;

                const daysInMonth = new Date(year, month + 1, 0).getDate();

                const daysInPreviousMonth = new Date(year, month, 0).getDate();

                const totalCells = Math.ceil((firstWeekday + daysInMonth) / 7 ) * 7;

                for (let index = 0; index < totalCells; index++) {
                    let date;
                    let isOtherMonth = false;

                    if (index < firstWeekday) {
                        const day = daysInPreviousMonth - firstWeekday + index + 1;
                        date = new Date(year, month - 1, day);
                        isOtherMonth = true;
                    } else if (index >= firstWeekday + daysInMonth) {
                        const day = index - (firstWeekday + daysInMonth) + 1;
                        date = new Date(year, month + 1, day);
                        isOtherMonth = true;
                    } else {
                        const day = index - firstWeekday + 1;
                        date = new Date(year, month, day);
                    }

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'date-range-calendar-day';
                    button.textContent = date.getDate();

                    if (isOtherMonth) {
                        button.classList.add('other-month');
                    }

                    const today = new Date();

                    if (isSameDay(date, today)) {
                        button.classList.add('today');
                    }

                    if (rangeStart && isSameDay(date, rangeStart)) {
                        button.classList.add('range-start');
                    }

                    if (rangeEnd && isSameDay(date, rangeEnd)) {
                        button.classList.add('range-end');
                    }

                    if (
                        rangeStart &&
                        rangeEnd &&
                        date > rangeStart &&
                        date < rangeEnd
                    ) {
                        button.classList.add('in-range');
                    }

                    button.addEventListener(
                        'click',
                        event => {
                            event.stopPropagation();
                            const selectedDate = startOfDay(date);

                            /*
                             * First selection:
                             * keep picker open.
                             */
                            if (!selectingRange || !rangeStart) {
                                rangeStart = selectedDate;
                                rangeEnd = null;
                                selectingRange = true;

                                status.textContent = 'Pick end date'; // German: 'Enddatum auswählen';

                                updateTrigger();
                                renderCalendar();

                                return;
                            }

                            /*
                             * Second selection.
                             */
                            if (selectedDate < rangeStart) {
                                rangeEnd = rangeStart;
                                rangeStart = selectedDate;
                            } else {
                                rangeEnd = selectedDate;
                            }

                            /*
                             * Same day:
                             * start and end are identical.
                             */
                            if (isSameDay(rangeStart, rangeEnd)) {
                                rangeStart = startOfDay(rangeStart);
                                rangeEnd = endOfDay(rangeEnd);
                            } else {
                                rangeStart = startOfDay(rangeStart);
                                rangeEnd = endOfDay(rangeEnd);
                            }

                            selectingRange = false;

                            status.textContent = 'Date range picked' // German: 'Zeitraum ausgewählt';
                            updateValue();
                            renderCalendar();

                            /*
                             * Close only after
                             * the complete range
                             * has been selected.
                             */
                            wrapper.classList.remove('open');
                            trigger.classList.remove('active');
                        }
                    );

                    calendarDays.appendChild(button);
                }
            };

            /* ------------------------------
               MONTH NAVIGATION
            ------------------------------ */
            previousButton.addEventListener(
                'click',
                event => {
                    event.stopPropagation();
                    displayedMonth = new Date(
                        displayedMonth.getFullYear(),
                        displayedMonth.getMonth() - 1,
                        1
                    );

                    renderCalendar();
                }
            );

            nextButton.addEventListener(
                'click',
                event => {
                    event.stopPropagation();
                    displayedMonth = new Date(
                        displayedMonth.getFullYear(),
                        displayedMonth.getMonth() + 1,
                        1
                    );

                    renderCalendar();
                }
            );

            /* ------------------------------
               INITIAL VALUE
            ------------------------------ */
            const existingRange = parseInputRange(input.value);

            if (existingRange) { 
                rangeStart = existingRange.start;
                rangeEnd = existingRange.end;
                displayedMonth = new Date(
                    rangeStart.getFullYear(),
                    rangeStart.getMonth(),
                    1
                );

                updateTrigger();
            } else {
                const now = new Date();

                displayedMonth = new Date(
                    now.getFullYear(),
                    now.getMonth(),
                    1
                );

                triggerLabel.textContent = 'Select date range';
            }

            /* ------------------------------
               OPEN / CLOSE
            ------------------------------ */
            trigger.addEventListener(
                'click',
                event => {
                    event.stopPropagation();
                    const isOpen = wrapper.classList.contains('open');

                    if (!isOpen) {
                        wrapper.classList.add('open');
                        trigger.classList.add('active');

                        renderCalendar();
                    } else {
                        wrapper.classList.remove('open');
                        trigger.classList.remove('active');
                    }
                }
            );

            /* ------------------------------
               BUILD PICKER
            ------------------------------ */
            wrapper.append(trigger,dropdown);

            input.classList.add('date-range-enhanced');
            input.type = 'hidden';
            input.insertAdjacentElement('afterend', wrapper);

            renderCalendar();
        });

    /* ------------------------------
       CLOSE ON OUTSIDE CLICK
    ------------------------------ */
    if (!document.body.dataset.dateRangeOutsideClick) {
        document.body.dataset.dateRangeOutsideClick = '1';
        document.addEventListener(
            'click',
            event => {
                document.querySelectorAll('.date-range-picker.open').forEach(picker => {
                    if (picker.contains(event.target)) {
                        return;
                    }

                    picker.classList.remove('open');
                    const trigger = picker.querySelector('.badge-date-range-trigger');

                    if (trigger) {
                        trigger.classList.remove(
                            'active'
                        );
                    }
                });
            }
        );
    }
}
