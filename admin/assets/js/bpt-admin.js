(function($) {
    'use strict';

    var navigation,
        customDateFormat,
        customTimeFormat,
        bptWelcomePanel,
        allOptions,
        admin,
        appearanceTab;

    navigation = {
        loadTab: function loadTab() {
            var currentTab = this.getAnchor;
            this.switchTabs(currentTab);
        },
        switchTabs: function hideTabs(tab) {
            var currentTab = tab,
                tabs = this.getTabs();

            if (!tab) {
                currentTab = tabs[0];
            }

            this.setAnchor(tab);

            $('#bpt-settings-wrapper').children('div').hide();

            if (!currentTab) {
                $('a[href="#account-setup"]').addClass('selected-tab');
                $('#bpt-settings-wrapper div:first-child').show();
                return;
            }

            $('div' + currentTab).show();
            $('a.bpt-admin-tab').removeClass('selected-tab');
            $('a[href="' + currentTab + '"]').addClass('selected-tab');
        },
        getAnchor: function getAnchor() {
            var anchor = window.location.hash.substring(1);

            if (!anchor) {
                return false;
            }

            anchor = '#' + anchor;

            return anchor;
        },
        getTabs: function getTabs() {
            var tabs = [];

            $('#brown_paper_tickets_settings ul li').each(function() {
               tabs.push($(this).children('a').attr('href'));
            });

            return tabs;
        },
        setAnchor: function setAnchor(tab) {

            if (tab === this.getAnchor()) {
                return;
            }

            document.location.replace(tab);

        }
    };

    appearanceTab = function() {
        var calendarHelp = $('#bpt-appearance-help-calendar'),
            eventListHelp = $('#bpt-appearance-help-event-list'),
            calendarCSS = $('#bpt-calendar-css'),
            eventListCSS = $('#bpt-event-list-css'),
            bindings,
            init,
            showCSSBox;

        bindings = function() {
            $('#bpt-appearance-show-event-list-help').click(function(event) {
                event.preventDefault();

                eventListHelp.toggle(function(showOrHide) {

                    if (showOrHide) {
                        eventListHelp.slideDown();
                    }
                });
            });

            $('#bpt-appearance-show-calendar-help').click(function(event) {
                event.preventDefault();

                calendarHelp.toggle(function(showOrHide) {

                    if (showOrHide) {
                        calendarHelp.slideDown();
                    }
                });
            });

            $('#bpt-event-list-use-style').click(function(event) {
                showCSSBox(event);
            });

            $('#bpt-calendar-use-style').click(function(event) {
                showCSSBox(event, true);
            });
        };

        showCSSBox = function(event, calendar) {
            if (!event) {
                var eventListUseStyle = $('#bpt-event-list-use-style'),
                    calendarUseStyle = $('#bpt-calendar-use-style');

                if (!eventListUseStyle.prop('checked')) {
                    eventListCSS.hide();
                }

                if (!calendarUseStyle.prop('checked')) {
                    calendarCSS.hide();
                }

                return;
            }

            if (calendar) {

                if (event.target.checked) {

                    calendarCSS.show();

                } else {

                    calendarCSS.hide();

                }

            } else {

                if (event.target.checked) {

                    eventListCSS.show();

                } else {

                    eventListCSS.hide();

                }

            }
        };

        init = function() {
            calendarHelp.hide();
            eventListHelp.hide();
            showCSSBox();
            bindings();
        };

        init();
    };

    customDateFormat = function() {

        var selectedDateFormat = $('select#date-format option').filter(':selected');

        if (selectedDateFormat.val() === 'custom') {
            $('input#custom-date-format-input').removeClass('hidden');
        } else {
            $('input#custom-date-format-input').addClass('hidden');
        }
    };

    customTimeFormat = function() {

        var selectedTimeFormat = $('select#time-format option').filter(':selected');

        if (selectedTimeFormat.val() === 'custom') {
            $('input#custom-time-format-input').removeClass('hidden');
        } else {
            $('input#custom-time-format-input').addClass('hidden');
        }
    };

    admin = {
        getAccount: function getAccount() {
            $.ajax(
                bptWP.ajaxurl,
                {
                    type: 'GET',
                    data: {
                        // wp ajax action
                        action : 'bpt_get_account',
                        // varsx
                        // send the nonce along with the request
                        nonce : bptWP.nonce,
                        bptData: 'account',
                    },
                    accepts: 'json',
                    dataType: 'json'

                }
            ).done(function(data) {
                bptWelcomePanel.set({
                    account: data
                });
            }).fail(function(data) {
                bptWelcomePanel.set({
                    error: data
                });
            });
        },
        deleteCache: function deleteCache() {
            $.ajax(
                bptWP.ajaxurl,
                {
                    type: 'POST',
                    data: {
                        // wp ajax action
                        action : 'bpt_delete_cache',
                        // vars
                        // send the nonce along with the request
                        bptNonce : bptWP.nonce,
                    },
                    accepts: 'json',
                    dataType: 'json'

                }
            )
            .always(function() {
                $('.bpt-loading').hide();

            }).done(function(data) {

                $('.bpt-loading').hide();
                $('#bpt-refresh-events').show();

                $('.bpt-advanced-options .bpt-success-message')
                .text(data.message)
                .fadeIn(500)
                .delay(2000)
                .fadeOut(500);
                // bptWelcomePanel.set({
                //     request: {
                //         result: data.result,
                //         message: data.message
                //     }
                // });
            }).fail(function(data) {
                $('.bpt-advanced-options .bpt-error-message')
                .text(data.message)
                .fadeIn(500)
                .delay(2000)
                .fadeOut(500);
            });
        },
        unhidePrice: function unhidePrice(event) {
            var priceLink = $(event.target),
                price = {
                    priceId: priceLink.data('price-id')
                };

            $.ajax(
                bptWP.ajaxurl,
                {
                    type: 'POST',
                    data: {
                        action: 'bpt_unhide_prices',
                        nonce: bptWP.nonce,
                        admin: true,
                        prices: [price]
                    },
                    accepts: 'json',
                    dataType: 'json'
                }
            )
            .always(function() {

            })
            .done(function(data) {
                if (data.success) {
                    priceLink.parent().parent().fadeOut();
                }
            })
            .fail(function(data) {

            });
        },
        getAllOptions: function getAllOptions(event) {
            event.preventDefault();
            var resultsBox = $('#debug-options-results');
            $.ajax(
                bptWP.ajaxurl,
                {
                    type: 'GET',
                    data: {
                        action: 'bpt_get_all_options',
                        nonce: bptWP.nonce,
                    },
                }
            )
            .always(function(){})
            .done(function(data) {
                allOptions.set('options', data);
            })
            .fail(function(){});
        },
    };

    $(document).ready(function() {
        navigation.switchTabs(navigation.getAnchor());

        $('a.bpt-admin-tab').click(function(e) {
            e.preventDefault();
            var tab = $(this).attr('href');
            navigation.switchTabs(tab);
        });

        customDateFormat();
        customTimeFormat();

        $('select#date-format').change(function() {
            customDateFormat();
        });

        $('select#time-format').change(function() {
            customTimeFormat();
        });

        $('.bpt-welcome-panel-close').click(function(event) {
            event.preventDefault();

            $('.bpt-welcome-panel').toggle();
        });

        $('#bpt-delete-cache').click(function(event) {
            event.preventDefault();
            $('.bpt-loading').show();
            admin.deleteCache();
        });

        $('a.bpt-unhide-price').click(function(event) {
            event.preventDefault();
            admin.unhidePrice(event);
        });

        $('a#get-all-options').click(admin.getAllOptions);
        $('a#test-api').click(admin.testApi);

        Ractive.DEBUG = false;

        bptWelcomePanel = new Ractive({
            el: '#greeting',
            template: '#bpt-welcome-panel-template',
            data: {}
        });

        allOptions = new Ractive({
            el: '#all-options-results',
            template: '#all-options-template',
            data: {options: []}
        });

        admin.getAccount();

        appearanceTab();
    });
})(jQuery);
