<?php
namespace Mindk\Framework\Models;
use Mindk\Framework\DB\DBOConnectorInterface;
use Mindk\Framework\DB\GenericConnector;

/**
 * Basic Model Class
 * @package Mindk\Framework\Models
 */
abstract class Model
{
    /**
     * @var string  DB Table name
     */
    protected $tableName = '';
    /**
     * @var string  DB Table primary key
     */
    protected $primaryKey = 'id';
    /**
     * @var null
     */
    protected $dbo = null;
    /**
     * Model constructor.
     * @param DBOConnectorInterface $db
     */
    public function __construct(DBOConnectorInterface $db)
    {
        $this->dbo = $db;
    }
    /**
     * Create new record
     */
    public function create( $data ) {
        $keys = array_keys($data);
        $fields = '`' . implode('`, `', $keys) . '`';
        $placeholder = substr(str_repeat('?, ', count($keys)), 0, -2);
        $sql = "INSERT INTO `" . $this->tableName ."`(". $fields .") VALUES (" . $placeholder . " )";

        return $this->dbo->prepareQuery($sql)->executeData($data)->getResult($this);
    }
    /**
     * Read record
     *
     * @param   int Record ID
     *
     * @return  object
     */
    public function load( $id ) {
        $sql = 'SELECT * FROM `' . $this->tableName .
            '` WHERE `'.$this->primaryKey.'`='.(int)$id; //!
        return $this->dbo->setQuery($sql)->getResult($this);
    }
    /**
     * Save record state to db
     *
     * @return bool
     */
    public function save() {

        $sql = "UPDATE `" . $this->tableName . "` SET";

        $class_vars = (array)get_class_vars(get_class($this));
        $object_vars = (array) get_object_vars($this);
        $data = null;


        for(reset($class_vars); ($class_var_key = key($class_vars)); next($class_vars)) {
            for (reset($object_vars); ($object_var_key = key($object_vars)); next($object_vars)) {
                if (!($object_vars[$object_var_key] instanceof GenericConnector)) {
                    if($class_var_key == $object_var_key) {
                        unset($object_vars[$object_var_key]);
                    }
                }
            }
        }

        foreach ($object_vars as $object_var_key => $value) {
            if(!($value instanceof GenericConnector) && $object_var_key != 'id') {
                $data[$object_var_key] = $value;
                $sql .= " " . $object_var_key . " = ?,";
            }
        }

        $sql = substr($sql, 0, -1);

        $sql .= " WHERE `" . $this->primaryKey . "` = " . $this->id;

        return $this->dbo->prepareQuery($sql)->executeData($data)->getResult($this);
    }
    /**
     * Delete record from DB
     */
    public function delete( $id ) {
        $sql = 'DELETE FROM `' . $this->tableName .
            '` WHERE `' . $this->primaryKey . '` = ' . (int) $id;
        return $this->dbo->execQuery($sql)->getResult($this);
    }
    /**
     * Get list of records
     *
     * @return array
     */
    public function getList() {
        $sql = 'SELECT * FROM `' . $this->tableName . '`';
        return $this->dbo->setQuery($sql)->getList(get_class($this));
    }
}