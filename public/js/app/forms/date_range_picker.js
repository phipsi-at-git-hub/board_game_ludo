/* ------------------------------
   DATE RANGE PICKER
------------------------------ */
function initDateRangePickers() {
    document.querySelectorAll('input[data-ui="date-range"]').forEach(input => {
        if (input.classList.contains('date-range-enhanced')) {
            return;
        }

        input.classList.add('date-range-enhanced');
        input.type = 'hidden';

        /* ------------------------------
           CONFIGURATION
        ------------------------------ */
        const localization = (input.dataset.uiLocalization || 'en-us').toLowerCase();
        const locale = localization.startsWith('de') ? 'de-DE' : 'en-US';
        const withTime = input.dataset.uiWithTime === 'true';

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
            result.setHours(23, 59, 59);
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
           INTERNAL DATE FORMAT
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
           DISPLAY DATE FORMAT
        ------------------------------ */
        const formatDisplayDate = date => {
            const datePart = locale === 'de-DE'
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

            const parseDate = dateValue => {
                const match = dateValue.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):(\d{2})$/);

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
           TIME HELPERS
        ------------------------------ */
        const createTimeOptions = () => {
            const options = [];

            for (let hour = 0; hour <= 23; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    options.push(`${pad(hour)}:${pad(minute)}`);
                }
            }

            /*
             * Explicit final option.
             */
            options.push('23:59');

            return options;
        };

        const isValidTime = value => {
            return /^([01]\d|2[0-3]):([0-5]\d)$/.test(value);
        };

        const getTimeParts = value => {
            if (!value || !isValidTime(value)) {
                return null;
            }

            const parts = value.split(':');

            return {
                hours: Number(parts[0]),
                minutes: Number(parts[1])
            };
        };

        /* ------------------------------
           WRAPPER
        ------------------------------ */
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
        previousButton.setAttribute('aria-label', locale === 'de-DE' ? 'Vorheriger Monat' : 'Previous month');
        previousButton.textContent = '‹';

        const nextButton = document.createElement('button');
        nextButton.type = 'button';
        nextButton.setAttribute('aria-label', locale === 'de-DE' ? 'Nächster Monat' : 'Next month');
        nextButton.textContent = '›';

        calendarNavigation.append(previousButton, nextButton);
        calendarHeader.append(calendarTitle, calendarNavigation);

        /* ------------------------------
           WEEKDAYS
        ------------------------------ */
        const weekdays = document.createElement('div');
        weekdays.className = 'date-range-calendar-weekdays';

        const weekdayNames = locale === 'de-DE'
            ? ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
            : ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];

        weekdayNames.forEach(name => {
            const weekday = document.createElement('div');
            weekday.className = 'date-range-calendar-weekday';
            weekday.textContent = name;
            weekdays.appendChild(weekday);
        });

        /* ------------------------------
           CALENDAR DAYS
        ------------------------------ */
        const calendarDays = document.createElement('div');
        calendarDays.className = 'date-range-calendar-days';

        /* ------------------------------
           TIME SECTION
        ------------------------------ */
        let timeSection = null;
        let startTimeField = null;
        let endTimeField = null;
        let startTimeDropdown = null;
        let endTimeDropdown = null;

        if (withTime) {
            timeSection = document.createElement('div');
            timeSection.className = 'date-range-time-section';

            /* ------------------------------
               START TIME
            ------------------------------ */
            const startTimeContainer = document.createElement('div');
            startTimeContainer.className = 'date-range-time-group';

            const startTimeLabel = document.createElement('label');
            startTimeLabel.className = 'date-range-time-label';
            startTimeLabel.textContent = locale === 'de-DE' ? 'Startzeit' : 'Start time';

            /* ------------------------------
               END TIME
            ------------------------------ */
            const endTimeContainer = document.createElement('div');
            endTimeContainer.className = 'date-range-time-group';

            const endTimeLabel = document.createElement('label');
            endTimeLabel.className = 'date-range-time-label';
            endTimeLabel.textContent = locale === 'de-DE' ? 'Endzeit' : 'End time';

            /* ------------------------------
               TIME INPUT CREATOR
            ------------------------------ */
            const createTimeInput = (initialValue, changeCallback) => {
                const container = document.createElement('div');
                container.className = 'date-range-time-input-wrapper';

                const field = document.createElement('input');
                field.type = 'text';
                field.className = 'date-range-time-input';
                field.placeholder = 'HH:MM';
                field.inputMode = 'numeric';
                field.autocomplete = 'off';

                if (initialValue) {
                    field.value = initialValue;
                }

                const dropdown = document.createElement('div');
                dropdown.className = 'date-range-time-dropdown';

                createTimeOptions().forEach(time => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'date-range-time-option';
                    option.textContent = time;

                    option.addEventListener('click', event => {
                        event.stopPropagation();

                        field.value = time;

                        dropdown.classList.remove('open');

                        changeCallback(field.value);
                    });

                    dropdown.appendChild(option);
                });

                /*
                field.addEventListener('focus', event => {
                    event.stopPropagation();
                    dropdown.classList.add('open');
                });
                */

                field.addEventListener('click', event => {
                    event.stopPropagation();
                    //dropdown.classList.add('open');

                    document.querySelectorAll('.date-range-time-dropdown.open').forEach(openDropdown => {
                        if (openDropdown !== dropdown) {
                            openDropdown.classList.remove('open'); 
                        }
                    }); 
                    dropdown.classList.toggle('open'); 
                });

                field.addEventListener('input', () => {
                    /*
                     * Manual typing means the
                     * predefined time dropdown
                     * is no longer relevant.
                     */
                    dropdown.classList.remove('open');
                    changeCallback(field.value);
                });

                field.addEventListener('change', () => {
                    dropdown.classList.remove('open');
                    changeCallback(field.value);
                });

                container.addEventListener('click', event => {
                    event.stopPropagation();
                });

                container.append(field, dropdown);

                return {
                    container,
                    field,
                    dropdown
                };
            };

            const startTime = createTimeInput('', () => {
                updateTimeState();
            });

            const endTime = createTimeInput('', () => {
                updateTimeState();
            });

            startTimeField = startTime.field;
            endTimeField = endTime.field;
            startTimeDropdown = startTime.dropdown;
            endTimeDropdown = endTime.dropdown;

            startTimeContainer.append(startTimeLabel, startTime.container);
            endTimeContainer.append(endTimeLabel, endTime.container);

            timeSection.append(startTimeContainer, endTimeContainer);
        }

        /* ------------------------------
           APPLY SECTION
        ------------------------------ */
        let applySection = null;
        let applyButton = null;

        if (withTime) {
            applySection = document.createElement('div');
            applySection.className = 'date-range-apply';

            applyButton = document.createElement('button');
            applyButton.type = 'button';
            applyButton.className = 'btn btn-actions btn-date-range-apply';
            applyButton.textContent = locale === 'de-DE' ? 'Übernehmen' : 'Apply';

            applySection.append(applyButton);
        }

        /* ------------------------------
           STATUS
        ------------------------------ */
        const status = document.createElement('div');
        status.className = 'date-range-picker-status';
        status.textContent = locale === 'de-DE'
            ? 'Zeitraum auswählen'
            : 'Pick date range';

        /* ------------------------------
           DROPDOWN STRUCTURE
        ------------------------------ */
        dropdown.append(calendarHeader, weekdays, calendarDays);

        if (timeSection) {
            dropdown.append(timeSection);
        }

        dropdown.append(status);

        if (applySection) {
            dropdown.append(applySection);
        }

        /* ------------------------------
           CALENDAR STATE
        ------------------------------ */
        let displayedMonth = null;
        let rangeStart = null;
        let rangeEnd = null;
        let selectingRange = false;

        let startTime = null;
        let endTime = null;

        /* ------------------------------
           INITIAL VALUE
        ------------------------------ */
        const existingRange = parseInputRange(input.value);

        if (existingRange) {
            rangeStart = cloneDate(existingRange.start);
            rangeEnd = cloneDate(existingRange.end);

            displayedMonth = new Date(
                rangeStart.getFullYear(),
                rangeStart.getMonth(),
                1
            );

            if (withTime) {
                startTime = `${pad(rangeStart.getHours())}:${pad(rangeStart.getMinutes())}`;
                endTime = `${pad(rangeEnd.getHours())}:${pad(rangeEnd.getMinutes())}`;

                startTimeField.value = startTime;
                endTimeField.value = endTime;
            }
        } else {
            const now = new Date();

            displayedMonth = new Date(
                now.getFullYear(),
                now.getMonth(),
                1
            );
        }

        /* ------------------------------
           UPDATE TRIGGER
        ------------------------------ */
        const updateTrigger = () => {
            if (rangeStart && rangeEnd) {
                triggerLabel.textContent = `${formatDisplayDate(rangeStart)} - ${formatDisplayDate(rangeEnd)}`;
                return;
            }

            if (rangeStart) {
                triggerLabel.textContent = formatDisplayDate(rangeStart);
                return;
            }

            triggerLabel.textContent = locale === 'de-DE'
                ? 'Zeitraum auswählen'
                : 'Select date range';
        };

        /* ------------------------------
           UPDATE STATUS
        ------------------------------ */
        const updateStatus = () => {
            status.classList.remove('error');

            if (!rangeStart) {
                status.textContent = locale === 'de-DE'
                    ? 'Startdatum auswählen'
                    : 'Pick start date';

                return;
            }

            if (!rangeEnd) {
                status.textContent = locale === 'de-DE'
                    ? 'Enddatum auswählen'
                    : 'Pick end date';

                return;
            }

            if (withTime) {
                if (!startTime || !endTime) {
                    status.textContent = locale === 'de-DE'
                        ? 'Start- und Endzeit auswählen'
                        : 'Select start and end time';

                    return;
                }

                if (!isValidTime(startTime) || !isValidTime(endTime)) {
                    status.textContent = locale === 'de-DE'
                        ? 'Ungültige Uhrzeit'
                        : 'Invalid time';

                    status.classList.add('error');

                    return;
                }
            }

            status.textContent = locale === 'de-DE'
                ? 'Zeitraum ausgewählt'
                : 'Date range selected';
        };

        /* ------------------------------
           UPDATE TIME STATE
        ------------------------------ */
        function updateTimeState() {
            if (!withTime) {
                return;
            }

            startTime = startTimeField.value.trim();
            endTime = endTimeField.value.trim();

            updateStatus();
            updateApplyState();
        }

        /* ------------------------------
           APPLY BUTTON STATE
        ------------------------------ */
        const updateApplyState = () => {
            if (!applyButton) {
                return;
            }

            const validDates = !!rangeStart && !!rangeEnd;
            const validTimes = isValidTime(startTime) && isValidTime(endTime);

            applyButton.disabled = !validDates || !validTimes;
        };

        /* ------------------------------
           WRITE VALUE
        ------------------------------ */
        const applyRange = () => {
            if (!rangeStart || !rangeEnd) {
                return false;
            }

            if (withTime) {
                if (!isValidTime(startTime) || !isValidTime(endTime)) {
                    status.textContent = locale === 'de-DE'
                        ? 'Bitte gültige Start- und Endzeit eingeben.'
                        : 'Please enter valid start and end times.';

                    status.classList.add('error');

                    return false;
                }

                const startParts = getTimeParts(startTime);
                const endParts = getTimeParts(endTime);

                rangeStart.setHours(
                    startParts.hours,
                    startParts.minutes,
                    0,
                    0
                );

                rangeEnd.setHours(
                    endParts.hours,
                    endParts.minutes,
                    0,
                    0
                );
            } else {
                rangeStart = startOfDay(rangeStart);
                rangeEnd = endOfDay(rangeEnd);
            }

            /*
             * Time ranges must also be chronologically
             * valid when both dates are identical.
             */
            if (isSameDay(rangeStart, rangeEnd) && rangeEnd < rangeStart) {
                status.textContent = locale === 'de-DE'
                    ? 'Die Endzeit muss nach der Startzeit liegen.'
                    : 'The end time must be after the start time.';

                status.classList.add('error');

                return false;
            }

            input.value = `${formatInternalDate(rangeStart)} - ${formatInternalDate(rangeEnd)}`;

            triggerLabel.textContent = `${formatDisplayDate(rangeStart)} - ${formatDisplayDate(rangeEnd)}`;

            input.dispatchEvent(
                new Event('change', {
                    bubbles: true
                })
            );

            status.classList.remove('error');

            status.textContent = locale === 'de-DE'
                ? 'Zeitraum ausgewählt'
                : 'Date range selected';

            return true;
        };

        /* ------------------------------
           CALENDAR RENDER
        ------------------------------ */
        const renderCalendar = () => {
            calendarDays.innerHTML = '';

            calendarTitle.textContent = new Intl.DateTimeFormat(
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
             * Sunday = 0
             * Monday = 0 in calendar
             */
            let firstWeekday = firstDay.getDay();
            firstWeekday = firstWeekday === 0 ? 6 : firstWeekday - 1;

            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPreviousMonth = new Date(year, month, 0).getDate();

            const totalCells = Math.ceil(
                (firstWeekday + daysInMonth) / 7
            ) * 7;

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

                // ToDo: Define: Should this situations be more exclusive? 
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
                    !isSameDay(date, rangeStart) && 
                    !isSameDay(date, rangeEnd) && 
                    date > rangeStart &&
                    date < rangeEnd
                ) {
                    button.classList.add('in-range');
                }

                /* ------------------------------
                   DATE CLICK
                ------------------------------ */
                button.addEventListener('click', event => {
                    event.stopPropagation();

                    const selectedDate = startOfDay(date);

                    /*
                     * First selection.
                     */
                    if (!selectingRange || !rangeStart) {
                        rangeStart = selectedDate;
                        rangeEnd = null;
                        selectingRange = true;

                        if (withTime) {
                            if (!startTime) {
                                startTime = '00:00';
                                startTimeField.value = startTime;
                            }

                            if (!endTime) {
                                endTime = '23:59';
                                endTimeField.value = endTime;
                            }
                        }

                        status.textContent = locale === 'de-DE'
                            ? 'Enddatum auswählen'
                            : 'Pick end date';

                        updateTrigger();
                        updateStatus();
                        updateApplyState();
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
                     * Same date is valid.
                     */
                    rangeStart = startOfDay(rangeStart);
                    rangeEnd = endOfDay(rangeEnd);

                    selectingRange = false;

                    /*
                     * With time:
                     * do NOT apply or close yet.
                     */
                    if (withTime) {
                        if (!startTime) {
                            startTime = '00:00';
                            startTimeField.value = startTime;
                        }

                        if (!endTime) {
                            endTime = '23:59';
                            endTimeField.value = endTime;
                        }

                        status.textContent = locale === 'de-de'
                            ? 'Start- und Endzeit prüfen oder anpassen'
                            : 'Check or adjust start and end time';

                        updateTrigger();
                        updateStatus();
                        updateApplyState();
                        renderCalendar();

                        return;
                    }

                    /*
                     * Without time:
                     * range is immediately complete.
                     */
                    updateTrigger();
                    updateStatus();
                    renderCalendar();

                    if (applyRange()) {
                        wrapper.classList.remove('open');
                        trigger.classList.remove('active');
                    }
                });

                calendarDays.appendChild(button);
            }
        };

        /* ------------------------------
           MONTH NAVIGATION
        ------------------------------ */
        previousButton.addEventListener('click', event => {
            event.stopPropagation();

            displayedMonth = new Date(
                displayedMonth.getFullYear(),
                displayedMonth.getMonth() - 1,
                1
            );

            renderCalendar();
        });

        nextButton.addEventListener('click', event => {
            event.stopPropagation();

            displayedMonth = new Date(
                displayedMonth.getFullYear(),
                displayedMonth.getMonth() + 1,
                1
            );

            renderCalendar();
        });

        /* ------------------------------
           APPLY BUTTON
        ------------------------------ */

        if (applyButton) {
            applyButton.addEventListener('click', event => {
                event.stopPropagation();

                if (!applyRange()) {
                    return;
                }

                wrapper.classList.remove('open');
                trigger.classList.remove('active');
            });
        }

        /* ------------------------------
           TRIGGER
        ------------------------------ */
        trigger.addEventListener('click', event => {
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
        });

        /* ------------------------------
           INITIAL STATE
        ------------------------------ */
        updateTrigger();
        updateStatus();
        updateApplyState();

        /* ------------------------------
           INSERT
        ------------------------------ */
        wrapper.append(trigger, dropdown);
        input.insertAdjacentElement('afterend', wrapper);

        renderCalendar();
    });

    /* ------------------------------
       CLOSE ON OUTSIDE CLICK
    ------------------------------ */
    if (!document.body.dataset.dateRangeOutsideClick) {
        document.body.dataset.dateRangeOutsideClick = '1';

        document.addEventListener('click', event => {
            /* ------------------------------
               CLOSE DATE RANGE PICKERS
            ------------------------------ */
            document.querySelectorAll('.date-range-picker.open').forEach(picker => {
                if (picker.contains(event.target)) {
                    return;
                }

                picker.classList.remove('open');

                const trigger = picker.querySelector('.badge-date-range-trigger');

                if (trigger) {
                    trigger.classList.remove('active');
                }
            });

            /* ------------------------------
               CLOSE TIME DROPDOWNS
            ------------------------------ */
            document.querySelectorAll('.date-range-time-dropdown.open').forEach(dropdown => {
                const group = dropdown.closest('.date-range-time-group');

                if (group && group.contains(event.target)) {
                    return;
                }

                dropdown.classList.remove('open');
            });
        });
    }
}
