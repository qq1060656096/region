<?php
namespace Zwei\Region\Custom\CityArea;

use Zwei\Base\DB;
use Zwei\Region\Region;
use Zwei\Region\RegionTranslation;

/**
 * Class CityArea
 * @package Zwei\Region\Custom\CityArea
 */
class CityArea
{
    /**
     * 地区类
     * @var Region
     */
    protected $region = null;

    /**
     * 地区翻译类
     * @var RegionTranslation
     */
    protected $regionTranslation = null;

    /**
     * 构造方法初始化
     *
     * CityArea constructor.
     * @param string $regionTableName 地区信息表名
     * @param string $regionTranslationTableName 地区翻译信息表名
     */
    public function __construct($regionTableName = 'city_area', $regionTranslationTableName = 'city_area_translation')
    {
        $this->region = new Region($regionTableName);
        $this->regionTranslation = new RegionTranslation($regionTranslationTableName);
    }

    /**
     * 获取地区类
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * 获取地区翻译类
     *
     * @return RegionTranslation
     */
    public function getRegionTranslation()
    {
        return $this->regionTranslation;
    }

    /**
     * 添加国家
     * @param string $name 国家名
     * @param string $regionCode 地区代码(默认值空字符)
     * @param int $regionHot 热门(默认值0)
     * @return bool|string 成功返回地区id, 否则失败
     */
    public function addCountry($name, $regionCode = '', $regionHot = 0)
    {
        return $this->region->add($name, CityAreaLevel::LEVEL_COUNTRY, 0, '', $regionCode, $regionHot);
    }

    /**
     * 添加城市
     * @param integer $countryId 国家id
     * @param string $name 城市名
     * @param string $regionCode 地区代码(默认值空字符)
     * @param int $regionHot 热门(默认值0)
     * @return bool|string 成功返回地区id, 否则失败
     */
    public function addCity($countryId, $name, $regionCode = '', $regionHot = 0)
    {
        $regionPath = $countryId;
        $regionPid = $countryId;
        return $this->region->add($name, CityAreaLevel::LEVEL_CITY, $regionPid, $regionPath, $regionCode, $regionHot);
    }

    /**
     * 添加城市区域
     * @param integer $countryId 国家id
     * @param integer $cityId 城市id
     * @param string $name 城市区域名
     * @param string $regionCode 地区代码(默认值空字符)
     * @param int $regionHot 热门(默认值0)
     * @return bool|string 成功返回地区id, 否则失败
     */
    public function addCityArea($countryId, $cityId, $name, $regionCode = '', $regionHot = 0)
    {
        $regionPath = "{$countryId},$cityId";
        $regionPid = $cityId;
        return $this->region->add($name, CityAreaLevel::LEVEL_AREA, $regionPid, $regionPath, $regionCode, $regionHot);
    }

    /**
     * 获取国家列表
     *
     * @param string $langCode 语言
     * @param string $orderBy 排序(默认值='letter_sort asc',默认是字母排序)
     * @return array
     */
    public function getCountryLists($langCode, $orderBy = 'letter_sort asc')
    {
        return $this->getSubLists(0, $langCode, CityAreaLevel::LEVEL_COUNTRY, $orderBy);
    }

    /**
     * 获取城市列表
     *
     * @param integer $regionId 城市地区id
     * @param string $langCode 语言
     * @param string $orderBy 排序(默认值='letter_sort asc',默认是字母排序)
     * @return array
     */
    public function getCityLists($regionId, $langCode, $orderBy = 'letter_sort asc')
    {
        return $this->getSubLists($regionId, $langCode, CityAreaLevel::LEVEL_CITY, $orderBy);
    }

    /**
     * 获取城市区域列表
     *
     * @param integer $regionId 城市区域地区id
     * @param string $langCode 语言
     * @param string $orderBy 排序(默认值='letter_sort asc',默认是字母排序)
     * @return array
     */
    public function getCityAreaLists($regionId, $langCode, $orderBy = 'letter_sort asc')
    {
        return $this->getSubLists($regionId, $langCode, CityAreaLevel::LEVEL_AREA, $orderBy);
    }

    /**
     * 获取指定地区的下级(子)地区
     *
     * @param integer $regionId 地区id
     * @param integer $langCode 语言
     * @param integer $regionLevel 层级
     * @param string $orderBy 排序
     * @return array
     */
    public function getSubLists($regionId, $langCode, $regionLevel, $orderBy = '')
    {
        $whereArr = [
            $regionId,
            $regionLevel,
            $langCode,
        ];
        $orderBy ? $orderBy = "order by {$orderBy}": null;
        $regionTableName = $this->region->getTableName();
        $regionTranslationTableName = $this->regionTranslation->getTableName();
        $sql = <<<str
select * from {$regionTableName} c INNER JOIN {$regionTranslationTableName} ct on c.region_id=ct.region_id
where region_pid = ? and `region_level` = ? and lang_code = ? {$orderBy};
str;
        $dbConnection = DB::getInstance()->getConnection();
        return $dbConnection->fetchAll($sql, $whereArr);
    }
}