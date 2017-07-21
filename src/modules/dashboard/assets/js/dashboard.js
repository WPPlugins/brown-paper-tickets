(function($) {
    'use strict';

    var bptWelcomePanel,
        allOptions,
        admin;

    admin = {
        getAccount: function getAccount() {
            $.ajax(
                bptDashboard.ajaxurl,
                {
                    type: 'GET',
                    data: {
                        // wp ajax action
                        action : 'bpt_dashboard_get_account',
                        // varsx
                        // send the nonce along with the request
                        nonce : bptDashboard.nonce,
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
        getAllOptions: function getAllOptions(event) {
            event.preventDefault();
            var resultsBox = $('#debug-options-results');
            $.ajax(
                bptDashboard.ajaxurl,
                {
                    type: 'GET',
                    data: {
                        action: 'bpt_dashboard_get_all_options',
                        nonce: bptDashboard.nonce,
                    },
                }
            )
            .always(function(){})
            .done(function(data) {
                allOptions.set('options', JSON.stringify(data));
            })
            .fail(function(){});
        },
    };

    $(document).ready(function() {
        $('.bpt-welcome-panel-close').click(function(event) {
            event.preventDefault();
            $('.bpt-welcome-panel').toggle();
        });

        $('a#get-all-options').click(admin.getAllOptions);

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
    });
})(jQuery);
