<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2022-01-29 00:33:55
 * @modify date 2022-01-29 00:33:55
 * @license GPLv3
 * @desc [description]
 */

namespace Plugins\SaviorSO\Core;

trait DatabaseUtils
{
    public function runQueryOrFail(string $Query)
    {
        try {
            $this->db->query($Query);
            return true;
        } catch (\PDOException $e) {
            $this->severity = 'danger';
            $this->error = $e->getMessage();
            return false;
        }
    }
}