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
 * This class contains the methods used to get sales information about events.
 * The Developer ID must have the event's owner listed as an authorized account.
 */
class SalesInfo extends BptAPI
{

    /**
     * Get the Event Sales info for all events or a specific event,
     * or a specific event's specific date.
     *
     * __Authorization Required__
     * 
     * @param string  $username       The Username of the authorized account. Required.
     * @param string  $eventID        The Event ID. Optional.
     * @param string  $dateID         The Date ID. Optional.
     * @param boolean $getOnlyCurrent Whether or not to only get sales info for events that are currently active. Optional.
     *
     * @return array
     */
    public function getEventSales(
        $username,
        $eventID,
        $dateID = '',
        $getOnlyCurrent = false
    ) {
        $apiOptions = array(
            'endpoint' => 'eventsales',
            'account' => $username,
            'event_id' => $eventID,
            'date_id' => $dateID,
            'current' => $getOnlyCurrent
        );

        $eventSalesXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($eventSalesXML['error'])) {
            $this->setError('getEventSales', $eventSalesXML['error']);
            return false;
        }

        $eventSales = array();

        foreach ($eventSalesXML as $eventSale) {
            $singleEventSale = array(
                'title' => (string) $eventSale->title,
                'link' => (string) $eventSale->link,
                'id' => (integer) $eventSale->e_number,
                'eventStatus' => (string) $eventSale->event_status,
                'ticketsSold' => (integer) $eventSale->tickets_sold,
                'collectedValue' => (integer) $eventSale->collected_value,
                'paidValue' => (integer) $eventSale->paid_value
            );

            $eventSales[] = $singleEventSale;
        }

        return $eventSales;
    }


    ////////////////////////////
    // Sales/Order Data Calls //
    ////////////////////////////

    /**
     * Get the sales data of a specific date or all dates
     *
     * __Authorization Required__
     * 
     * @param string $username The username of the event owner. Required.
     * @param string $eventID  The Event ID. Required.
     * @param string $dateID   The Price ID. Required.
     *
     * @return [type]
     */
    public function getDateSales(
        $username,
        $eventID,
        $dateID = ''
    ) {
        $apiOptions = array(
            'endpoint' => 'datesales',
            'account' => $username,
            'event_id' => $eventID,
            'date_id' => $dateID
        );

        $dateSalesXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($dateSalesXML['error'])) {
            $this->setError('getDateSales', $dateSalesXML['error']);
            return false;
        }

        $dateSales = array();

        foreach ($dateSalesXML as $dateSale) {

            $singleDate = array(
                'id' => (integer) $dateSale->date_id,
                'beginTime' => (string) $dateSale->begin_time,
                'endTime' => (string) $dateSale->end_time,
                'ticketsSold' => (integer) $dateSale->date_tickets_sold,
                'collectedValue' => (integer) $dateSale->date_collected_value,
                'prices' => array()
            );

            foreach ($dateSale->price as $price) {
                $singlePrice = array(
                    'id' => (integer) $price->price_id,
                    'name' => (string) $price->price_name,
                    'ticketsSold' => (integer) $price->price_tickets_sold,
                    'collectedValue' => (integer) $price->price_collected_value
                );

                $singleDate['prices'][] = $singlePrice;
            }

            $dateSales[] = $singleDate;
        }

        return $dateSales;
    }

    /**
     * Get order info for events or a specific event, date or price
     * 
     * __Authorization Required__
     * 
     * @param string  $username Your account. It must be in the Authorized Accounts list.
     * @param integer $eventID  The ID of the Event. Optional.
     * @param string  $dateID   The ID of the Date. Optional.
     * @param string  $priceID  The ID of the Price. Optional.
     *
     * @return array|boolean  $sales   An array of sales information or false if unsuccessful.
     */
    public function getOrders(
        $username,
        $eventID = '',
        $dateID = '',
        $priceID = ''
    ) {
        $apiOptions = array(
            'endpoint' => 'orderlist',
            'account' => $username,
            'event_id' => $eventID,
            'date_id' => $dateID,
            'price_id' => $priceID
        );

        $ordersXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($ordersXML['error'])) {
            $this->setError('getOrders', $ordersXML['error']);
            return false;
        }

        $orders = array();

        foreach ($ordersXML->item as $sale) {

            $singleOrder = array(
                'time' => (string) $sale->order_time,
                'dateID' => (integer) $sale->date_id,
                'priceID' => (integer) $sale->price_id,
                'quantity' => (integer) $sale->quantity,
                'firstName' => (string) $sale->fname,
                'lastName' => (string) $sale->lname,
                'address' => (string) $sale->address,
                'city' => (string) $sale->city,
                'state' => (string) $sale->state,
                'zip' => (string) $sale->zip,
                'country' => (string) $sale->country,
                'email' => (string) $sale->email,
                'phone' => (string) $sale->phone,
                'creditCard' => (integer) $sale->cc,
                'shippingMethod' => (string) $sale->shipping_method,
                'notes' => (string) $sale->order_notes,
                'ticketNumber' => (string) $sale->ticket_number,
                'section' => (string) $sale->section,
                'row' => (string) $sale->row,
                'seat' => (string) $sale->seat
            );

            // put the singleSale into the sales array
            $orders[] = $singleOrder;
        }

        return $orders;

    }
}
