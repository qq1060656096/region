<?php
namespace Zwei\Region;

use Zwei\Base\DB;

/**
 * 地区翻译
 *
 * Class RegionTranslation
 * @package Zwei\Region
 */
class RegionTranslation extends Base
{
    /**
     * 构造方法初始化
     *
     * CommentStatistics constructor.
     * @param string $tableName 评论表名
     */
    public function __construct($tableName = 'region_translation')
    {
        $this->setTableName($tableName);
    }

    /**
     * 更新翻译内容(存在就更新,不存在就插入)
     *
     * @param integer $regionId 地区id
     * @param string $langCode 语言code
     * @param string $langName 语言名
     * @param string $spell 拼写(默认空值,表示不更新字段值)
     * @param string $letterSort 字母排序(默认空值,表示不更新字段值)
     * @param int $langSort 排序(默认空值,表示不更新字段值)
     * @return int 成功返回受影响行数,否则失败
     */
    public function t($regionId, $langCode, $langName, $spell = null, $letterSort = null, $langSort = null)
    {
        $row = $this->get($regionId, $langCode);
        // 添加
        if(!$row) {
            $spell === null ? $spell = '' : null;
            $letterSort === null ? $letterSort = '' : null;
            $langSort === null ? $langSort = 0 : null;
            $result = $this->add($regionId, $langCode, $langName, $spell, $letterSort, $langSort);
        }// 更新
        else {
            $result = $this->update($regionId, $langCode, $langName, $spell, $letterSort, $langSort);
        }
        return $result ? true : false;
    }
    /**
     * 添加翻译
     *
     * @param integer $regionId 地区id
     * @param string $langCode 语言code
     * @param string $langName 语言名
     * @param string $spell 拼写(默认空字符串)
     * @param string $letterSort 字母排序(默认空字符串)
     * @param int $langSort 排序(默认0)
     * @return bool|string 成功返回翻译id, 否则失败
     */
    public function add($regionId, $langCode, $langName, $spell = '', $letterSort = '', $langSort = 0)
    {
        $data = [
            'region_id' => $regionId, // 地区id
            'lang_name' => $langName, // 翻译
            'lang_code' => $langCode, // 语言code
            'spell' => $spell, // 拼写
            'letter_sort' => $letterSort,// 字母排序
            'lang_sort' => $langSort,// 数字排序
            'lang_changed' => time()
        ];
        $dbConnection = DB::getInstance()->getConnection();
        $result = $dbConnection->insert($this->getTableName(), $data);
        return $result ? $dbConnection->lastInsertId() : false;
    }

    /**
     * 更新翻译内容
     *
     * @param integer $regionId 地区id
     * @param string $langCode 语言code
     * @param string $langName 语言名
     * @param string $spell 拼写(默认空值,表示不更新字段值)
     * @param string $letterSort 字母排序(默认空值,表示不更新字段值)
     * @param int $langSort 排序(默认空值,表示不更新字段值)
     * @return int 成功返回受影响行数,否则失败
     */
    public function update($regionId, $langCode, $langName, $spell = null, $letterSort = null, $langSort = null)
    {
        $whereArr = [
            'region_id' => $regionId, // 地区id
            'lang_code' => $langCode, // 语言code
        ];

        $data = [
            'lang_name' => $langName, // 翻译
            'lang_changed' => time()
        ];
        $data['lang_name'] = $langName;// 翻译地区名
        $spell === null ? null : $data['spell'] = $spell;// 拼写
        $letterSort === null ? null : $data['letter_sort'] = $letterSort;// 字母排序
        $langSort === null ? null : $data['lang_sort'] = $langSort;// 数字排序

        $dbConnection = DB::getInstance()->getConnection();
        return $dbConnection->update($this->getTableName(), $data, $whereArr);
    }

    /**
     * 获取实体指定语言翻译记录
     *
     * @param integer $regionId 地区id
     * @param string $langCode 语言code
     * @return array|bool
     */
    public function get($regionId, $langCode)
    {
        $whereArr = [$regionId, $langCode];
        $sql = "select * from {$this->getTableName()} where region_id = ? and lang_code = ? limit 1";
        $dbConnection = DB::getInstance()->getConnection();
        return $dbConnection->fetchAssoc($sql, $whereArr);
    }

    /**
     * 获取地区所有翻译语言记录
     * @param integer $regionId 地区id
     * @return array
     */
    public function getAllLang($regionId)
    {
        $whereArr = [$regionId, ];
        $sql = "select * from {$this->getTableName()} where region_id = ?";
        $dbConnection = DB::getInstance()->getConnection();
        return $dbConnection->fetchAll($sql, $whereArr);
    }
}