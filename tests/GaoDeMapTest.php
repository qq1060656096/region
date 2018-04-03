<?php
namespace Zwei\Region\Tests;

use Zwei\Base\Config;
use Zwei\Region\Custom\CityArea\CityArea;
use Overtrue\Pinyin\Pinyin;

/**
 * 高的地图
 *
 * Class GaoDeMapTest
 * @package Zwei\Region\Tests
 */
class GaoDeMapTest extends BaseTestCase
{

    public function test()
    {
//        $pinyin = new Pinyin(); // 默认
//        $countrySpell = implode('', $countrySpellArr);
//        $letterSort = strtoupper($countrySpell[0]);
    }

    /**
     * 生成多国家中国城市地区信息
     */
    public function ttestGenerator()
    {
        $key = Config::get('MAP_GAO_DE_KEY');
        $url = "http://restapi.amap.com/v3/config/district?key={$key}&keywords=中国&subdistrict=3&extensions=base";
        $content = file_get_contents($url);
        $data = json_decode($content, true);
        $countryLists = $data['districts'];

        $cityArea = new CityArea();
        $langArr = [
            'zh_cn',
            'en_us',
            'zh_tw',
        ];
        $pinyin = new Pinyin(); // 默认

        foreach ($countryLists as $key => $country) {

            if ($country['level'] != 'country') {
                contine;
            }
            $countryName = $country['name'];
            $countryId = $cityArea->addCountry($countryName);
            $countrySpellArr = $pinyin->convert("中国");
            $countrySpell = implode('', $countrySpellArr);
            $letterSort = strtoupper($countrySpell[0]);
            foreach ($langArr as $key3 => $row3LangCode) {
                $countryLangName = $row3LangCode == 'zh_cn' ? $countryName: "{$countryName}.{$row3LangCode}";
                $letterSort = $countrySpell[0];
                $cityArea->getRegionTranslation()->t($countryId, $row3LangCode, $countryLangName, $countrySpell, $letterSort);
            }

            foreach ($country['districts'] as $key4 => $province) {
//                unset($province['districts']);
                print_r($province);
                break;
            }
        }
    }

}
