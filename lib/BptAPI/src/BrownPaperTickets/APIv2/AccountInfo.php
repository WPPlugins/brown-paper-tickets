<?php
/**
 *   Copyright (c) 2014 Brown Paper Tickets
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
 *  @license  GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
 *  @link     Link
 **/

namespace BrownPaperTickets\APIv2;

/**
 * This class pulls in info about a particular account.
 * The account must be listed in the Developer ID's authorized accounts.
 */
class AccountInfo extends BptAPI
{
    ///////////////////////////////
    // Account Information Calls //
    ///////////////////////////////

    /**
     * Get the account information
     *
     * @param string $username The username of the account must be authorized. Required.
     *
     * @return array|boolean The account info array if successful, false if not and sets
     * an error in the error log.
     */
    public function getAccount($username)
    {
        $apiOptions = array(
            'endpoint' => 'viewaccount',
            'account' => $username
        );

        $accountXML = $this->parseXML($this->callAPI($apiOptions));

        if (isset($accountXML['error'])) {
            $this->setError('getAccount', $accountXML['error']);
            return false;
        }

        $account = array(
            'id' => (integer) $accountXML->client_id,
            'username' => (string) $accountXML->c_client_name,
            'firstName' => (string) $accountXML->c_fname,
            'lastName' => (string) $accountXML->c_lname,
            'address' => (string) $accountXML->c_address,
            'city' => (string) $accountXML->c_city,
            'state' => (string) $accountXML->c_state,
            'zip' => (string) $accountXML->c_zip,
            'phone' => (string) $accountXML->c_phone,
            'email' => (string) $accountXML->c_email,
            'nameForChecks' => (string) $accountXML->c_name_for_checks
        );

        return $account;
    }
}
