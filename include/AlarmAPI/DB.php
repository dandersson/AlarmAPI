<?php
namespace AlarmAPI;

/**
 * `mysqli`-based database class.
 */
class DB extends \mysqli
{
    const DB_CONFIG = 'config/db.ini.php';
    const DBTABLES_CONFIG = 'config/dbtables.ini.php';

    protected static $instance;

    /**
     * Open a MySQL database connection using the `mysqli` interface with
     * parameters as per the external configuration file.
     */
    public function __construct()
    {
        $dbconfig = parse_ini_file(self::DB_CONFIG);
        $dbtconfig = parse_ini_file(self::DBTABLES_CONFIG);

        @parent::__construct(
            $dbconfig['host'],
            $dbconfig['username'],
            $dbconfig['password'],
            $dbconfig['database']
        );

        if (mysqli_connect_errno()) {
            throw new exception(mysqli_connect_error(), mysqli_connect_errno());
        }

        $this->set_charset($dbconfig['charset']);

        $this->db_table_name = $dbtconfig['db_table_name'];
        $this->alarm_state = $dbtconfig['alarm_log'];
    }

    /**
     * Singleton pattern for returning a common database connection.
     */
    public static function getInstance() {
        if (!self::$instance) { self::$instance = new self(); }
        return self::$instance;
    }

    /**
     * Set MySQL time format according to locale preference.
     */
    public function setTimeFormat($language)
    {
        $query = 'SET @@lc_time_names=' . $language;
        $stmt = $this->prepare($query);
        $stmt->execute();
    }
}
