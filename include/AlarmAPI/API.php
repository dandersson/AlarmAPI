<?php
namespace AlarmAPI;

/**
 * API utility functions.
 */
class API
{
    const HTTP_STATUS_CODES_CONFIG = 'config/http_status_codes.ini.php';

    // Store semi-static lookup table (set on first access) of HTTP status
    // codes defined in an external configuration file.
    public static $http_status_codes = null;
    public static function http_status_codes()
    {
        if (self::$http_status_codes === null) {
            self::$http_status_codes = parse_ini_file(self::HTTP_STATUS_CODES_CONFIG);
        }
        return self::$http_status_codes;
    }

    /**
     * Utility function to get corresponding textual representation of an HTTP status code.
     */
    function getStatusCodeMessage($status)
    {
        return isset(self::$http_status_codes[$status]) ? self::$http_status_codes[$status] : '';
    }

    /**
     * Send response as a serialized JSON object.
     *
     * The input must be an array containing at least the index 'status'
     * representing the wanted HTTP response code as an integer. Sample usage:
     *
     *   sendJSONResponse(['status' => 200, 'data' => ['state' => 1]]);
     *
     * A successful API request returns a JSON object of the format:
     *
     *   {
     *     'status' : int(200),
     *     'success': bool(true),
     *     'data': {
     *       ...
     *     }
     *   }
     *
     * with definitions:
     *
     * - 'status':  the HTTP status code for the request (will be duplicated in
     *              the actual response header). 200 indicates success,
     *              everything else indicates an error.
     * - `success`: a boolean indicating whether the request was successful as
     *              per the above definition.
     * - `data`:    payload data (most likely given as an associative array).
     *
     * An unsuccessful API request returns a JSON object of the format:
     *
     *   {
     *     'status' : int(HTTP status code),
     *     'success': bool(false),
     *     'message': string,
     *   }
     *
     * with definitions:
     *
     * - 'status':  see above definition.
     * - 'success': see above definition.
     * - 'message': a message describing the status of the API request.
     */
    function sendJSONResponse($response)
    {
        $status = $response['status'];
        $response['success'] = ($response['status'] == 200);

        $status_header = 'HTTP/1.1 ' . $status . ' ' . self::getStatusCodeMessage($status);
        header($status_header);
        header('Content-type: application/json');
        echo JSON_encode($response);
    }
}
