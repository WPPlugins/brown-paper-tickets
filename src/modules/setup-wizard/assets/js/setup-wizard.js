(function($) {
    'use strict';

    var bptAPI,
        BptWizardNav,
        bptSetupWizard;

    bptAPI = {
        getAccount: function getAccount() {

            $('.bpt-loading').fadeIn();

            $.ajax(
                bptSetupWizardAjax.ajaxurl,
                {
                    type: 'POST',
                    data: {
                        // wp ajax action
                        action : 'bpt_test_account',
                        // vars
                        devID : $('input[name="_bpt_dev_id"]').val(),
                        clientID : $('input[name="_bpt_client_id"]').val(),
                        // send the nonce along with the request
                        nonce: bptSetupWizardAjax.nonce,
                    },
                    accepts: 'json',
                    dataType: 'json'

                }
            )
            .always(function() {
                $('.bpt-loading').hide();
            })
            .fail(function(data) {
                bptSetupWizard.set({
                    unknownError: data
                });
            })
            .done(function(data) {

                if ( data.error === 'No Developer ID.') {
                    bptSetupWizard.set({
                        accountError: data.error
                    });

                    return;
                }

                if (data.account.result || data.events.result) {

                    if (data.account.result) {
                        bptSetupWizard.set({
                            accountError: data.account
                        });
                    }

                    if (data.events.result) {
                        bptSetupWizard.set({
                            eventError: data.events
                        });
                    }

                    if (!data.events.result) {

                        bptSetupWizard.set({
                            events: data.events
                        });

                    } else {

                        bptSetupWizard.set({
                            events: undefined
                        });

                    }

                    if (!data.account.result) {
                        bptSetupWizard.set({
                            account: data.account,
                        });
                    } else {
                        bptStupWizard.set({
                            account: undefined,
                        });
                    }

                    return;
                }

                bptSetupWizard.set({
                    account: data.account,
                    events: data.events,
                    accountError: undefined,
                    eventError: undefined
                });

            })
            .always(function() {

            });

        },
        saveSettings: function saveSettings() {
            var settings = $('#bpt-setup-wizard-form').serialize();
            $.post( 'options.php', settings)
            .always(function() {

            })
            .fail(function() {
                throw new Error('Saving... Failed!');
            })
            .done(function(data) {
                $('.bpt-setup-wizard-save').text('Saved');

                setTimeout(function() {
                    $('.bpt-setup-wizard-save').text('Save Settings');
                }, 3000);

            });
        }
    };


    /**
     * bptWizardNav handles hiding/showing the previous/next element
     * named the same class.
     *
     * @param  string prevButton     The element you would like to use for the
     *                               next buttons.
     * @param  string nextButton     The element you would like to use for the
     *                               previous buttons.
     * @param  string stepContainers The selector used for each step.
     * @return void
     */
    BptWizardNav = function BptWizardNav(prevButton, nextButton, stepContainers) {

        this.init = function init () {

            var parent = this;

            this.setStepContainers(stepContainers);

            $(stepContainers).hide();

            $(this.stepContainers[0]).fadeIn(500);

            $(nextButton).click(function(event) {

                var currentStep = parent.currentStep;

                event.preventDefault();

                parent.nextStep(currentStep);

            });

            $(prevButton).click(function(event) {

                var currentStep = parent.currentStep;

                event.preventDefault();

                parent.prevStep(currentStep);
            });


        };

        this.currentStep = 0;

        this.stepContainers = [];

        this.setStepContainers = function setStepContainers(stepContainers) {
            this.stepContainers =  $(stepContainers).toArray();
        };

        this.nextStep = function nextStep(currentStep) {
            var parent = this;
            $(this.stepContainers[currentStep]).fadeOut(500, function() {

                $(parent.stepContainers[currentStep + 1]).fadeIn(500);

            });

            this.setCurrentStep(currentStep + 1);

        };

        this.prevStep = function prevStep(currentStep) {
            var parent = this;

            $(this.stepContainers[currentStep]).fadeOut(500, function() {

                $(parent.stepContainers[currentStep - 1]).fadeIn(500);

            });

            this.setCurrentStep(currentStep - 1);
        };

        this.goToStep = function goToStep(step) {
            var currentStep = this.currentStep;

            $(this.stepContainers[currentStep]).fadeOut(500, function() {

                $(parent.stepContainers[step]).fadeIn(500);

            });

            this.setCurrentStep(step);
        };

        this.setCurrentStep = function setCurrentStep(step) {
            this.currentStep = step;
        };

        this.init();
    };

    $(document).ready(function() {
        Ractive.DEBUG = false;
        bptSetupWizard = new Ractive({
            el: '#bpt-setup-wizard-response',
            template: '#bpt-setup-wizard-template',
            data: {
                unescapeHTML: function unescapeHTML(html) {
                    return _.unescape(html);
                },
                explainError: function explainError(errorCode, type) {

                    if (type === 'account') {

                        if ( errorCode === '100003' ) {
                            return 'It looks like the Developer ID provided is not authorized to access that Client ID.';
                        }

                        if (this.data.eventError.code === '100003') {
                            return 'There is an issue with pulling in event information using that Developer ID.' +
                                   'This usually means that the Developer ID is incorrect.';
                        }


                        if ( errorCode === '0' ) {
                            return 'No events could be pulled up with that Client ID.';
                        }

                        return 'Unknown Error';
                    }

                    if (type === 'events') {

                        if ( errorCode === '100003' ) {
                            return 'The Developer ID is not valid.';
                        }

                        if ( errorCode === '0' ) {
                            return 'No events could be pulled up with that Client ID.';
                        }
                    }
                },
                liveEvents: function liveEvents(events) {

                    var live = 0;

                    for (var i = 0; i < events.length; i++ ) {
                        if (events[i].live) {
                            live++;
                        }
                    }

                    return live;
                }
            }
        });

        $('.bpt-setup-wizard-test-account').click(function(event) {
            event.preventDefault();
            bptAPI.getAccount();
        });

        $('.bpt-setup-wizard-next-step').click(function(event) {
            event.preventDefault();
        });

        $('.bpt-setup-wizard-previous-step').click(function(event) {
            event.preventDefault();
        });

        $('.bpt-setup-wizard-save').click(function(event) {
            event.preventDefault();
            bptAPI.saveSettings();
        });

        $('.bpt-setup-wizard-toggle-settings').click(function(event) {
            event.preventDefault();
            $('.bpt-setup-wizard-advanced-settings').toggle();
        });

        var stepNav = new BptWizardNav('.bpt-setup-wizard-prev-step', '.bpt-setup-wizard-next-step', '.bpt-setup-wizard');

    });

})(jQuery);
