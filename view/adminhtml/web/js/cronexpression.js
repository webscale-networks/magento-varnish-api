/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

define(["jquery"], function($) {
    "use strict";

    let note = $('#row_webscale_varnish_flush_cache_schedule_frequency .note span')
    let items = {
        'frequency': '#webscale_varnish_flush_cache_schedule_frequency',
        'everytext': '#webscale_varnish_flush_cache_schedule_every',
        'everytime': '#webscale_varnish_flush_cache_schedule_time',
        'timehour': '[data-ui-id="time-groups-flush-cache-schedule-fields-start-time-value-hour"]',
        'timeminute': '[data-ui-id="time-groups-flush-cache-schedule-fields-start-time-value-minute"]',
        'timesecond': '[data-ui-id="time-groups-flush-cache-schedule-fields-start-time-value-second"]'
    };

    let init = function () {
        if (note && note.length) {
            // $(items.timesecond).prop('disabled', 'disabled');
            return bind();
        }
    }

    let bind = function() {
        for (const key in items) {
            const instance = $(items[key]);
            instance.on('change', function (e) {
                return change(e, instance);
            }.bind(instance));
        }

        $(items.frequency).trigger('change');
    }

    /**
     * Trigger change event and combine cron expression
     */
    let change = function(e, element) {
        let value = '',
            frequency = $(items.frequency),
            timeMinute = $(items.timeminute),
            timeHour = $(items.timehour),
            everyText = $(items.everytext),
            everyTime = $(items.everytime);

        switch(frequency.val()) {
            case 'H':
                value = '0 * * * *';
                break;
            case 'D':
                value = parseInt($(timeMinute).val(), 10) + ' ' + parseInt($(timeHour).val(), 10) + ' * * *';
                break;
            case 'custom':
                if (everyText.val() !== "undefined" && everyText.val() !== '') {
                    let txtValue = everyText.val();
                    if (everyTime.val() == 'hour') {
                        value = '0 */' + txtValue + ' * * *';
                    } else if (everyTime.val() == 'min') {
                        value = '*/' + txtValue + ' * * * *';
                    }
                }
                break;

        }

        note.html('Cron Expression: <a href="https://crontab.guru/#' + value.replace(' ', '_') + '" target="_blank">' +  (value ? '<code style="letter-spacing: 2px;">' + value + '</code></a>' : '<span style="color:grey;">none</span>'));
    }

    return init();
});
