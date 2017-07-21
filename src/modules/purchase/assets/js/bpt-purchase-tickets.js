(function($) {
    'use strict';

    var ManageCart = function(options) {

        var ajaxOptions = {
                url: bptPurchaseTickets.ajaxurl,
                type: 'POST',
                dataType: 'json'
            },
            shoppingCart = options.shoppingCart,
            addTickets,
            getCartContents,
            parseTicketForm,
            addShippingInfo,
            addBillingInfo,
            removeTicket,
            updateCart;


        if (!options.stage) {
            options.stage = 'getCartInfo';
        }

        if (!options.form) {
            options.form = null;
        }

        this.getCartContents = function() {

            ajaxOptions.type = 'POST';

            ajaxOptions.data = {
                action: 'bpt_get_cart_contents',
                nonce: bptPurchaseTickets.nonce
            };

            $.ajax(ajaxOptions)
            .fail()
            .done(function(response) {

                for (var i in response.prices) {
                    if (response.prices[i].quantity === '0') {
                        delete response.prices[i];
                    }
                }

                shoppingCart.set({
                    prices: response.prices,
                    cartValue: response.cartValue
                });

            })
            .always(function() {
                shoppingCart.set('loading', false);
            });
        };

        this.addPrices = function(form) {
            var prices;

            if (!form) {
                prices = shoppingCart.get('prices');
            } else {
                prices = parseTicketForm(form);
            }

            for (var i = 0; i < prices.length; i++) {
                if (prices[i].quantity === '0') {
                    delete prices[i];
                }
            }

            ajaxOptions.type = 'POST';
            ajaxOptions.data = {
                action: 'bpt_add_prices',
                prices: prices,
                nonce: bptPurchaseTickets.nonce,
            };

            $.ajax(ajaxOptions)
            .done(function(data) {

                options.shoppingCart.set({
                    message: data.message,
                    prices: data.prices,
                    cartValue: data.cartValue
                });

                if (data.message) {
                    var messageDiv = $('#bpt-shopping-cart-message');

                    messageDiv.fadeIn();

                    window.setTimeout(function() {
                        messageDiv.fadeOut();
                    }, 5000);
                }

                
            })
            .always(function(data) {
                shoppingCart.set('loading', false);
            })
            .fail(function(xhr) {
                shoppingCart.set({
                    error: xhr.responseText
                });
            });
        };

        this.updateCart = updateCart = function(prices) {
            this.addPrices();
        };

        this.removeTicket = removeTicket = function(tickets) {
            var ticketsToRemove = {};

            if (!Array.isArray(tickets)) {
                var priceID = tickets.priceID.toString();
                ticketsToRemove[priceID] = tickets;
            }

            ajaxOptions.data = {
                action: 'bpt_remove_price',
                tickets: ticketsToRemove,
                nonce: bptPurchaseTickets.nonce,
                stage: 'removeTickets'
            };

            shoppingCart.set('loading', true);

            $.ajax(ajaxOptions)
            .done(function(data) {

                options.shoppingCart.set({
                    message: data.message,
                    prices: data.prices,
                    cartValue: data.cartValue
                });

                if (data.message) {
                    var messageDiv = $('#bpt-shopping-cart-message');

                    messageDiv.fadeIn();

                    window.setTimeout(function() {
                        messageDiv.fadeOut();
                    }, 5000);
                }
            })
            .always(function(data) {
                shoppingCart.set('loading', false);
            })
            .fail(function(xhr) {
                shoppingCart.set({
                    error: xhr.responseText
                });
            });
        };

        /**
         * Parse the tickets selected in the given form.
         * @param  object form The form object. It must be a jquery object.
         * @return array       An array of selected tickets, each containing
         *                     a priceId, shippingMethod, quantity, value and
         *                     name parameter.
         */
        parseTicketForm = function(form) {
            var eventId = form.data('event-id'),
                prices = form.find('select.bpt-price-qty'),
                shippingMethod = form.find('select.bpt-shipping-method').val(),
                parsedPrices = {};

                prices.each(function(i, price) {
                    price = $(price);
                    var priceTd = price.parent(),
                        priceValue = priceTd.siblings('td.bpt-price-value').data('price-value'),
                        priceName = priceTd.siblings('td.bpt-price-name').data('price-name'),
                        eventTitle = priceTd.siblings('td.bpt-price-name').data('event-title');

                    if (price.val() !== '0') {
                        parsedPrices[price.data('price-id')] = {
                            priceId: price.data('price-id'),
                            shippingMethod: shippingMethod,
                            quantity: price.val(),
                            value: priceValue,
                            name: priceName,
                            eventTitle: eventTitle,
                            eventId: eventId
                        };
                    }
                });

                return parsedPrices;
        };

    };

    $(document).on('bptEventListLoaded', function(event) {
        var eventForms = $('.add-to-cart'),
            template,
            shoppingCart,
            manageCart;


        $('div.bpt-event-list').prepend('<div id="bpt-shopping-cart"></div>');

        $.ajax({
            url: bptPurchaseTickets.templateUrl
        })
        .fail(function(xhr) {
            template = 'Sorry, the shopping cart could not be loaded.';
        })
        .done(function(data) {
            template = $(data).html();
        })
        .always(function() {

            shoppingCart = new Ractive({
                el: '#bpt-shopping-cart',
                template: template,
                data: {
                    prices: [],
                    shippingInfo: {
                        firstName: '',
                        lastName: '',
                        email: '',
                        phone: '',
                        address: '',
                        address2: '',
                        city: '',
                        state: '',
                        country: ''
                    },
                    billingInfo: {
                        firstName: '',
                        lastName: '',
                        email: '',
                        phone: '',
                        address: '',
                        address2: '',
                        city: '',
                        state: '',
                        country: ''
                    },
                    countries: [
                        'Afghanistan',
                        'Aland Islands',
                        'Albania',
                        'Algeria',
                        'American Samoa',
                        'Andorra',
                        'Angola',
                        'Anguilla',
                        'Antarctica',
                        'Antigua And Barbuda',
                        'Argentina',
                        'Armenia',
                        'Aruba',
                        'Australia',
                        'Austria',
                        'Azerbaijan',
                        'Azores',
                        'Bahamas',
                        'Bahrain',
                        'Bangladesh',
                        'Barbados',
                        'Belarus',
                        'Belgium',
                        'Belize',
                        'Benin',
                        'Bermuda',
                        'Bhutan',
                        'Bolivia',
                        'Bosnia And Herzegovina',
                        'Botswana',
                        'Bouvet Island',
                        'Brazil',
                        'British Indian Ocean Territory',
                        'Brunei Darussalam',
                        'Bulgaria',
                        'Burkina Faso',
                        'Burundi',
                        'Cambodia',
                        'Cameroon',
                        'Canada',
                        'Cape Verde',
                        'Cayman Islands',
                        'Central African Republic',
                        'Chad',
                        'Chile',
                        'China',
                        'Christmas Island',
                        'Cocos (keeling) Islands',
                        'Colombia',
                        'Comoros',
                        'Congo',
                        'Congo, The Democratic Republic Of The',
                        'Cook Islands',
                        'Costa Rica',
                        'Cote Divoire',
                        'Croatia',
                        'Cyprus',
                        'Czech Republic',
                        'Denmark',
                        'Djibouti',
                        'Dominica',
                        'Dominican Republic',
                        'Ecuador',
                        'Egypt',
                        'El Salvador',
                        'Equatorial Guinea',
                        'Eritrea',
                        'Estonia',
                        'Ethiopia',
                        'Falkland Islands',
                        'Faroe Islands',
                        'Fiji',
                        'Finland',
                        'France',
                        'French Guiana',
                        'French Polynesia',
                        'French Southern Territories',
                        'Gabon',
                        'Gambia',
                        'Georgia',
                        'Germany',
                        'Ghana',
                        'Gibraltar',
                        'Greece',
                        'Greenland',
                        'Grenada',
                        'Guadeloupe',
                        'Guam',
                        'Guatemala',
                        'Guernsey',
                        'Guinea',
                        'Guinea-Bissau',
                        'Guyana',
                        'Haiti',
                        'Heard Island And Mcdonald Islands',
                        'Holy See',
                        'Honduras',
                        'Hong Kong',
                        'Hungary',
                        'Iceland',
                        'India',
                        'Indonesia',
                        'Iraq',
                        'Ireland',
                        'Isle Of Man',
                        'Israel',
                        'Italy',
                        'Jamaica',
                        'Japan',
                        'Jersey',
                        'Jordan',
                        'Kazakhstan',
                        'Kenya',
                        'Kiribati',
                        'Korea, Republic Of',
                        'Kosovo',
                        'Kyrgyzstan',
                        'Latvia',
                        'Lebanon',
                        'Lesotho',
                        'Liberia',
                        'Libyan Arab Jamahiriya',
                        'Liechtenstein',
                        'Lithuania',
                        'Luxembourg',
                        'Macao',
                        'Macedonia, The Former Yugoslav Republic Of',
                        'Madagascar',
                        'Madeira',
                        'Malawi',
                        'Malaysia',
                        'Maldives',
                        'Mali',
                        'Malta',
                        'Marshall Islands',
                        'Martinique',
                        'Mauritania',
                        'Mauritius',
                        'Mayotte',
                        'Mexico',
                        'Micronesia, Federated States Of',
                        'Moldova',
                        'Monaco',
                        'Mongolia',
                        'Montenegro',
                        'Montserrat',
                        'Morocco',
                        'Mozambique',
                        'Myanmar',
                        'Namibia',
                        'Nauru',
                        'Nepal',
                        'Netherlands',
                        'Netherlands Antilles',
                        'New Caledonia',
                        'New Zealand',
                        'Nicaragua',
                        'Niger',
                        'Nigeria',
                        'Niue',
                        'Norfolk Island',
                        'Northern Mariana Islands',
                        'Norway',
                        'Oman',
                        'Pakistan',
                        'Palau',
                        'Palestinian Territory, Occupied',
                        'Panama',
                        'Papua New Guinea',
                        'Paraguay',
                        'Peru',
                        'Philippines',
                        'Pitcairn',
                        'Poland',
                        'Portugal',
                        'Puerto Rico',
                        'Qatar',
                        'Réunion',
                        'Romania',
                        'Russian Federation',
                        'Rwanda',
                        'Saint Barthélemy',
                        'Saint Helena',
                        'Saint Kitts And Nevis',
                        'Saint Lucia',
                        'Saint Martin',
                        'Saint Pierre And Miquelon',
                        'Saint Vincent And The Grenadines',
                        'Samoa',
                        'San Marino',
                        'Sao Tome And Principe',
                        'Saudi Arabia',
                        'Senegal',
                        'Serbia',
                        'Seychelles',
                        'Sierra Leone',
                        'Singapore',
                        'Slovakia',
                        'Slovenia',
                        'Solomon Islands',
                        'Somalia',
                        'South Africa',
                        'South Georgia And The South Sandwich Islands',
                        'Spain',
                        'Sri Lanka',
                        'Suriname',
                        'Svalbard And Jan Mayen',
                        'Swaziland',
                        'Sweden',
                        'Switzerland',
                        'Taiwan',
                        'Tajikistan',
                        'Tanzania, United Republic Of',
                        'Thailand',
                        'Timor-Leste',
                        'Togo',
                        'Tokelau',
                        'Tonga',
                        'Trinidad And Tobago',
                        'Tunisia',
                        'Turkey',
                        'Turkmenistan',
                        'Turks And Caicos Islands',
                        'Tuvalu',
                        'Uganda',
                        'Ukraine',
                        'United Arab Emirates',
                        'United Kingdom',
                        'United States',
                        'United States Minor Outlying Islands',
                        'Uruguay',
                        'Uzbekistan',
                        'Vanuatu',
                        'Venezuela',
                        'Vietnam',
                        'Virgin Islands, British',
                        'Virgin Islands, US',
                        'Wallis And Futuna',
                        'Western Sahara',
                        'Yemen',
                        'Zambia',
                        'Zimbabwe'
                    ],
                    showShipping: true,
                    billingIsShipping: true,
                    currency: function(currency) {
                        if (currency === 'USD') {
                            return '$';
                        }

                        if (currency === 'CAD') {
                            return 'CAD$';
                        }

                        if (currency === 'EUR') {
                            return '€';
                        }

                        if (currency === 'GBP') {
                            return '£';
                        }

                        return currency;
                    }
                }
            });

            shoppingCart.on({
                removeTicket: function(element) {
                    var price = shoppingCart.get(element.keypath);

                        price.quantity = '0';

                        manageCart.updateCart();
                },
                updateCart: function(element) {
                    manageCart.addPrices();
                },
                checkout: function(element) {
                    manageCart.addPrices();
                    shoppingCart.set('showShipping', true);
                },
                billingIsShipping: function(element) {

                    if (element.node.checked) {
                        shoppingCart.set('billingIsShipping', true);
                        shoppingCart.set('billingInfo', shoppingCart.get('shippingInfo'));
                        return;
                    }
                    
                    shoppingCart.set('billingIsShipping', false);
                }
            });

            shoppingCart.observe('shippingInfo', function(newValue, oldValue, keypath) {
                if (shoppingCart.get('billingIsShipping')) {
                    shoppingCart.set('billingInfo', shoppingCart.get('shippingInfo'));
                }
            });

            var options = {
                shoppingCart: shoppingCart
            };

            manageCart = new ManageCart(options);

            manageCart.getCartContents();
        });

        eventForms.each(function(i, form) {
            form = $(form);

            var submitButton = form.find('.bpt-submit'),
                postID = form.parent().parent().data('post-id');

            submitButton.click(function(event) {
                event.preventDefault();

                manageCart.addPrices(form);
            });
        });
    });

})(jQuery);