<?php
namespace Zwei\Region\Tests;

use Zwei\Base\DB;
use Zwei\Region\RegionTranslation;

/**
 * 地区翻译测试
 * Class RegionTranslationTest
 * @package Zwei\Region\Tests
 */
class RegionTranslationTest extends BaseTestCase
{
    /**
     * 公共基竟
     */
    public static function setUpBeforeClass()
    {
        $dbConnection = DB::getInstance()->getConnection();
        $dbConnection->delete('region_translation',[
            'region_id' => '2018040201',
            'lang_code' => 'zh_cn',
        ]);
        $dbConnection->delete('region_translation',[
            'region_id' => '2018040202',
            'lang_code' => 'zh_cn',
        ]);
    }

    /**
     * 测试地区新增翻译内容
     */
    public function testAdd()
    {
        $obj = new RegionTranslation();
        $regionId =  2018040201;
        $langCode = 'zh_cn';
        $langName = '中国';
        $spell = 'zhongguo';
        $letterSort = 'Z';
        $langSort = 1;
        $insertId = $obj->add($regionId, $langCode, $langName, $spell, $letterSort, $langSort);
        $this->assertTrue($insertId > 0 ? true : false);
    }

    /**
     * 测试地区更新翻译
     * @depends testAdd
     */
    public function testUpdate()
    {
        $obj = new RegionTranslation();
        $regionId =  2018040201;
        $langCode = 'zh_cn';
        $langName = '中国.update';
        $spell = 'zhongguo.update';
        $letterSort = 'Z.update';
        $langSort = 2;
        $updateCount = $obj->update($regionId, $langCode, $langName, $spell, $letterSort, $langSort);
        $this->assertEquals("1", $updateCount);
    }

    /**
     * 测试翻译添加
     */
    public function testTadd()
    {
        $obj = new RegionTranslation();
        $regionId =  2018040202;
        $langCode = 'zh_cn';
        $langName = '中国.add';
        $spell = 'zhongguo.add';
        $letterSort = 'Z.add';
        $langSort = 22;
        $bool = $obj->t($regionId, $langCode, $langName, $spell, $letterSort, $langSort);

        $regionId =  2018040202;
        $langCode = 'en_us';
        $langName = '中国.en_us.add';
        $spell = 'zhongguo.en_us.add';
        $letterSort = 'Z.en_us.add';
        $langSort = 22;
        $bool = $obj->t($regionId, $langCode, $langName, $spell, $letterSort, $langSort);
        $this->assertTrue($bool);
    }

    /**
     * 测试翻译修改
     * @depends testTadd
     */
    public function testTupdate()
    {
        $obj = new RegionTranslation();
        $regionId =  2018040202;
        $langCode = 'zh_cn';
        $langName = '中国.update2';
        $spell = 'zhongguo.update2';
        $letterSort = 'Z.update2';
        $langSort = 222;
        $bool = $obj->t($regionId, $langCode, $langName, $spell, $letterSort, $langSort);
        $this->assertTrue($bool);
    }

    /**
     *
     */
    public function testGet()
    {
        $obj = new RegionTranslation();
        $regionId =  2018040202;
        $langCode = 'zh_cn';
        // 获取指定地区指定语言翻译内容
        $row = $obj->get($regionId, $langCode);
        $this->assertEquals('2018040202', $row['region_id']);
        // 获取地区所有语言翻译内容
        $regionInfoAllLang = $obj->getAllLang($regionId);
        $this->assertCount(2, $regionInfoAllLang);
    }
}