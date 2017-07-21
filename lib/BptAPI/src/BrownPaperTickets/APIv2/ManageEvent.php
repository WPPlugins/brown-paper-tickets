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
 * This class contains all the methods necessary to create and manage events.
 */
class ManageEvent extends BptAPI
{
    ///////////////////////////////
    // Create/Manage Event Calls //
    ///////////////////////////////

    /**
     * The message shown when there is a missing parameter missing. It is shown
     * when the exception is thrown.
     * @var string
     */
    private $missingParamsMessage = 'Required parameters are missing.';

    /**
     * This method allows you to create a new event.
     *
     * __Authorization Required__
     *
     * @param array $eventParams The event's parameters.
     *
     * |parameter|type|description|required|
     * |---------|----|-----------|--------|
     * | `username` | string | The username the event will be created under. | **YES** |
     * | `name`  | string | The name of the event. | **YES** |
     * | `city`  | string | The city the event takes place in. | **YES** |
     * | `state  | string | The state the event takes place in. Must be state abbreviation. | **YES** |
     * | `shortDescription` | string | The short description. Must be fewer than 250 characters. Will cut off if over the limit. | **YES** |
     * | `fullDescription`  | string | The full description. | **YES** |
     * | `address1` | string | The first address line. | NO |
     * | `address2` | string | The second address line. | NO |
     * | `zip` | string | The Zip/Postal code. | NO |
     * | `phone` | integer | The phone numbers. | NO |
     * | `web` | integer | The event's website. | NO |
     * | `endOfEventMessage | string | A message to be displayed when the event completes. | NO |
     * | `endOfSaleMessage | string | A message to be displayed when the sale is completed. | NO |
     * | `dateNotes | string | Notes about the dates. | NO |
     * | `notes` | string | General notes displayed under the event description. | NO |
     * | `contactName` | string | Contact's name. | NO |
     * | `contactEmail` | string | Contact's email. | NO |
     * | `contactPhone` | string | Contact's phone. | NO |
     * | `contactFax` | integer | Contact's fax | NO |
     * | `contactAddress1` | string | Contact's first address line. | NO |
     * | `contactAddress2` | string | Contact's second address line. | NO |
     * | `contactCity` | string | Contact's city | NO |
     * | `contactState` | string | Contact's state | NO |
     * | `contactZip` | string | Contact's Zip/Postal code | NO |
     * | `contactCountry | string | Contact's country | NO |
     * | `public` | boolean | Whether or not to make this event public. Default is true. | NO |
     *
     * @return integer|false The new created event's ID or false if failure.
     */
    public function createEvent($eventParams)
    {

        if (!isset($eventParams['username'])
            || !isset($eventParams['name'])
            || !isset($eventParams['city'])
            || !isset($eventParams['state'])
            || !isset($eventParams['shortDescription'])
            || !isset($eventParams['fullDescription'])
        ) {
            throw new \InvalidArgumentException($this->missingParamsMessage);
        }

        if (strlen($eventParams['shortDescription']) > 250) {
            $eventParams['shortDescription'] = substr($eventParams['shortDescription'], 0, 250);
        }

        $apiOptions['endpoint'] = 'createevent';
        $apiOptions['account'] = $eventParams['username'];
        $apiOptions['e_name'] = $eventParams['name'];
        $apiOptions['e_city'] = $eventParams['city'];
        $apiOptions['e_state'] = $eventParams['state'];
        $apiOptions['e_short_description'] = $eventParams['shortDescription'];
        $apiOptions['e_description'] = $eventParams['fullDescription'];

        if (isset($eventParams['address1'])) {
            $apiOptions['e_address1'] = $eventParams['address1'];
        }
        if (isset($eventParams['address2'])) {
            $apiOptions['e_address2'] = $eventParams['address2'];
        }

        if (isset($eventParams['zip'])) {
            $apiOptions['e_zip'] = $eventParams['zip'];
        }
        if (isset($eventParams['phone'])) {
            $apiOptions['e_phone'] = $eventParams['phone'];
        }
        if (isset($eventParams['web'])) {
            $apiOptions['e_web'] = $eventParams['web'];
        }
        if (isset($eventParams['endOfEventMessage'])) {
            $apiOptions['end_of_event_message'] = $eventParams['endOfEventMessage'];
        }

        if (isset($eventParams['endOfSaleMessage'])) {
            $apiOptions['end_of_sale_message'] = $eventParams['endOfSaleMessage'];
        }
        if (isset($eventParams['dateNotes'])) {
            $apiOptions['date_notes'] = $eventParams['dateNotes'];
        }
        if (isset($eventParams['notes'])) {
            $apiOptions['e_notes'] = $eventParams['notes'];
        }
        if (isset($eventParams['contactName'])) {
            $apiOptions['c_name'] = $eventParams['contactName'];
        }
        if (isset($eventParams['contactEmail'])) {
            $apiOptions['c_email'] = $eventParams['contactEmail'];
        }
        if (isset($eventParams['contactPhone'])) {
            $apiOptions['c_phone'] = $eventParams['contactPhone'];
        }
        if (isset($eventParams['contactFax'])) {
            $apiOptions['c_fax'] = $eventParams['contactFax'];
        }
        if (isset($eventParams['contactAddress1'])) {
            $apiOptions['c_address1'] = $eventParams['contactAddress1'];
        }
        if (isset($eventParams['contactAddress2'])) {
            $apiOptions['c_address2'] = $eventParams['contactAddress2'];
        }
        if (isset($eventParams['contactCity'])) {
            $apiOptions['c_city'] = $eventParams['contactCity'];
        }
        if (isset($eventParams['contactState'])) {
            $apiOptions['c_state'] = $eventParams['contactState'];
        }
        if (isset($eventParams['contactZip'])) {
            $apiOptions['c_zip'] = $eventParams['contactZip'];
        }
        if (isset($eventParams['contactCountry'])) {
            $apiOptions['c_country'] = $eventParams['contactCountry'];
        }
        if (isset($eventParams['public'])) {
            $apiOptions['public'] = $eventParams['public'];
        }

        $createEventXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($createEventXML['error'])) {
            $this->setError('createEvent', $createEventXML['error']);
            return false;
        }

        return (integer) $createEventXML->event_id;
    }

    /**
     * Change an event's details. Same eventParams as `createEvent`.
     *
     * __Authorization Required__
     *
     * @param array  $eventParams The event's parameters.
     *
     * |parameter|type|description|required|
     * |---------|----|-----------|--------|
     * | `username` | string | The event owner's username. | **YES** |
     * | `eventID` | string | The event's ID. | **YES** |
     * | `name`  | string | The name of the event. | NO |
     * | `city`  | string | The city the event takes place in. | NO |
     * | `state  | string | The state the event takes place in. Must be state abbreviation. | NO |
     * | `shortDescription` | string | The short description. Must be fewer than 250 characters. Will cut off if over the limit. | NO |
     * | `fullDescription`  | string | The full description. | NO |
     * | `address1` | string | The first address line. | NO |
     * | `address2` | string | The second address line. | NO |
     * | `zip` | string | The Zip/Postal code. | NO |
     * | `phone` | integer | The phone numbers. | NO |
     * | `web` | integer | The event's website. | NO |
     * | `endOfEventMessage | string | A message to be displayed when the event completes. | NO |
     * | `endOfSaleMessage | string | A message to be displayed when the sale is completed. | NO |
     * | `dateNotes | string | Notes about the dates. | NO |
     * | `notes` | string | General notes displayed under the event description. | NO |
     * | `contactName` | string | Contact's name. | NO |
     * | `contactEmail` | string | Contact's email. | NO |
     * | `contactPhone` | string | Contact's phone. | NO |
     * | `contactFax` | integer | Contact's fax | NO |
     * | `contactAddress1` | string | Contact's first address line. | NO |
     * | `contactAddress2` | string | Contact's second address line. | NO |
     * | `contactCity` | string | Contact's city | NO |
     * | `contactState` | string | Contact's state | NO |
     * | `contactZip` | string | Contact's Zip/Postal code | NO |
     * | `contactCountry | string | Contact's country | NO |
     * | `public` | boolean | Whether or not to make this event public. Default is true. | NO |
     *
     * @return boolean True if event was successfully changed. False if not.
     */

    public function changeEvent($eventParams)
    {
        if (!isset($eventParams['username']) || !isset($eventParams['eventID'])) {
            throw new \InvalidArgumentException($this->missingParamsMessage);
        }

        $apiOptions['endpoint'] = 'changeevent';
        $apiOptions['account'] = $eventParams['username'];
        $apiOptions['event_id'] = $eventParams['eventID'];

        if (isset($eventParams['name'])) {
            $apiOptions['e_name'] = $eventParams['name'];
        }

        if (isset($eventParams['city'])) {
            $apiOptions['e_city'] = $eventParams['city'];
        }

        if (isset($eventParams['state'])) {
            $apiOptions['e_state'] = $eventParams['state'];
        }

        if (isset($eventParams['shortDescription'])) {
            $apiOptions['e_short_description'] = $eventParams['shortDescription'];
        }

        if (isset($eventParams['fullDescription'])) {
            $apiOptions['e_description'] = $eventParams['fullDescription'];
        }

        if (isset($eventParams['address1'])) {
            $apiOptions['e_address1'] = $eventParams['address1'];
        }

        if (isset($eventParams['address2'])) {
            $apiOptions['e_address2'] = $eventParams['address2'];
        }

        if (isset($eventParams['zip'])) {
            $apiOptions['e_zip'] = $eventParams['zip'];
        }

        if (isset($eventParams['phone'])) {
            $apiOptions['e_phone'] = $eventParams['phone'];
        }

        if (isset($eventParams['web'])) {
            $apiOptions['e_web'] = $eventParams['web'];
        }

        if (isset($eventParams['endOfEventMessage'])) {
            $apiOptions['end_of_event_message'] = $eventParams['endOfEventMessage'];
        }

        if (isset($eventParams['endOfSaleMessage'])) {
            $apiOptions['end_of_sale_message'] = $eventParams['endOfSaleMessage'];
        }

        if (isset($eventParams['dateNotes'])) {
            $apiOptions['date_notes'] = $eventParams['dateNotes'];
        }

        if (isset($eventParams['notes'])) {
            $apiOptions['e_notes'] = $eventParams['notes'];
        }

        if (isset($eventParams['contactName'])) {
            $apiOptions['c_name'] = $eventParams['contactName'];
        }

        if (isset($eventParams['contactEmail'])) {
            $apiOptions['c_email'] = $eventParams['contactEmail'];
        }

        if (isset($eventParams['contactPhone'])) {
            $apiOptions['c_phone'] = $eventParams['contactPhone'];
        }

        if (isset($eventParams['contactFax'])) {
            $apiOptions['c_fax'] = $eventParams['contactFax'];
        }

        if (isset($eventParams['contactAddress1'])) {
            $apiOptions['c_address1'] = $eventParams['contactAddress1'];
        }

        if (isset($eventParams['contactAddress2'])) {
            $apiOptions['c_address2'] = $eventParams['contactAddress2'];
        }

        if (isset($eventParams['contactCity'])) {
            $apiOptions['c_city'] = $eventParams['contactCity'];
        }

        if (isset($eventParams['contactState'])) {
            $apiOptions['c_state'] = $eventParams['contactState'];
        }

        if (isset($eventParams['contactCountry'])) {
            $apiOptions['c_country'] = $eventParams['contactCountry'];
        }

        if (isset($eventParams['activated'])) {
            $apiOptions['activated'] = $eventParams['activated'];
        }

        $changeEventXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($changeEventXML['error'])) {
            $this->setError('changeEvent', $changeEventXML['error']);
            return false;
        }

        return true;
    }

    /**
     * Add a date to an event.
     *
     * __Authorization Required__
     *
     * @param array  $dateParams An array of parameters.
     *
     * It must include the following:
     *
     * |parameter      |type     |description |required |
     * |---------------|---------|------------|---------|
     * | `username`    | string  | The username of the event's owner | **YES** |
     * | `eventID`     | integer | The ID you wish to add this date to.|**YES**|
     * | `beginTime`   | string  | The time the event begins on this date.|**YES**|
     * | `endTime`     | string  | The time the event ends on this date. Must be in MON-DD-YYYY 24:00 format |**YES**|
     * | `salesEnd`    | string  | The time you wish to end sales. Must be in MON-DD-YYYY 24:00 format |**YES**|
     * | `maxSales`    | integer | The maximum number of sales for this date. Use 0 for unlimited. |YES|
     * | `physical`    | boolean | Whether or not to sell physical tickets for this date. |NO|
     * | `pointOfSale` | boolean | Whether or not to make tickets available through the Brown Paper Tickets Open Ticket Network. Default is true. |NO|
     * | `willCall`    | boolean | Whether to offer tickets for Will-Call pickup. Default is true. |NO|
     * | `printAtHome` | boolean | Whether to offer print at home tickets. Default is true. |NO|
     * | `mobile`      | boolean | Whether to offer mobile tickets. Default is true. |NO|
     *
     * @return integer|boolean The newly created date's ID or false if not succesful.
     */
    public function addDate($dateParams)
    {

        if (!isset($dateParams['username'])
            || !isset($dateParams['eventID'])
            || !isset($dateParams['beginTime'])
            || !isset($dateParams['endTime'])
            || !isset($dateParams['salesEnd'])
            || !isset($dateParams['maxSales'])
        ) {
            throw new \InvalidArgumentException($this->missingParamsMessage);
        }

        if (!checkDateFormat($dateParams['beginTime'])
            || !checkDateFormat($dateParams['endTime'])
        ) {
            $this->setError('addDate', 'Invalid date format.');
            return false;
        }

        $apiOptions = array(
            'endpoint' => 'changedate',
            'account' => $dateParams['username'],
            'event_id' => $dateParams['eventID'],
            'begin_time' => $dateParams['beginTime'],
            'end_time' => $dateParams['endTime'],
            'sales_end' => $dateParams['salesEnd'],
            'max_sales' => $dateParams['maxSales'],
            'physical' => (isset($dateParams['physical']) ? $this->convertBoolToString($dateParams['physical']) : 't'),
            'pos' => (isset($dateParams['pos']) ? $this->convertBoolToString($dateParams['pos']) : 't'),
            'willcall' => (isset($dateParams['willcall']) ? $this->convertBoolToString($dateParams['willcall']) : 't'),
            'pah' => (isset($dateParams['pah']) ? $this->convertBoolToString($dateParams['pah']) : 't'),
            'mobile' => (isset($dateParams['mobile']) ? $this->convertBoolToString($dateParams['mobile']) : 't')
        );

        $addDateXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($addDateXML['error'])) {
            $this->setError('addDate', $addDateXML['error']);
            return false;
        }

        return (integer) $addDateXML->date_id;
    }

    /**
     * Change a date.
     *
     * __Authorization Required__
     *
     * @param array  $dateParams An array of parameters.
     *
     * It must include the following:
     *
     * |parameter      |type     |description |required |
     * |---------------|---------|------------|---------|
     * | `username`    | string  | The username of the event's owner | **YES** |
     * | `eventID`     | integer | The ID of the event the date belongs to.| **YES** |
     * | `dateID`      | integer | the ID of the date you wish to change. | **YES |
     * | `beginTime`   | string  | The time the event begins on this date.| NO |
     * | `endTime`     | string  | The time the event ends on this date.| NO | NO
     * | `salesEnd`    | string  | The time you wish to end sales.| NO |
     * | `maxSales`    | integer | The maximum number of sales for this date.|NO|
     * | `physical`    | boolean | Whether or not to sell physical tickets for this date.|NO|
     * | `pointOfSale` | boolean | Whether or not to make tickets available through the Brown Paper Tickets Open Ticket Network.|NO|
     * | `willCall`    | boolean | Whether to offer tickets for Will-Call pickup.|NO|
     * | `printAtHome` | boolean | Whether to offer print at home tickets.|NO|
     * | `mobile`      | boolean | Whether to offer mobile tickets.|NO|
     *
     * @return array True if successful, false if not.
     */
    public function changeDate($dateParams)
    {
        if (!isset($dateParams['username'])
            || !isset($dateParams['eventID'])
            || !isset($dateParams['dateID'])
        ) {
            throw new \InvalidArgumentException($this->missingParamsMessage);
        }

        if (!checkDateFormat($dateParams['beginTime'])
            || !checkDateFormat($dateParams['endTime'])
        ) {
            $this->setError('addDate', 'Invalid date format.');
            return false;
        }

        $apiOptions = array(
            'endpoint' => 'changeDate',
            'account' => $dateParams['username'],
            'event_id' => $dateParams['eventID'],
            'begin_time' => $dateParams['beginTime'],
        );

        if (isset($dateParams['endTime'])) {
            if (!checkDateFormat($dateParams['endTime'])) {
                $this->setError('addDate', 'Invalid endTime format.');
                return false;
            }
            $apiOptions['end_time'] = $dateParams['endTime'];
        }

        if (isset($dateParams['salesEnd'])) {

            if (!checkDateFormat($dateParams['salesEnd'])) {
                $this->setError('addDate', 'Invalid salesEnd format.');
                return false;
            }

            $apiOptions['sales_end'] = $dateParams['salesEnd'];

        }

        if (isset($dateParams['maxSales'])) {
            $apiOptions['max_sales'] = $dateParams['maxSales'];
        }

        if (isset($dateParams['physical'])) {
            $apiOptions['physical'] = $this->convertBoolToString($dateParams['physical']);
        }

        if (isset($dateParams['pos'])) {
            $apiOptions['pos'] = $this->convertBoolToString($dateParams['pos']);
        }

        if (isset($dateParams['willCall'])) {
            $apiOptions['willcall'] = $this->convertBoolToString($dateParams['willcall']);
        }

        if (isset($dateParams['printAtHome'])) {
            $apiOptions['pah'] = $this->convertBoolToString($dateParams['printAtHome']);

        }

        if (isset($dateParams['mobile'])) {
            $apiOptions['mobile'] = $this->convertBoolToString($dateParams['mobile']);
        }

        $changeDateXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($changeDateXML['error'])) {
            $this->setError('changeDate', $changeDateXML['error']);
            return false;
        }

        return true;
    }

    /**
     * Add a price to the event
     *
     * __Authorization Required__
     *
     * @param array $priceParams An array of price parameters
     *
     * | Parameter   | Type    | Description | Required|
     * |-------------|---------|-------------|---------|
     * | `eventID`   | integer | The ID of the event that this price will be attached to.| **YES** |
     * | `dateID`    | integer | The ID of the date that this price will be attached to.| **YES** |
     * | `value`     | integer | The Value of the price.| **YES** |
     * | `name`      | string  | The name of the price. | **YES** |
     * | `startTime` | string  | The time the price goes on sale. Must be in the MMM-DD-YYYY 24:00 | NO |
     * | `endTime`   | string  | The time the prices ceases sale. Must be in the MMM-DD-YYYY 24:00 | NO |
     * | `maxSales`  | integer | The maximum number of tickets for sale at this price level. | NO |
     *
     * @return  array An array containing the ID of the price created.
     */
    public function addPrice($priceParams)
    {

        if (!isset($priceParams['username'])
            || !isset($priceParams['eventID'])
            || !isset($priceParams['dateID'])
            || !isset($priceParams['value'])
            || !isset($priceParams['name'])
        ) {
            throw new \InvalidArgumentException($this->missingParamsMessage);
        }

        $apiOptions = array(
            'endpoint' => 'addprice',
            'account' => $priceParams['username'],
            'event_id' => $priceParams['eventID'],
            'date_id' => $priceParams['dateID'],
            'price' => $priceParams['value'],
            'price_name' => $priceParams['name'],
        );

        if (isset($priceParams['startTime'])) {
            $apiOptions['start_time'] = $priceParams['startTime'];
        }

        if (isset($priceParams['endTime'])) {

            $apiOptions['end_time'] = $priceParams['endTime'];
        }

        if (isset($priceParams['maxSales'])) {

            $apiOptions['max_sales'] = $priceParams['maxSales'];
        }

        $addPriceXML = $this->parseXML($this->$callAPI($apiOptions));

        if (isset($addPriceXML['error'])) {
            $this->setError('addPrice', $addPriceXML['error']);
            return false;
        }

        $addPrice = (integer) $addPriceXML->price_id;
    }

    /**
     * Change a price on the event
     *
     * @param array $priceParams An array of price parameters
     *
     * | Parameter   | Type    | Description | Required|
     * |-------------|---------|-------------|---------|
     * | `username`  | string  | The username the event belongs to. | **YES** |
     * | `priceID    | integer | The ID of the price that will be changed. | **YES** |
     * | `eventID`   | integer | The ID of the event that this price is attached to.| **YES** |
     * | `value`     | integer | The Value of the price.| NO |
     * | `name`      | string  | The name of the price. | NO |
     * | `startTime` | string  | The time the price goes on sale. Must be in the MMM-DD-YYYY 24:00 | NO |
     * | `endTime`   | string  | The time the prices ceases sale. Must be in the MMM-DD-YYYY 24:00 | NO |
     * | `maxSales`  | integer | The maximum number of tickets for sale at this price level. | NO |
     *
     * @return  array Returns an array containing a success or fail.
     */
    public function changePrice($priceParams)
    {

        if (!isset($priceParams['priceID'])
            || !isset($priceParams['username'])
            || !isset($priceParams['eventID'])
            || !isset($priceParams['priceID'])
        ) {
            throw new \InvalidArgumentException($this->missingParamsMessage);
        }

        $apiOptions = array(
            'endpoint' => 'changeprice',
            'account'  => $priceParams['username'],
            'event_id' => $priceParams['eventID'],
            'price_id' => $priceParams['priceID'],
        );

        if (isset($priceParams['value'])) {
            $apiOptions['price'] = $priceParams['value'];
        }

        if (isset($priceParams['name'])) {
            $apiOptions['price_name'] = $priceParams['name'];
        }

        if (isset($priceParams['startTime'])) {
            $apiOptions['start_time'] = $priceParams['startTime'];
        }

        if (isset($priceParams['endTime'])) {

            $apiOptions['end_time'] = $priceParams['endTime'];
        }

        if (isset($priceParams['maxSales'])) {

            $apiOptions['max_sales'] = $priceParams['maxSales'];
        }

        $addPriceXML = $this->parseXML($this->$callAPI($apiOptions));

        if (isset($changePriceXML['error'])) {
            $this->setError('changePrice', $changePriceXML['error']);
            return false;
        }

        return true;
    }
}
