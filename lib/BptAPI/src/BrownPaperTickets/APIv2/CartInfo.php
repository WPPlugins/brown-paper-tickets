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
 * This class contains the methods used to get information about a cart.
 */
class CartInfo extends BptAPI
{
    /**
     * Returns an array containing the contents of the specified cart.
     *
     * @param  string $cartID The Cart ID
     * @return array          An array containing the carts of the cart.
     */
    public function getContents($cartID)
    {
        $apiOptions = array(
            'endpoint' => 'cartcontents',
            'cart_id' => $cartID
        );

        $cartContentsXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($cartContentsXML['error'])) {
            $this->setError('getCartContents', $cartContentsXML['error']);
            return false;
        }

        $cartContents = array();

        foreach ($cartContentsXML->ticket as $ticket) {
            $singleTicket = array(
                'eventID' => (string) $ticket->event_id,
                'eventName' => (string) $ticket->e_name,
                'dateID' => (string) $ticket->date_id,
                'beginTime' => (string) $ticket->begin_time,
                'endTime' => (string) $ticket->end_time,
                'priceID' => (string) $ticket->price_id,
                'priceName' => (string) $ticket->price_name,
                'priceValue' => (string) $ticket->price,
                'shippingMethod' => (string) $ticket->shipping,
                'seatingSection' => (string) $ticket->section,
                'seatingRow' => (string) $ticket->row,
                'seatingSeat' => (string) $ticket->seat,
                'willCallLastName' => (string) $ticket->lname,
                'willCallFirstName' => (string) $ticket->fname
            );

            $cartContents[] = $singleTicket;
        }

        return $cartContents;
    }


    /**
     * Get the value of the specified Cart
     *
     * @param  string $cartID The cart ID.
     * @return array|boolean  An associative array containing the value
     * of the cart.
     */
    public function getValue($cartID)
    {
        $apiOptions = array(
            'endpoint' => 'cartvalue',
            'cart_id' => $cartID
        );

        $cartValueXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($cartValueXML['error'])) {
            $this->setError('getCartValue', $cartValueXML['error']);
            return false;
        }

        $cartValue = array(
            'cartValue' => (string) $cartValueXML->val,
            'cartCurrency' => (string) $cartValueXML->currency,
            'shippingNeeded' => $this->convertToBool($cartValueXML->shipping)
        );

        return $cartValue;
    }
}
