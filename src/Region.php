<?php
namespace Zwei\Region;


use Zwei\Base\DB;

class Region extends Base
{
    /**
     * 构造方法初始化
     *
     * CommentStatistics constructor.
     * @param string $tableName 地区表名
     */
    public function __construct($tableName = 'region')
    {
        $this->setTableName($tableName);
    }

    /**
     * 添加地区
     *
     * @param string $regionName 地区名
     * @param integer $regionLevel 层级
     * @param integer $regionPid 地区父id
     * @param string $regionPath 路径
     * @param string $regionCode 地区代码
     * @param int $regionHot 热门地区
     * @return bool|string 成功返回地区id, 否则失败
     */
    public function add($regionName, $regionLevel, $regionPid, $regionPath, $regionCode = '', $regionHot = 0)
    {
        $data = [
            'region_pid' => $regionPid, // 地区父id
            'region_path' => $regionPath, // 路径
            'region_level' => $regionLevel, // 层级
            'region_name' => $regionName, // 名称
            'region_code' => $regionCode,// 地区代码
            'region_hot' => $regionHot,// 热门地区
            'region_changed' => time()
        ];
        $dbConnection = DB::getInstance()->getConnection();
        $result = $dbConnection->insert($this->getTableName(), $data);
        return $result ? $dbConnection->lastInsertId() : false;
    }

    /**
     * 更新地区
     * @param integer $regionId 地区id
     * @param array $data 更新字段
     * @return bool|string 成功返回地区id, 否则失败
     */
    public function update($regionId, array $data)
    {
        $whereArr = [
            'region_id' => $regionId, // 地区id
        ];
        $data['region_changed'] = time();
        $dbConnection = DB::getInstance()->getConnection();
        return $dbConnection->update($this->getTableName(), $data, $whereArr);
    }

    /**
     * 获取指定地区记录
     * @param integer $regionId 地区id
     * @return array
     */
    public function get($regionId)
    {
        $whereArr = [$regionId, ];
        $sql = "select * from {$this->getTableName()} where region_id = ?";
        $dbConnection = DB::getInstance()->getConnection();
        return $dbConnection->fetchAssoc($sql, $whereArr);
    }

    /**
     * 获取指定地区的下级(子)地区
     * @param integer $regionId 地区id
     * @param integer $regionLevel 层级
     * @return array
     */
    public function getSubLists($regionId, $regionLevel)
    {
        $whereArr = [$regionId, $regionLevel];
        $sql = "select * from {$this->getTableName()} where region_pid = ? and region_level = ?";
        $dbConnection = DB::getInstance()->getConnection();
        return $dbConnection->fetchAll($sql, $whereArr);
    }


}