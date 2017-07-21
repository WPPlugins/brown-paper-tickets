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
 * This class contains all the methods necessary to purchase tickets through the API.
 */
class ManageCart extends BptAPI
{
    private $cartCreatedAt;
    private $cartTtl = 900;
    private $cartID;

    private $affiliateID;

    private $prices = array();
    private $pricesSent = false;
    private $pricesRemoved = array();
    private $pricesNotAdded = array();

    private $shippingInfo = array();
    private $shippingInfoSent = false;

    private $billingInfo = array();
    private $receipt = array();
    private $billingInfoSent = false;

    private $allowedShipping = array(1, 2, 3, 4);
    private $allowedCardTypes = array('Visa', 'Mastercard', 'Amex', 'Discover');
    private $value = 0;

    private $requireFullBilling = false;
    private $requireWillCallNames = false;

    /**
     * Initialize a cart ID from the BPT Api or pass in an existing cart and created at time.
     *
     * @param  string $cartID The existing cart ID.
     * @param  integer $cartCreatedAt The time the cart was created at.
     * @return mixed Error array or a string containing the cart ID.
     */
    public function initCart($cartID = null, $createdAt = null)
    {

        if ($cartID) {

            $this->cartID = $cartID;
            $this->cartCreatedAt = $createdAt;

            if ($this->isExpired()) {
                return array('success' => false, 'message' => 'Cart has expired.');
            }
        }

        if (!$cartID) {
            $apiOptions = array(
                'endpoint' => 'cart',
                'stage' => 1
            );

            $cartXML = $this->parseXML($this->callAPI($apiOptions));

            if (isset($cartXML['error'])) {
                $this->setError('initCart', $cartXML['error']);
                return false;
            }

            $this->cartID = (string) $cartXML->cart_id;
            $this->cartCreatedAt = time();
        }


        return array('success' => true, 'cartID' => $this->cartID, 'cartCreatedAt' => $this->cartCreatedAt);
    }

    public function existingCart($cartID = null, $cartCreatedAt = null)
    {
        $this->cartId = $cartID;
        $this->cartCreatedAt = $cartCreatedAt;
    }

    /**
     * Tests whether or not the cart ID has expired. If it has, it sets the
     * cartID to null and returns true. If not, it simply returns false.
     * @return boolean
     */
    public function isExpired()
    {
        if ($this->cartCreatedAt + $this->cartTtl <  time()) {
            $this->cartID = null;
            return true;
        }

        return false;
    }

    public function getRequireFullBilling()
    {
        return $this->requireFullBilling;
    }

    public function getRequireWillCallNames()
    {
        return $this->requireWillCallNames;
    }

    /**
     * Gets the cart ID or null if expired.
     *
     * @return string|array The Cart ID or an Array containing
     * the error from the BPT API.
     */
    public function getCartID()
    {
        $this->isExpired();
        return $this->cartID;
    }

    public function getCartCreatedAt()
    {
        return $this->cartCreatedAt;
    }

    public function setAffiliateID($affiliateID)
    {
        $this->affiliateID = $affiliateID;
    }

    public function getAffiliateID()
    {
        return $this->affiliateID;
    }

    /**
     * Sets this class's price array.
     *
     * @param array $prices An array with all the price info.
     * | parameter | type | description |
     * |-----------|--------|-------------|
     * | `prices`  | array  | An array of prices with pricing info. The array key should be the price ID. |
     *
     * ### $prices array
     * | parameter | type | description |
     * |-----------|------|-------------|
     * | `shippingMethod` | integer | An integer representing shipping method*
     * | `quantity` | integer | the number of tickets you wish to add. |
     * | `affiliateID` | integer | Optional. If you wish to earn a commision, add the affiliate ID. |
     *
     *
     * __Shipping Method Info__
     *
     * 1 - Physical Tickets
     *
     * 2 - Will Call
     *
     * 3 - Print at Home
     *
     * ### Example:
     * ```
     * $prices = array(
     *     '12345' => array(
     *         'shippingMethod' => 1,
     *         'quantity' => 2,
     *     ),
     *     '12346' => array(
     *         'shippingMethod' => 3,
     *         'quantity' => 3
     *     )
     * );
     */

    public function setPrices($prices)
    {
        foreach ($prices as $priceId => $value) {
            if (!isset($value['shippingMethod']) || !in_array($value['shippingMethod'], $this->allowedShipping)) {
                unset($prices[$priceId]);
                continue;
            }

            if ($value['shippingMethod'] === 2) {
                $this->requireWillCallNames = true;
            }
        }

        $this->prices = $prices;

        return $this->prices;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Remove prices. Update the cart after removing prices using sendPrices();
     *
     * @param  array $params An array containing the cart ID and an array of Prices IDs.
     * @return array         The results array.
     *
     * | parameter | type   | description |
     * |-----------|--------|-------------|
     * | prices    | array  | An array of price IDs to be removed |
     */
    public function removePrices($prices)
    {

        $modifiedPrices = $this->getPrices();

        foreach ($prices as $price) {
            if (isset($modifiedPrices[$price])) {

                $modifiedPrices[$price]['quantity'] = 0;

            }
        }

        $this->prices = $modifiedPrices;

        return $this->prices;
    }

    public function getPricesNotAdded()
    {
        return $this->pricesNotAdded;
    }

    public function getPricesRemoved()
    {
        return $this->pricesRemoved;
    }

    /**
     * Send prices to the cart.
     * @todo Really figure out a decent way of accomplishing this.
     *
     * @return  array Returns either a success or error message array.
     */

    public function sendPrices()
    {
        if ($this->getReceipt()) {
            return array('success' => false, 'message' => 'Billing info has already been sent.');
        }

        $cartID = $this->getCartID();

        if (!$cartID) {
            return array(
                'success' => false,
                'message' => 'Invalid cart.'
            );
        }

        $apiOptions = array(
            'endpoint' => 'cart',
            'stage' => 2,
            'cart_id' => $this->cartID,
        );

        if ($this->affiliateID) {
            $apiOptions['ref'] = $this->affiliateID;
        }

        $newPrices = array();

        foreach ($this->getPrices() as $priceID => $values) {

            $apiOptions['price_id'] = $priceID;
            $apiOptions['shipping'] = $values['shippingMethod'];
            $apiOptions['quantity'] = $values['quantity'];

            $addPricesXML = $this->parseXML($this->callAPI($apiOptions));


            if (isset($addPricesXML['error'])) {
                $this->pricesNotAdded[$priceID] = $addPricesXML['error'];
                continue;
            }

            $this->setValue($addPricesXML->val);

            $resultcode = (string) $addPricesXML->resultcode;

            if ($apiOptions['quantity'] === 0 && $resultcode === '000000') {
                $this->pricesRemoved[] = $priceID;
                continue;
            }


            $newPrices[$priceID] = array(
                'quantity' => $apiOptions['quantity'],
                'shippingMethod' => $apiOptions['shipping']
            );

        }

        if (!$newPrices && !$this->getPricesRemoved() && $this->getPricesNotAdded()) {
            return array('success' => false, 'message' => 'Prices were not able to be added.');
        }

        $this->setPrices($newPrices);
        $this->pricesSent = true;

        return array('success' => true, 'message' => 'Prices sent.');
    }

    public function setShipping($shippingInfo)
    {
        if (!isset($shippingInfo['firstName']) ||
            !isset($shippingInfo['lastName']) ||
            !isset($shippingInfo['address']) ||
            !isset($shippingInfo['city']) ||
            !isset($shippingInfo['state']) ||
            !isset($shippingInfo['zip']) ||
            !isset($shippingInfo['country'])
        ) {
            $this->setError('setShipping', 'Missing required Fields.');
            return false;
        }

        if ($this->requireWillCallNames === true &&
            (!isset($shippingInfo['willCallFirstName']) ||
            !isset($shippingInfo['willCallLastName']))
        ) {
            $shippingInfo['willCallFirstName'] = $shippingInfo['firstName'];
            $shippingInfo['willCallLastName'] = $shippingInfo['lastName'];
        }

        $this->shippingInfo = $shippingInfo;

        return array('success' => true, 'message' => 'Shipping info set.');
    }

    public function getShipping()
    {
        return $this->shippingInfo;
    }

    /**
     * Send shipping info to the cart.
     * @param array $params The shipping info.
     */
    public function sendShipping()
    {

        if ($this->getReceipt()) {
            return array('success' => false, 'message' => 'Billing info has already been sent.');
        }

        $cartID = $this->getCartID();

        if (!$cartID) {
            return array(
                'success' => false,
                'message' => 'Invalid cart.'
            );
        }

        if (!$this->getPrices()) {
            return array(
                'success' => false,
                'message' => 'No tickets.'
            );
        }

        if (!$this->pricesSent) {
            return array(
                'success' => false,
                'message' => 'Prices have not been sent.'
            );
        }

        $apiOptions = array(
            'endpoint' => 'cart',
            'stage' => 3,
            'cart_id' => $cartID,
            'fname' => $this->shippingInfo['firstName'],
            'lname' => $this->shippingInfo['lastName'],
            'address' => $this->shippingInfo['address'],
            'city' => $this->shippingInfo['city'],
            'state' => $this->shippingInfo['state'],
            'zip' => $this->shippingInfo['zip'],
            'country' => $this->shippingInfo['country'],
        );

        if (isset($shippingInfo['willCallFirstName'])) {
            $apiOptions['attendee_firstname'] = $this->shippingInfo['willCallFirstName'];
        }

        if (isset($shippingInfo['willCallLastName'])) {
            $apiOptions['attendee_lastname'] = $this->shippingInfo['willCallLastName'];
        }

        $apiResponse = $this->callAPI($apiOptions);

        $shippingInfoXML = $this->parseXML($apiResponse);

        if (isset($shippingInfoXML['error'])) {
            return array('success' => false, 'message' => $shippingInfoXML['error']);
        }

        $this->shippingInfoSent = true;

        return true;
    }

    public function getBilling()
    {
        return $this->billingInfo;
    }

    public function setBilling($billingInfo)
    {

        if (!isset($billingInfo['firstName']) || !isset($billingInfo['lastName'])) {
            return array('success' => false, 'message' => 'First and last name are required.');
        }

        if ($this->getRequireFullBilling()) {
            if (!isset($billingInfo['email']) || !isset($billingInfo['phone'])) {
                return array('success' => false, 'message' => 'Email and telephone are required.');
            }

            if (!isset($billingInfo['type']) ||
                !isset($billingInfo['number']) ||
                !isset($billingInfo['expMonth']) ||
                !isset($billingInfo['expYear']) ||
                !isset($billingInfo['cvv2'])
            ) {
                return array('success' => false, 'message' => 'Credit card info is required.');
            }


            if (!in_array($billingInfo['type'], $this->allowedCardTypes)) {
                return array('success' => false, 'message' => 'Type must be Visa, Mastercard, Discover or Amex.');
            }
        }

        $this->billingInfo = $billingInfo;

        return array('success' => true, 'message' => 'Billing info set.');

    }

    /**
     * Add billing info to the cart.
     * @param array $params The billing info.
     *
     */
    public function sendBilling()
    {

        if ($this->getReceipt()) {
            return array('success' => false, 'message' => 'Billing info has already been sent.');
        }

        $cartID = $this->getCartID();

        if (!$cartID) {
            return array(
                'success' => false,
                'message' => 'Invalid cart.'
            );
        }

        if (!$this->getPrices()) {
            return array(
                'success' => false,
                'message' => 'No prices set.'
            );
        }

        if (!$this->pricesSent) {
            return array(
                'success' => false,
                'message' => 'Prices have not been sent.'
            );
        }

        if (!$this->getShipping()) {
            return array(
                'success' => false,
                'message' => 'No shipping info set.'
            );
        }

        if (!$this->shippingInfoSent) {
            return array(
                'success' => false,
                'message' => 'Shipping info has not been sent.'
            );
        }

        if (!$this->getBilling()) {
            return array(
                'success' => false,
                'message' => 'No billing info set.'
            );
        }

        $apiOptions = array(
            'endpoint' => 'cart',
            'stage' => 4,
            'cart_id' => $cartID,
            'billing_fname' => $this->billingInfo['firstName'],
            'billing_lname' => $this->billingInfo['lastName'],
        );

        if ($this->requireFullBilling) {
            $apiOptions['type'] = $this->billingInfo['type'];
            $apiOptions['number'] = $this->billingInfo['number'];
            $apiOptions['exp_month'] = $this->billingInfo['expMonth'];
            $apiOptions['exp_year'] = $this->billingInfo['expYear'];
            $apiOptions['cvv2'] = $this->billingInfo['cvv2'];
            $apiOptions['billing_address'] = $this->billingInfo['address'];
            $apiOptions['billing_city'] = $this->billingInfo['city'];
            $apiOptions['billing_state'] = $this->billingInfo['state'];
            $apiOptions['billing_zip'] = $this->billingInfo['zip'];
            $apiOptions['billing_country'] = $this->billingInfo['country'];
            $apiOptions['email'] = $this->billingInfo['email'];
            $apiOptions['phone'] = $this->billingInfo['phone'];
        }

        $billingInfoXML = $this->parseXML($this->callAPI($apiOptions));
        if (isset($billingInfoXML['error'])) {
             return array('success' => false, 'message' => $billingInfoXML['error']);
        }

        $results = array(
            'success' => true,
            'message' => 'Purchase complete.',
        );

        if ($billingInfoXML->pahurl) {
            $results['pahurl'] = (string) $billingInfoXML->pahurl;
        }

        $this->billingInfoSent = true;
        $this->setReceipt($results);
        return $results + $this->getReceipt();
    }

    public function getReceipt()
    {
        return $this->receipt;
    }

    private function setReceipt($receipt)
    {
        if (isset($receipt['success'])) {
            unset($receipt['success']);
        }

        if (isset($receipt['message'])) {
            unset($receipt['message']);
        }

        if ($receipt['pahurl']) {
            $receipt['ticketURL'] = $receipt['pahurl'];
        }

        $receipt['cartID'] = $this->getCartID();
        $receipt['total'] = $this->getValue();
        $receipt['receiptURL'] = 'https://www.brownpapertickets.com/confirmation/' . $this->getCartID();

        $this->receipt = $receipt;
    }

    private function setValue($value)
    {
        $value = (real) $value;
        if ($value === 0) {
            $this->requireFullBilling = false;
        }

        if ($value > 0) {
            $this->requireFullBilling = true;
        }

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
