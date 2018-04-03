<?php
namespace Zwei\Region;

use Zwei\Base\DB;

/**
 * 基类
 *
 * Class Base
 * @package Zwei\Region
 *
 */
class Base
{
    /**
     * 表名
     * @var string
     */
    protected $tableName = "";


    /**
     * 设置表名
     * @param string $tableName 表名
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }
    /**
     * 获取表名
     * @return string
     */
    public function getTableName()
    {
        $tableName = DB::getInstance()->getTable($this->tableName);
        return $tableName;
    }
}