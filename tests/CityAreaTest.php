<?php
namespace Zwei\Region\Tests;

use Zwei\Region\Base;
use Zwei\Region\Custom\CityArea\CityArea;

class CityAreaTest extends BaseTestCase
{
    /**
     * 测试获取国家列表
     */
    public function testCountryLists()
    {
        $cityArea = new CityArea();
        // 获取中文语言国家列表
        $lists = $cityArea->getCountryLists('zh-cn');
//        print_r($lists);
        $this->assertEquals(1, $lists[0]['region_level']);
    }

    /**
     * 测试获取中国中文语言城市列表
     */
    public function testChinaCityLists()
    {
        $cityArea = new CityArea();
        // 取中国中文语言城市列表
        $lists = $cityArea->getCityLists(1,'zh-cn');
//        print_r($lists);
        $this->assertEquals(2, $lists[0]['region_level']);
    }

    public function testCityAreaLists()
    {
        $cityArea = new CityArea();
        // 取中国 成都城市中文语言区域列表
        $lists = $cityArea->getCityAreaLists(554,'zh-cn');
//        print_r($lists);
        $this->assertEquals(3, $lists[0]['region_level']);
    }
}