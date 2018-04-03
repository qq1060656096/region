<?php
namespace Zwei\Region\Tests;

use Overtrue\Pinyin\Pinyin;
use Zwei\Base\DB;
use Zwei\Region\Custom\CityArea\CityArea;

/**
 * 美团爬虫
 * Class MeiTuanSpiderTest
 * @package Zwei\Region\Tests
 */
class MeiTuanSpiderTest extends BaseTestCase
{
    /**
     * 测试获取美团地区字母列表
     */
    public function testGetLetterLists()
    {
        $url = "https://i.meituan.com/index/changecity";
        $content = file_get_contents($url);
//        var_dump($content);
        $pattern = "/<ul class=\"charlist\">(.*?)<\/ul>/ism";
        preg_match($pattern, $content, $matchArr);

        // 获取字母列表
        $listStr = $matchArr[0];
        $letterPattern = "/<a.*>(.*?)<\/a>/";
        $matchLetterArr = [];
        preg_match_all($letterPattern, $listStr, $matchLetterArr);
//        print_r($matchLetterArr[1]);
        file_put_contents(__DIR__.'/meituan-spider/letter-lists.json', json_encode($matchLetterArr[1], true));
    }

    /**
     * 获取城市和城市区域列表
     */
    public function testGetCityLists()
    {
        $content = file_get_contents(__DIR__.'/meituan-spider/letter-lists.json');
        $letterLists = json_decode($content, true);
//        print_r($letterLists);
        $opts = array (
            'http' => array (
                'method' => 'GET',
                'header'=>
                    "Cookie:uuid=34276057860c4387a236.1522631826.1.0.0; _lx_utm=utm_source%3DBaidu%26utm_medium%3Dorganic; _lxsdk_cuid=16283ecea0dc8-015ca8e48a0cfd-454c092b-1fa400-16283ecea0da0; __mta=211032362.1522631764843.1522631764843.1522631764843.1; iuuid=6749342ED5430089E3B60DF61D50C12B6057D49B797373D03216A0C2532BC250; _lxsdk=6749342ED5430089E3B60DF61D50C12B6057D49B797373D03216A0C2532BC250; webp=1; __utmz=74597006.1522631772.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _hc.v=e5aa03cf-c80f-aa2f-c0ce-783cc84ffe75.1522631785; ci=313; cityname=%E5%AE%9C%E5%AE%BE; JSESSIONID=qzcdoar3wu2coptbn8o0sh4c; IJSESSIONID=qzcdoar3wu2coptbn8o0sh4c; idau=1; __utma=74597006.1387275342.1522631772.1522661865.1522720037.3; __utmc=74597006; i_extend=H__a100005__b3; latlng=30.65618,104.08329,1522720039385; ci3=1; __mta=211032362.1522631764843.1522631764843.1522720042564.2; __utmb=74597006.8.9.1522720049853; _lxsdk_s=162892fd531-dd4-f8e-030%7C%7C6; \r\n"
            )
        );
        $context = stream_context_create($opts);

        $pinyin = new Pinyin(); // 默认

        foreach ($letterLists as $char) {
            $url = "https://i.meituan.com/index/changecity/more/{$char}";
            $content = file_get_contents($url);
            // 匹配内容
            $pattern = "/<ul class=\"table box nopadding\">(.*?)<\/ul>/ism";
            preg_match($pattern, $content, $matchArr);
            $matchContent = $matchArr[0];
//            print_r($matchContent);
            $letterPattern = "/<a.*?href=\"(.*?)\".*?>(.*?)<\/a>?/ism";
            preg_match_all($letterPattern, $matchContent, $matchCityLists);
            $charCityLists = [];
            foreach ($matchCityLists[0] as $key1 => $row1) {
                $letterPattern2 = "/<a(.*?)href=\"(.*?)\".*?>(.*?)<\/a>?/is";
                preg_match_all($letterPattern2, $row1, $matchCityRow);
//                print_r($matchCityRow);
                $cityName = $matchCityRow[3][0];
                $cityUrl = trim('http:'.$matchCityRow[2][0], '');

                $cityNameArr = $pinyin->convert($cityName);
                $citySpell = implode('', $cityNameArr);
                $cityLetterSort = strtoupper($citySpell[0]);

                $row1New = [
                    'name' => $cityName,
                    'url' => $cityUrl,
                    'spell' => $citySpell,
                    'letterSort' => $cityLetterSort,
                    'level' =>  'city',
                ];
//                print_r($row1New);
                // 访问城市页面,获取设置到cookie中的城市id
                file_get_contents($cityUrl, false, $context);
                preg_match("/Set-Cookie: ci=(\d+);/",$http_response_header[8], $cityIdArr);
//                print_r($cityIdArr);
                $cityId = $cityIdArr[1];
                $cityName = urlencode($cityName);
                $opts2 = array (
                    'http' => array (
                        'method' => 'GET',
                        'header'=>
                            "Cookie: JSESSIONID=1gobtc897uwm818q773348724s; IJSESSIONID=1gobtc897uwm818q773348724s; iuuid=8566A48949312C8934F653BDA6376FE2AC4AB7F10FE1956708F7DAC15EA1C215; nodown=yes; _lx_utm=utm_campaign%3Dm.baidu%26utm_medium%3Dorganic%26utm_source%3Dm.baidu%26utm_content%3D100037%26utm_term%3D; _lxsdk_cuid=1627b2fbdc0c8-053e988c345e8-2d604637-3d10d-1627b2fbdc1c8; _lxsdk=8566A48949312C8934F653BDA6376FE2AC4AB7F10FE1956708F7DAC15EA1C215; __utmc=74597006; __utmz=74597006.1522485150.1.1.utmcsr=m.baidu|utmccn=m.baidu|utmcmd=organic|utmcct=100037; ci3=1; uuid=554ec9d8-b432-48ec-87da-11e9b3d50850; _hc.v=c689cb65-0db6-16e9-36bb-be8e59556dd2.1522485384; webp=1; __utma=74597006.1495684570.1522485150.1522485150.1522500747.2; ci={$cityId}; cityname={$cityName}; \r\n"
                    )
                );
                $context2 = stream_context_create($opts2);
                $content = file_get_contents("http://meishi.meituan.com/i/", false, $context2);
                preg_match("/<div class=\"biz-wrapper.*?\".*?>(.*?)<\/div>/ism", $content, $matches3);
                preg_match_all("/<a.*?><span.*?>(.*?)<\/span>(.*?)<\/a>/ism", $matches3[0], $matches4);
                $row1New['lists'] = $matches4[1];
                $charCityLists[] = $row1New;
//                break;
            }
            $charCityListsNew = [];
            $charCityListsNew[$char] = $charCityLists;
            $str = json_encode($charCityListsNew);
            file_put_contents(__DIR__."/meituan-spider/city-$char.json", $str);
//            break;
        }
    }

    /**
     * 城市地区数据清理
     */
    public static function setFixTestSaveCityAreaClear()
    {
        $cityArea = new CityArea();
        $regionTable = $cityArea->getRegion()->getTableName();
        $regionTranslationTable = $cityArea->getRegionTranslation()->getTableName();

        $sql = <<<str
-- ----------------------------
-- Table structure for city_area
-- ----------------------------
DROP TABLE IF EXISTS `{$regionTable}`;
CREATE TABLE `{$regionTable}` (
  `region_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `region_pid` int(11) unsigned DEFAULT '0' COMMENT '父id,本表中的region_id字段,0没有父级',
  `region_path` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT '路径',
  `region_level` int(11) unsigned DEFAULT NULL COMMENT '层级',
  `region_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `region_code` varchar(64) DEFAULT NULL COMMENT '地区代码',
  `region_hot` int(11) DEFAULT '0' COMMENT '热门地区',
  `region_changed` int(11) DEFAULT '0' COMMENT '操作时间: 防止更新失败情况',
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='多国家城市区域';

-- ----------------------------
-- Records of city_area
-- ----------------------------

-- ----------------------------
-- Table structure for city_area_translation
-- ----------------------------
DROP TABLE IF EXISTS `{$regionTranslationTable}`;
CREATE TABLE `{$regionTranslationTable}` (
  `lang_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `region_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '实体id',
  `lang_name` varchar(255) DEFAULT NULL COMMENT '翻译',
  `spell` varchar(255) DEFAULT '' COMMENT '拼写: 例如中文是拼音',
  `lang_code` varchar(12) NOT NULL DEFAULT '0' COMMENT '评论人',
  `letter_sort` varchar(64) DEFAULT '' COMMENT '字母排序',
  `lang_sort` int(11) DEFAULT '0' COMMENT '排序',
  `lang_changed` int(11) DEFAULT '0' COMMENT '操作时间: 防止更新失败情况',
  PRIMARY KEY (`lang_id`),
  UNIQUE KEY `unique` (`region_id`,`lang_code`) USING BTREE,
  KEY `region_id` (`region_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='多国家城市区域翻译';
str;

        DB::getInstance()->getConnection()->exec($sql);
    }

    /**
     * 测试生成国家城市区域
     */
    public function testGeneratorCountryCityArea()
    {
        self::setFixTestSaveCityAreaClear();
        $content = file_get_contents(__DIR__.'/meituan-spider/letter-lists.json');
        $letterLists = json_decode($content, true);
        print_r($letterLists);

        $cityArea = new CityArea();
        $langLists = [
            'zh-cn',
            'en-us',
        ];

        $country = [
            'name' => "中国",
            'spell' => '',
            'letterSort' => '',
            'level' => 'country',
        ];
        $pinyin = new Pinyin(); // 默认
        $nameArr = $spell = $letterSort = null;
        $nameArr = $pinyin->convert($country['name']);
        $spell = implode('', $nameArr);
        $letterSort = strtoupper($spell[0]);

        $countryId = $cityArea->addCountry($country['name']);
        foreach ($langLists as $lang) {
            $countryLangName = $lang == 'zh-cn' ? $country['name'] : $country['name'].'.'.$lang;
            $cityArea->getRegionTranslation()->t($countryId, $lang, $countryLangName, $spell, $letterSort);
        }
        foreach ($letterLists as $char) {
            $content = file_get_contents(__DIR__."/meituan-spider/city-$char.json");
            $cityLists = json_decode($content, true);
            $charCityLists = $cityLists[$char];
            foreach ($charCityLists as $key1 => $row1) {

                $cityId = $cityArea->addCity($countryId, $row1['name']);
                foreach ($langLists as $lang) {
                    $cityLangName = $lang == 'zh-cn' ? $row1['name'] : $row1['name'].'.'.$lang;
                    $cityArea->getRegionTranslation()->t($cityId, $lang, $cityLangName, $row1['spell'], $row1['letterSort']);
                }
                $areaLists = $row1['lists'];
                $areaListsNew = [];
                foreach ($areaLists as $areaName) {
                    if ($areaName == '全城') {
                        continue;
                    }
                    $nameArr = $spell = $letterSort = null;
                    $nameArr = $pinyin->convert($areaName);
                    $spell = implode('', $nameArr);
                    $letterSort = strtoupper($spell[0]);
                    $areaListsNew[] = [
                        'name' => $areaName,
                        'spell' => $spell,
                        'letterSort' => $letterSort,
                    ];
                    $areaId = $cityArea->addCityArea($countryId, $cityId, $areaName);
                    foreach ($langLists as $lang) {
                        $areaLangName = $lang == 'zh-cn' ? $areaName : $areaName.'.'.$lang;
                        $cityArea->getRegionTranslation()->t($areaId, $lang, $areaLangName, $spell, $letterSort);
                    }
                }
                $row1['lists'] = $areaListsNew;
            }
//            $countryLists[0]['lists'][$char] = $cityLists[$char];
//            break;
        }

//        file_put_contents(__DIR__."/meituan-spider/region-merge.json", json_encode($countryLists, true));
    }
}