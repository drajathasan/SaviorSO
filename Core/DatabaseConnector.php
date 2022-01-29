<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2022-01-29 00:27:41
 * @modify date 2022-01-29 00:27:41
 * @license GPLv3
 * @desc [description]
 */

namespace Plugins\SaviorSO\Core;

trait DatabaseConnector
{
    public function createConnection()
    {
        $this->db = null;
        $this->db = new \PDO($this->dsn['driver'], $this->dsn['username'], $this->dsn['password']);
    }
}
