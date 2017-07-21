<?php
/**
 *  The MIT License (MIT)
 *
 *  Copyright (c) 2014 Brown Paper Tickets
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 *
 *  @category License
 *  @package  BptAPI
 *  @author   Chandler Blum <chandler@brownpapertickets.com>
 *  @license  MIT <http://mit-license.org/>
 *  @link     https://github.com/BrownPaperTickets/BptAPI.php
 **/

namespace BrownPaperTickets\APIv2;

/**
 * This class contains all the methods needed to get event information
 */
class EventInfo extends BptAPI
{
    /**
     * Get events.
     *
     * @param string  $username  The Brown Paper Tickets username.
     *                           Must be listed in Authorized Accounts
     * @param integer $eventID   Pass in the eventID if you wish to only
     *                           get info for that specific event.
     * @param boolean $getDates  Whether or not to get the Dates
     * @param boolean $getPrices Whether or not to get the Prices
     *
     * @return array  $events An array of events information.
     */
    public function getEvents(
        $username = null,
        $eventID = null,
        $getDates = false,
        $getPrices = false
    ) {
        $apiOptions = array(
            'endpoint' => 'eventlist'
        );

        if (isset($username)) {
            $apiOptions['client'] = $username;
        }

        if (isset($eventID)) {

            $apiOptions['event_id'] = $eventID;
        }

        $eventsXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($eventsXML['error'])) {
            $this->setError('getEvents', $eventsXML['error']);
            return false;
        }

        $events = array();

        foreach ($eventsXML->event as $event) {
            $singleEvent = array(
                'id'=> (integer) $event->event_id,
                'title'=> (string) $event->title,
                'live'=> $this->convertToBool($event->live),
                'address1'=> (string) $event->e_address1,
                'address2'=>(string) $event->e_address2,
                'city'=> (string) $event->e_city,
                'state'=> (string) $event->e_state,
                'zip'=> (string) $event->e_zip,
                'shortDescription'=> (string) $event->description,
                'fullDescription'=> (string) $event->e_description,
                'phone' => (string) $event->e_phone,
                'web' => (string) $event->e_web,
                'contactName' => (string) $event->c_name,
                'contactPhone' => (string) $event->c_phone,
                'contactAddress1' => (string) $event->c_address1,
                'contactAddress2' => (string) $event->c_address2,
                'contactCity' => (string) $event->c_city,
                'contactState' => (string) $event->c_state,
                'contactCountry' => (string) $event->c_country,
                'contactZip' => (string) $event->c_zip,
                'contactEmail' => (string) $event->c_email
            );

            if ($getDates === true || $getDates === 'true') {

                $singleEvent['dates'] = $this->getDates($event->event_id, null, $getPrices);
            }

            $events[] = $singleEvent;
        }

        return $events;
    }
    /**
     * Get the dates.
     *
     * @param integer $eventID   The ID of the event
     * @param integer $dateID    The ID of the date.
     * @param boolean $getPrices Whether or not to get the Prices
     *
     * @return array  $sales An array of sales information.
     */
    public function getDates(
        $eventID,
        $dateID = null,
        $getPrices = false
    ) {

        $apiOptions = array(
            'endpoint' => 'datelist',
            'event_id' => $eventID
        );

        if (isset($dateID)) {
            $apiOptions['date_id'] = $dateID;
        }

        $datesXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($datesXML['error'])) {
            $this->setError('getDates', $datesXML['error']);
            return false;
        }

        $dates = array();

        foreach ($datesXML->date as $date) {

            $dateID = $date->date_id;

            $singleDate = array(
                'id'=> (integer) $dateID,
                'dateStart'=> (string) $date->datestart,
                'dateEnd'=> (string) $date->dateend,
                'timeStart'=> (string) $date->timestart,
                'timeEnd'=> (string) $date->timeend,
                'live'=> (boolean) $this->convertToBool($date->live),
                'available'=> (integer) $date->date_available
            );

            if ($getPrices === true || $getPrices === 'true') {

                $singleDate['prices'] = $this->getPrices($eventID, $dateID);
            }

            $dates[] = $singleDate;

        }

        if (count($dates) > 1) {

            $dates = $this->sortByKey($dates, 'dateStart');
            return $dates;

        } else {
            return $dates;
        }
    }

    /**
     * Get a the prices for a date.
     *
     * @param integer $eventID The ID of the Event
     * @param integer $dateID  The ID of the date
     *
     * @return array  $dates An array of dates.
     */
    public function getPrices($eventID, $dateID)
    {

        $apiOptions = array(
            'endpoint' => 'pricelist',
            'event_id' => $eventID,
            'date_id' => $dateID,
            'show_order' => true,
            'includepassword' => 1,
        );

        $priceXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($priceXML['error'])) {
            $this->setError('getPrices', $priceXML['error']);
            return false;
        }

        $prices = array();

        foreach ($priceXML->price as $price) {

            $single_price = array(
                'id'=> (integer) $price->price_id,
                'name'=> (string) $price->name,
                'value'=> (float) $price->value,
                'serviceFee'=> (float) $price->service_fee,
                'venueFee'=> (float) $price->venue_fee,
                'live'=> (boolean) $this->convertToBool($price->live),
                'order' => (integer) ($price->order ? $price->order : 1),
                'password' => (string) $price->password,
            );

            $prices[] = $single_price;
        }

        if (count($prices) > 1) {
            $prices = $this->sortByKey($prices, 'name');

            return $prices;

        } else {

            return $prices;
        }
    }

    /**
     * This will return an array with entries for each image attached to an event.
     * Each entry has the following fields: large, medium, small with urls to
     * the corresponding image size.
     *
     * @param  integer    $eventID The ID of the event you want the images for.
     * @return array|null An array of image sizes and their URL or null if no images
     * were found.
     */
    public function getImages($eventID)
    {
        $ch = curl_init();

        $url = 'https://www.brownpapertickets.com/eventimages.rss?e_number=' . $eventID;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION, 4);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $apiResponse = curl_exec($ch);

        curl_close($ch);

        $xml = $this->parseXML($apiResponse);

        if (isset($xml->channel->item->title) && $xml->channel->item->title == 'Error') {

            $this->setError('getImages', (string) $xml->channel->item->description);
            return false;
        }

        $images = array();

        foreach ($xml->channel->item as $item) {

            $bpt = $item->children('http://www.brownpapertickets.com/bpt.html');

            $image = array(
                'large' => ($bpt->image_large ? (string) $bpt->image_large : false),
                'medium' => ($bpt->image_medium ? (string) $bpt->image_medium : false),
                'small' => ($bpt->image_small ? (string) $bpt->image_small : false),
            );

            $images[] = $image;

        }

        if (!$images) {
            $this->setError('getImages', 'No images found.');
            return null;
        }

        return $images;
    }
}
