<?php
namespace AlarmAPI;

/**
 * Localized interface to the alarm status.
 */
class Alarm
{
    const STRINGS_CONFIG = 'config/strings.ini.php';

    const ON = 1;
    const OFF = 0;
    const UNKNOWN = -1;

    /**
     * Fetch database connection and register localization.
     */
    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->strings = self::i18n();
        $this->db->setTimeFormat($this->strings['time_lang']);
    }

    /**
     * Get current alarm status and return it as a PHP array corresponding the
     * the JSON structure described in the API class. See API class
     * documentation for a general description of return format.
     *
     * By default returns the data fields
     *
     * - `state`: an int ∈ {1, 0, −1} representing {on, off, unknown} state
     *            respectively.
     * - `time`:  a localized time string indicating when the last status was
     *            set.
     * 
     * Additional available localized strings can be requested by giving them
     * as string elements in an input array. See the string configuration for
     * available strings.
     *
     * There are two "magic" fields that can be requested, that return a string
     * dependent on the current alarm state:
     *
     * - `short_title`: returns a short localized title, such as "Disarmed".
     * - `long_title`:  returns a long localized title, such as "The alarm is
     *                  disarmed since".
     *
     * If an unknown string field is requested, the JSON response will indicate
     * an error.
     */
    public function getCurrentStatus($fields = [])
    {
        $query = '
            SELECT
                `status`,
                DATE_FORMAT(`date`, "%W %Y-%m-%d %H:%i")
            FROM `larmlog`
            ORDER BY `date` DESC
            LIMIT 1
        ';
        $stmt = $this->db->prepare($query);
        $stmt->bind_result($state, $time);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();

        $data = [
            'state' => $state,
            'time' => $time,
        ];

        if ($fields !== []) {
            // Magic field `long_title`.
            if (in_array('long_title', $fields)) {
                $data['long_title'] = self::getLongTitle($state, $this->strings);
                $clear_keys[] = 'long_title';
            }
            // Magic field `short_title`.
            if (in_array('short_title', $fields)) {
                $data['short_title'] = self::getShortTitle($state, $this->strings);
                $clear_keys[] = 'short_title';
            }
            // Remove magic keys if present.
            if (isset($clear_keys)) $fields = array_diff($fields, $clear_keys);

            foreach ($fields as $field) {
                if (isset($this->strings[$field])) {
                    $data[$field] = $this->strings[$field];
                } else {
                    return [
                        'status' => 400,
                        'message' => "Unknown string field '$field'.",
                    ];
                }
            }
        }

        return [
            'status' => 200,
            'data' => $data,
        ];
    }

    /**
     * Return localized `short_title` field corresponding to the given state.
     */
    private static function getShortTitle($state, $strings)
    {
        switch ($state) {
        case self::ON:
            return $strings['short_title_on'];
        case self::OFF:
            return $strings['short_title_off'];
        case self::UNKNOWN:
            return $strings['short_title_unknown'];
        }
    }

    /**
     * Return localized `long_title` field corresponding to the given state.
     */
    private static function getLongTitle($state, $strings)
    {
        switch ($state) {
        case self::ON:
            return $strings['long_title_on'];
        case self::OFF:
            return $strings['long_title_off'];
        case self::UNKNOWN:
            return $strings['long_title_unknown'];
        }
    }

    /**
     * Internationalization function which returns a string array corresponding
     * to either the optional given language code, or the user preferred
     * language as per the `ACCEPT_LANGUAGE` request from the browser.
     *
     * If the language lacks an entry in the translation file, English is used
     * as a fallback language.
     */
    private static function i18n($language = null) {
        if ($language === null && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }

        $strings = parse_ini_file(self::STRINGS_CONFIG, true);
        return isset($strings[$language]) ? $strings[$language] : $strings['en'];
    }
}
