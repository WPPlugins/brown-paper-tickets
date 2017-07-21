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

use SimpleXMLElement;
use DateTime;
use Exception;

/**
 * Brown Paper Tickets API Wrapper
 * This base class contains the utility methods used by the sub classes.
 */
class BptAPI
{

    /**
     * Your Brown Paper Tickets Developer ID
     * @var string
     */
    protected $devID;

    /**
     * The base URL used to make API calls.
     * @var string
     */
    private $baseURL = 'https://www.brownpapertickets.com/api2/';

    /**
     * An array of errors.
     * @var array
     */
    private $errors = array();

    /**
     * Whether or not to log the errors.
     * @var boolean
     */
    private $logErrors = false;

    /**
     * Set the Dev ID
     *
     * @param string $devID Your Brown Paper Tickets Developer ID
     */

    /**
     * Initializes the class, sets the Developer ID and any optional parameters
     * in the `$params` variable.
     * @param string $devID  Your Brown Paper Tickets developer ID.
     * @param array  $params An array of options that can set the following properties.
     *
     * | parameter | type | description                       |
     * |-----------|------|-----------------------------------|
     * | logErrors | bool | Whether or not to log the errors. |
     */
    public function __construct($devID, $params = null)
    {
        $this->devID = $devID;

        if ($params) {

            if (isset($params['logErrors'])) {
                $this->logErrors = $params['logErrors'];
            }
        }
    }

    //////////////////////////////////////
    // Utility Functions for this class //
    //////////////////////////////////////

    /**
    * Make the call to the API using cURL.
    *
    * @param array $apiOptions an Array of parameters you wish to send to the API.
    * The first value must be the API endpoint
    *
    * @return object The xml returned by the API
    */
    protected function callAPI($apiOptions)
    {
        if (!$apiOptions['endpoint']) {
            $this->setError('callAPI', 'An endpoint must be set in the argument array.');
            return false;
        }

        $endPoint = $apiOptions['endpoint'];

        unset($apiOptions['endpoint']);
        //$params = array_shift($apiOptions);
        $url = $this->baseURL.$endPoint.'?id='.$this->devID;

        foreach ($apiOptions as $key => $value) {
            $url = $url.'&'.$key.'='.urlencode($value);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION, 4);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $apiResponse = curl_exec($ch);

        curl_close($ch);

        return $apiResponse;
    }


    /**
     * Simple array sort function
     *
     * @param array $array The array you want to sort
     * @param key   $key   The key you want to sort by
     *
     * @return array  the sorted array
     */
    protected function sortByKey($array, $key)
    {

        //Loop through and get the values of our specified key
        foreach ($array as $k => $v) {
            $b[] = strtolower($v[$key]);
        }

        asort($b);

        foreach ($b as $k => $v) {
            $c[] = $array[$k];
        }

        return $c;

    }

    /**
     * Sets an option.
     * @param string  $option The options name.
     * @param mixed   $value  The value of the option.
     *
     * @return boolean True if succesful, false if not.
     */
    public function setOption($option, $value)
    {
        $validOptions = array(
            'logErrors',
            'devID'
        );

        if (!in_array($option, $validOptions)) {
            $this->setError('setOption', 'Invalid option: '.$option);
            return false;
        }

        $this->$option = $value;

        if ($this->$option === $value) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the option.
     *
     * @param   $option string The name of the option.
     * @return  mixed   The value of the option.
     */
    public function getOption($option)
    {
        if (isset($this->$option)) {
            return $this->$option;
        } else {
            throw new \Exception($option.' does not exist.');
        }
    }

    /**
     * Put an error into the private errors array.
     * @param string $method      Should be the name of the method that the error
     * is thrown in.
     * @param string $description A description of the error.
     *
     * @return boolean|null If logErrors is set to true, it will return true or
     * false if successful. If it isn't set, it will return null.
     */
    public function setError($method, $description)
    {
        if ($this->logErrors) {
            $this->errors[] = array($method => $description);

            if (end($this->errors) === array($method => $description)) {
                return true;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }

    /**
     * Get the errors.
     * @param  string $newest Pass in `'newest'` to return the most recent error.
     * Otherwise returns all.
     *
     * @return array An array of errors.
     */
    public function getErrors($newest = null)
    {
        if ($this->logErrors) {
            if ($newest === 'newest') {
                return end($this->errors);
            }

            return array_reverse($this->errors);
        }
    }

    /**
     * parse the XML file
     *
     * @param string $rawXML the XML string to parse
     *
     * @return object $xmlTree A parsed XML object
     */
    protected function parseXML($rawXML)
    {

        libxml_use_internal_errors(true);

        try {

            $xmlTree = new SimpleXMLElement($rawXML);

        } catch (\Exception $exception) {
            // Something went wrong.

            $data = array(
                'result' => 'fail',
                'code' => '0',
                'error' => $exception->getMessage()
            );

            return $this->handleError($data);

        }

        if ($xmlTree->result == 'fail') {

            return $this->handleError($xmlTree);

        }

        return $xmlTree;


    }

    /**
    * Handle the xml if an error was thrown.
    *
    * @param mixed[] $data The xml containing the error
    *
    * @return array Do something
    */
    protected function handleError($data)
    {

        if (isset($data['result'])) {
            return $data;
        }

        // Need to do error checking
        $error = array(
            'result' => 'fail',
            'code' => (string) $data->resultcode,
            'error' => (string) $data->note
        );

        return $error;
    }

    /**
     * Check to see if the date format is correct.
     *
     * @param string $date The date string. Must be in the MON-DD-YYYY 24:00 format.
     *
     * @return boolean     Returns false if date is bad.
     */
    protected function checkDateFormat($date)
    {
        $format = 'M-d-Y h:i';

        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * Converts the API response (that comes as either 'y' or 'n') to a boolean type.
     *
     * @param  string $value the String to check
     * @return boolean       a true or false boolean
     */
    protected function convertToBool($value)
    {
        if ($value == 'n') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Converts a boolean to a string.
     *
     * @param  string $value The string to check.
     * @return boolean       The boolean value of the string
     */
    protected function convertBoolToString($value)
    {
        if ($value) {
            return 't';
        } else {
            return 'f';
        }
    }
}
