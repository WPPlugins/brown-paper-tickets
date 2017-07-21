(function($) {
	'use strict';
	var BptEventList;

	BptEventList = function BptEventList(eventListOptions) {

		var self = this,
			bpt,
			init,
			showOrHidePrice,
			clickHidePrice,
			getEvents,
			postID = eventListOptions.postID,
			allEvents = [],
			eventList,
			setPriceMaxQuantity,
			setPriceIntervals,
			toggleFee;

		Ractive.DEBUG = false;
		eventList = new Ractive({
			el: '#bpt-event-list-' + postID,
			template: '#bpt-event-template',
			data: {
				formatDate: function formatDate(newFormat, date) {
					var singleDate = moment(date, 'YYYY-MM-DD');
					return singleDate.format(newFormat);
				},

				formatTime: function formatTime(newFormat, time) {
					var singleTime = moment(time, 'H:mm');
					return singleTime.format(newFormat);
				},

				unescapeHTML: function unescapeHTML(html) {
					return _.unescape(html);
				},

				/**
				 * Takes a price string and a currency symbol, splits the price
				 * into an array at the decimal point and replaces the "." with
				 * a "," if the currency symbol is the €.
				 * the proper
				 * @param {string} price    A price string with a "." separator.
				 * @param {string} currency The currency Symbol.
				 */
				formatPrice: function formatPrice(price, currency) {
					var separator = '.',
						priceArr;

					if (currency === '€' ) {
						separator = ',';
					}

					if (price === 0) {
						return 'Free';
					}

					priceArr = price.toString().split('.');

					if (!priceArr[1]) {
						price = priceArr[0] + separator + '00';
					} else {
						price = priceArr[0] + separator + priceArr[1];
					}

					return currency + '' + price.toString();
				},
				/**
				 * Formats the service fee using @formatPrice if the service
				 * fee isn't free/0.
				 * @param {string} fee      Same as @formatPrice
				 * @param {string} currency Same as @currency
				 */
				formatServiceFee: function(fee, currency) {
					if (!fee) {
						return;
					}

					return formatPrice(fee, currency);
				},
				priceValue: function(price) {
					var value = price.value;
					if (price.includeFee) {
						value = price.value + price.serviceFee;
					}

					return value;
				},
				getQuantityOptions: function(price) {

					var options,
						total = price.maxQuantity || 20,
						interval = price.interval || 1,
						i = 0;

					if (!_.isNumber(interval)) {
						interval = parseInt(interval);
					}

					if (!_.isNumber(total)) {
						total = parseInt(total);
					}

					while (i <= total) {
						options = options + '<option value="' + i + '">' + i + '</option>';
						i = i + interval;
					}

					return options;
				},
				isHidden: function isHidden(hidden) {

					if (hidden) {
						return 'bpt-hidden-price';
					}
				}
			}
		});

		eventList.on({
			showFullDescription: function showFullDescription(event) {
				event.original.preventDefault();
				$(event.node)
					.parent()
					.next('.bpt-event-full-description')
					.toggleClass('hidden');
			},
			hidePrice: function(event) {
				showOrHidePrice(event);
			},
			unhidePrice: function(event) {
				showOrHidePrice(event, true);
			},
			setPriceMaxQuantity: function(event) {
				setPriceMaxQuantity(event);
			},
			togglePriceVisibility: function(event) {
				if (event.node.value === 'true') {
					showOrHidePrice(event, true);
				} else {
					showOrHidePrice(event);
				}
			},
			setPriceIntervals: function(event) {
				setPriceIntervals(event);
			},
			toggleFee: function(event) {
				var includeFee = true;

				if (event.node.value === 'false') {
					includeFee= false;
				}

				this.set(event.keypath + '.includeFee', includeFee);
				toggleFee(event);
			}
		});

		getEvents = function(){
			var	bptData = {
					action: 'bpt_get_events',
					nonce: eventListOptions.bptNonce,
					postID: eventListOptions.postID
				};

				if ( eventListOptions.clientID ) {
					bptData.clientID = eventListOptions.clientID;
				}

				if ( eventListOptions.eventID ) {

					bptData.eventID = eventListOptions.eventID;
				}

				$('div.bpt-loading-' + postID).fadeIn();

				$.ajax(
					eventListOptions.ajaxurl,
					{
						type: 'GET',
						data: bptData,
						accepts: 'json',
						dataType: 'json'
					}
				)
				.always(function() {
					$('div.bpt-loading-' + postID).hide();
				})
				.fail(function() {

					eventList.set({
						error: 'Unknown Error'
					});

				})
				.done(function(data) {
					if (data.error) {

						eventList.set(
							{
								error: data
							}
						);

					}

					if ( !data.error ) {
						eventList.set('events', data);

						var events = eventList.get('events');

						for (var i = 0; i < events.length; i++) {
							var currentEvent = events[i];
							eventList.set('events[' + i +'].selectedDate', currentEvent.dates[0]);
						}

						$(document).trigger('bptEventListLoaded');
					}
				})
				.always(function() {

				});
		};


		showOrHidePrice = function(event, showPrice) {
			var priceLink = $(event.original.target),
				price = {
					priceId: priceLink.data('price-id'),
					priceName: priceLink.data('price-name'),
					eventTitle: priceLink.parents('form').data('event-title'),
					eventId: priceLink.parents('form').data('event-id')
				},
				ajaxAction = 'bpt_hide_prices',
				dateKeyPath = event.keypath.replace('.selectedDate', '.dates') + '.hidden',
				selectedKeyPath =  event.keypath + '.hidden';

			event.original.preventDefault();

			if (!showPrice) {
				showPrice = false;
			}

			if (showPrice) {
				ajaxAction = 'bpt_unhide_prices';
			}

			$.ajax(
				eventListOptions.ajaxurl,
				{
					type: 'POST',
					data: {
						action: ajaxAction,
						nonce: eventListOptions.bptNonce,
						prices: [price]
					},
					accepts: 'json',
					dataType: 'json'
				}
			).always(function() {

			}).done(function(data) {

				if (data.success) {
					if (showPrice) {
						eventList.set(dateKeyPath, false);
						eventList.set(selectedKeyPath, false);
					} else {
						eventList.set(dateKeyPath, true);
						eventList.set(selectedKeyPath, true);
					}
				}

			}).fail();
		};

		setPriceMaxQuantity = function(event) {
			event.original.preventDefault();

			var quantity = {},
				id = event.context.id.toString(),
				max = event.original.target.value,
				maxQuantity = {},
				data = {
					bptNonce: eventListOptions.bptNonce,
					action: 'bpt_set_price_max_quantity',
					maxQuantity: [],
				};

			maxQuantity[id] = max;

			data.maxQuantity.push(maxQuantity);

			if (!max) {
				max = 0;
			}

			$.ajax(
				eventListOptions.ajaxurl,
				{
					type: 'POST',
					data: data
				}
			).always(function() {

			}).done(function(data) {

			});
		};

		setPriceIntervals = function(event) {
			event.original.preventDefault();

			var id = event.context.id,
				interval = event.original.target.value,
				price = {},
				data = {
					bptNonce: eventListOptions.bptNonce,
					action: 'bpt_set_price_intervals',
					intervals: []
				};

			price[id] = interval;

			data.intervals.push(price);

			$.ajax(
				eventListOptions.ajaxurl,
				{
					type: 'POST',
					data: data
				}
			).always()
			.done();
		};

		toggleFee = function(event) {
			event.original.preventDefault();

			var id = event.context.id,
				includeFee = event.original.target.value,
				price = {},
				data = {
					bptNonce: eventListOptions.bptNonce,
					action: 'bpt_set_price_include_fee',
					includeFee: []
				};

			price[id] = includeFee;

			data.includeFee.push(price);

			$.ajax(
				eventListOptions.ajaxurl,
				{
					type: 'POST',
					data: data
				}
			).always()
			.done();
		};

		init = function() {
			getEvents();
		};

		init();
	};

	$(document).ready(function() {

		var eventListContainers = $('.bpt-event-list'),
			eventLists = [];

		eventListContainers.each(function() {
			var postId = $(this).data('post-id').toString(),
				eventListOptions = window['bptEventFeedAjaxPost' + postId];

			eventLists['post' + postId] = new BptEventList(eventListOptions);

		});
	});

})(jQuery);
