(function($) {
    'use strict';

    var bptAPI,
        BptWizardNav,
        bptSetupWizard;

    bptAPI = {
        getAccount: function getAccount(dev) {

            if (dev) {
                eventList.set({
                    //bptEvents: [{"id":153529,"title":"Test Event","live":true,"address1":"Fremont Abbey Arts Center","address2":"4272 Fremont Ave North","city":"Seattle","state":"CA","zip":98103,"shortDescription":"An event with HTML in the event description and a seating chart attached.","fullDescription":"This is a full description!\n\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit. Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue. Nam tincidunt congue enim, ut porta lorem lacinia consectetur. Donec ut libero sed arcu vehicula ultricies a non tortor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut gravida lorem. Ut turpis felis, pulvinar a semper sed, adipiscing id dolor. Pellentesque auctor nisi id magna consequat sagittis. Curabitur dapibus enim sit amet elit pharetra tincidunt feugiat nisl imperdiet. Ut convallis libero in urna ultrices accumsan. Donec sed odio eros. Donec viverra mi quis quam pulvinar at malesuada arcu rhoncus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In rutrum accumsan ultricies. Mauris vitae nisi at sem facilisis semper ac in est.\n&lt;!-- &lt;h1&gt;Test Event - This is an H1&lt;\/h1&gt;\n\n&lt;h2&gt;... and h2&lt;\/h2&gt;\n&lt;h3&gt;h3 in blue.&lt;\/h3&gt;\nAdd CSS styling to the Custom CSS field under Look & Feel on your &lt;a href=\"https:\/\/www.brownpapertickets.com\/user\/profile.html\"&gt;profile&lt;\/a&gt; (you will need to be logged in for that link to work).\nFor example, in order to make the h3 tag appear blue, you'd simply put the following into that field:\n\nh3 {\ncolor: #0000FF;\n}\n\n&lt;a href=\"http:\/\/www.google.com\" target=\"_blank\"&gt;A link&lt;\/a&gt;\n\n&lt;b&gt;Bold text&lt;\/b&gt;\n\n&lt;i&gt;Italicized text&lt;\/i&gt;\n\nUnordered List:\n&lt;ul&gt;\n&lt;li&gt;List 1&lt;\/li&gt;\n&lt;li&gt;List 2&lt;\/li&gt;\n&lt;li&gt;List 3&lt;\/li&gt;\n&lt;\/ul&gt;\n\nOrdered List:\n&lt;ol&gt;\n&lt;li&gt;List 1&lt;\/li&gt;\n&lt;li&gt;List 2&lt;\/li&gt;\n&lt;li&gt;List 3&lt;\/li&gt;\n&lt;\/ol&gt;\n\n&lt;b&gt;img tag:&lt;\/b&gt;\n&lt;img src=\"http:\/\/community.brownpapertickets.com\/event_images\/26755\/sagan1.jpg\" width=\"50%\" \/&gt;\n&lt;img src=\"http:\/\/community.brownpapertickets.com\/event_images\/26755\/sagan2.gif\" width=\"50%\" \/&gt;\n\n&lt;b&gt;youtube iframe embed:&lt;\/b&gt;\n&lt;iframe width=\"400\" height=\"315\" src=\"http:\/\/www.youtube.com\/embed\/_w5vs4KoDS4\" frameborder=\"0\" allowfullscreen&gt;&lt;\/iframe&gt;\n\n&lt;b&gt;BandCamp iframe embed:&lt;\/b&gt;\n&lt;iframe width=\"400\" height=\"100\" style=\"position: relative; display: block; width: 400px; height: 100px;\" src=\"http:\/\/bandcamp.com\/EmbeddedPlayer\/v=2\/album=1674716061\/size=venti\/bgcol=FFFFFF\/linkcol=4285BB\/\" allowtransparency=\"true\" frameborder=\"0\"&gt;&lt;a href=\"http:\/\/duckyboys.bandcamp.com\/album\/dark-days\"&gt;Dark Days by Ducky Boys&lt;\/a&gt;&lt;\/iframe&gt; --&gt;","dates":[{"id":796736,"dateStart":"2016-02-28","dateEnd":"2016-02-28","timeStart":"8:00","timeEnd":"8:00","live":true,"available":10000,"prices":[{"id":2327416,"name":"General","value":0,"serviceFee":0,"venueFee":0,"live":false},{"id":2217847,"name":"Table Test","value":0,"serviceFee":0,"venueFee":0,"live":false},{"id":2530779,"name":"Paid Fee","value":8.71,"serviceFee":1.29,"venueFee":0,"live":true}]},{"id":828525,"dateStart":"2017-04-25","dateEnd":"2017-04-25","timeStart":"0:00","timeEnd":"0:00","live":true,"available":10000,"prices":[{"id":2327401,"name":"password","value":0,"serviceFee":0,"venueFee":0,"live":true},{"id":2327400,"name":"Ticket Test","value":0,"serviceFee":0,"venueFee":0,"live":true},{"id":2327417,"name":"General","value":0,"serviceFee":0,"venueFee":0,"live":false},{"id":2327399,"name":"Table Test","value":0,"serviceFee":0,"venueFee":0,"live":true},{"id":2530780,"name":"Paid Fee","value":8.71,"serviceFee":1.29,"venueFee":0,"live":true},{"id":2327398,"name":"Donation","value":10,"serviceFee":0,"venueFee":0,"live":true}]}]},{"id":153633,"title":"Print Test","live":true,"address1":"McAfee Center, Saratoga High School","address2":"20300 Herriman Avenue","city":"Saratoga","state":"CA","zip":95070,"shortDescription":"Test","fullDescription":"Seating Chart Test","dates":[{"id":787913,"dateStart":"2015-02-14","dateEnd":"2015-02-15","timeStart":"0:00","timeEnd":"0:00","live":true,"available":10000,"prices":[{"id":2189299,"name":"General","value":0,"serviceFee":0,"venueFee":0,"live":true},{"id":2189301,"name":"GIMME MONEY","value":10,"serviceFee":0,"venueFee":0,"live":true}]}]},{"id":338036,"title":"TICKETY TEST","live":true,"address1":"Brown Paper Tickets","address2":"220 Nickerson St","city":"Seattle","state":"WA","zip":98109,"shortDescription":"Tickety test, the ticketing testing.","fullDescription":"YA KNOW.","dates":[{"id":787919,"dateStart":"2015-03-15","dateEnd":"2016-02-15","timeStart":"0:00","timeEnd":"0:00","live":true,"available":10000,"prices":[{"id":2189321,"name":"TESTING","value":0,"serviceFee":0,"venueFee":0,"live":true},{"id":2551986,"name":"General","value":10,"serviceFee":1.34,"venueFee":0,"live":true}]},{"id":888696,"dateStart":"2015-08-23","dateEnd":"2015-08-23","timeStart":"10:00","timeEnd":"11:00","live":true,"available":10000,"prices":[{"id":2551987,"name":"General","value":10,"serviceFee":1.34,"venueFee":0,"live":true}]}]},{"id":296684,"title":"& API Test Event  ","live":true,"address1":"Brown Paper Tickets","address2":"220 Nickerson St","city":"Seattle","state":"WA","zip":98102,"shortDescription":"Testing Testing","fullDescription":"Is this thing on?","dates":[{"id":741570,"dateStart":"2015-03-07","dateEnd":"2015-03-07","timeStart":"0:00","timeEnd":"0:00","live":true,"available":10000,"prices":[{"id":2027635,"name":"Physical Tickets","value":10,"serviceFee":1.34,"venueFee":0,"live":true}]},{"id":741571,"dateStart":"2015-03-07","dateEnd":"2015-03-07","timeStart":"0:00","timeEnd":"0:00","live":true,"available":10000,"prices":[{"id":2027637,"name":"Will Call Tickets","value":0,"serviceFee":0,"venueFee":0,"live":true}]},{"id":741572,"dateStart":"2015-03-07","dateEnd":"2015-03-07","timeStart":"0:00","timeEnd":"0:00","live":true,"available":10000,"prices":[{"id":2027641,"name":"Print At Home","value":0,"serviceFee":0,"venueFee":0,"live":true}]}]}]
                });

                return;
            }

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
                console.log(data);
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
