<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2022-01-28 23:30:12
 * @modify date 2022-01-28 23:30:12
 * @license GPLv3
 * @desc [description]
 */

namespace Plugins\SaviorSO\Core;

use Ifsnop\Mysqldump\Mysqldump as IMysqldump;

class Database
{
    private static $Instance = null;
    private $dumperInstance = null;
    private $db;
    
    use DatabaseConnector,DatabaseUtils;

    /**
     * Dumper setting options
     *
     * @var array
     */
    private $dumpSettings = [
        'compress' => IMysqldump::NONE,
        'no-data' => false,
        'add-drop-table' => true,
        'single-transaction' => true,
        'lock-tables' => true,
        'add-locks' => false,
        'extended-insert' => false,
        'disable-keys' => true,
        'skip-triggers' => false,
        'add-drop-trigger' => true,
        'routines' => true,
        'databases' => false,
        'add-drop-database' => false,
        'hex-blob' => true,
        'no-create-info' => false,
        'where' => ''
    ]; 

    /**
     * PDO DSN for database connection
     *
     * @var array
     */
    private $dsn = [
        'driver' => "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
        'username' => DB_USERNAME,
        'password' => DB_PASSWORD
    ];

    /**
     * Error message for failed process
     *
     * @var string
     */
    public $error = '';

    /**
     * Error severity level
     *
     * @var string
     */
    public $severity = '';

    /**
     * Path with output file name
     *
     * @var string
     */
    private $outPutFile = '';

    /**
     * An assoc array for table row limitation
     *
     * @var array
     */
    private $tableLimit = [
        'biblio_log' => 1,
        'system_log' => 1
    ];

    private function connect()
    {
        $this->createConnection();
    }

    public function setDumper()
    {
        $this->dumperInstance = new IMysqldump($this->dsn['driver'], $this->dsn['username'], $this->dsn['password'], $this->dumpSettings);
        return $this;
    }

    public function setLimit(array $LimitList = [])
    {
        if (count($LimitList) > 0) $this->tableLimit = $LimitList;
        $this->dumperInstance->setTableLimits($this->tableLimit);
        return $this;
    }

    public function setOutput($outPutFileName)
    {
        $this->outPutFile = $outPutFileName;
        return $this;
    }

    public function makeBackup()
    {
        try {
            // set for unlimited time
            ini_set('max_execution_time', 0);
            $this->dumperInstance->start($this->outPutFile);

        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return $this;
    }

    public function createDummy()
    {
        if (!empty($this->error)) return false;

        $this->connect();

        if (!$this->runQueryOrFail('create database if not exists ' . DB_NAME . '_so;')) 
            return false;

        $this->dsn = [
            'driver' => "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.'_so',
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD
        ];

        $this->connect();

        // set for unlimited time
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 0);
        $getBackup = file_get_contents($this->outPutFile);

        try {
            $this->db->query('SET GLOBAL max_allowed_packet=' . (filesize($this->outPutFile) + filesize($this->outPutFile)) . ';' . $getBackup);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$Instance)) { self::$Instance = new Database; }

        return self::$Instance;
    }
}