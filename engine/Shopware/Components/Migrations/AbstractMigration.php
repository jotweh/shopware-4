<?php
/**
 * Shopware 4.0
 * Copyright © 2013 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Components\Migrations;

/**
 * @category  Shopware
 * @package   Shopware\Components\Migrations
 * @copyright Copyright (c) 2013, shopware AG (http://www.shopware.de)
 */
abstract class AbstractMigration
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * @var array
     */
    protected $sql = array();


    /**
     * @param \PDO $connection
     */
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param  \PDO $connection
     * @return AbstractMigration
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        $result = array();

        $regexPattern = '/[0-9]*-(.+)\.php$/i';

        $rc = new \ReflectionClass(get_class($this));
        $fileName = basename($rc->getFileName());

        preg_match($regexPattern, $fileName, $result);

        return $result[1];
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        $result = array();
        $regexPattern = '/[0-9]*$/i';

        preg_match($regexPattern, get_class($this), $result);

        return (int) $result[0];
    }

    /**
     * @return void
     */
    abstract public function up();

    /**
     * @param string $sql
     * @return AbstractMigration
     */
    public function addSql($sql)
    {
        // assure statement ends with semicolon
        $sql = rtrim($sql, ';');

        $this->sql[] = $sql;

        return $this;
    }

    /**
     * @return array
     */
    public function getSql()
    {
        return $this->sql;
    }
}
