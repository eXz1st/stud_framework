<?php
namespace Mindk\Framework\DB;
/**
 * Interface DBOConnectorInterface
 * @package Mindk\Framework\DB
 */
interface DBOConnectorInterface
{
    /**
     * Prepare sql query
     *
     * @param $sql
     * @return mixed
     */
    public function prepareQuery($sql);
    /**
     * Execute data
     *
     * @parm $data
     * @return mixed
     */
    public function executeData($data);
    /**
     * Set sql query
     *
     * @param $sql
     * @return mixed
     */
    public function setQuery($sql);
    /**
     * Exec sql query
     *
     * @param $sql
     * @return mixed
     */
    public function execQuery($sql);
    /**
     * Get single row result
     *
     * @param $target
     * @return mixed
     */
    public function getResult(&$target);
    /**
     * Get list of resulting rows
     *
     * @param string $targetClass
     * @return mixed
     */
    public function getList($targetClass = '\stdClass');
}