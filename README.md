# zwei/comment 包

> 为了在不同的项目中验证验证码，保存到验证码到数据库方便,做日志记录

## 1 安装(Install)
> 1. 通过Composer安装
> 2. 创建composer.json文件,并写入以下内容:

```json
{
  "require": {
    "zwei/comment": "dev-develop"
  }
}
```
> 3. 执行composer install

> 4. 在项目目录创建config/bao-loan.yml文件,添加一下内容

```yml
BASE_TEST_CONFIG: "base_test_config" # 本包测试键（单元测试用）

# 数据库配置
DB_HOST: "localhost" # 主机
DB_PORT: 3306 # 端口
DB_USER: "root" # 用户名
DB_PASS: "root" # 密码
DB_NAME: "demo" # 数据库名
DB_TABLE_PREFIX: "" # 表前缀
DB_CHARSET: "utf8" # 设置字符编码,空字符串不设置
DB_SQLLOG: false # 是否启用sql调试
```
> 5. 导入"dev/sql/city_area.20180403.sql"文件中的sql到数据库中

## 常用查询语句
```sql
-- 查询所有国家列表
select * from city_area c INNER JOIN city_area_translation ct on c.region_id=ct.region_id
where region_pid = 0 and `region_level` = 1 and lang_code = 'zh-cn' order by letter_sort asc;

-- 查询中国所有城市列表
select * from city_area c INNER JOIN city_area_translation ct on c.region_id=ct.region_id
where region_pid = 1 and `region_level` = 2 and lang_code = 'zh-cn' order by letter_sort asc;

-- 查询中国 安岳城市 所有区域列表
select * from city_area c INNER JOIN city_area_translation ct on c.region_id=ct.region_id
where region_pid = 80 and `region_level` = 3 and lang_code = 'zh-cn';


-- 查询中国 成都城市 所有区域列表
select * from city_area c INNER JOIN city_area_translation ct on c.region_id=ct.region_id
where region_pid = 554 and `region_level` = 3 and lang_code = 'zh-cn';

select count(*) /*10467条数据*/ from city_area;
```

## 多国家城市地区使用示例(use)
> 1. 例如项目目录在"E:\web\php7\test"
> 2. 创建index.php,并加入以下内容

```php
<?php
include_once 'vendor/autoload.php';

use Overtrue\Pinyin\Pinyin;
use Zwei\Region\Custom\CityArea\CityArea;

$pinyin = new Pinyin(); // 默认
        
$cityArea   = new CityArea();
// 添加国家
$countryId  = $cityArea->addCountry("国家名");
$lang       = 'zh-cn';// 语言code
$countryLangName = "中国";// 中文国家名

// 中文转拼音
$nameArr    = $pinyin->convert($countryLangName);
$spell      = implode('', $nameArr);// 拼写(中文语言是中文拼音, 英语语言就是英语)
$letterSort = strtoupper($spell[0]);// 拼写首字母
// 添加国家翻译
$cityArea->getRegionTranslation()->t($countryId, $lang, $countryLangName, $spell, $letterSort);

// 添加城市
$cityId = $cityArea->addCity($countryId, "城市名");
// 城市翻译
$cityArea->getRegionTranslation()->t($cityId, 'zh-cn', '成都', 'chengdu', 'C');

// 添加城市地区
$areaId = $cityArea->addCityArea($countryId, $cityId, '青羊区');
// 城市地区翻译
$cityArea->getRegionTranslation()->t($cityId, 'zh-cn', '青羊区', 'qingyangqu', 'Q');

// 获取中文语言国家列表
$lists = $cityArea->getCountryLists('zh-cn');
// 取中国中文语言城市列表
$lists = $cityArea->getCityLists(1,'zh-cn');
// 取中国 成都城市中文语言区域列表
$lists = $cityArea->getCityAreaLists(554,'zh-cn');
```

## 自定义地区请使用"Region"和"Region"
> 请参考"Zwei\Region\Custom\CityArea\CityArea"类,该类就是使用的"Region"和"RegionTranslation"封装的

```php
use Zwei\Region\Region;
use Zwei\Region\RegionTranslation;

$region = new Region();
$regionTranslation = new RegionTranslation();
```

### 单元测试使用
> --bootstrap 在测试前先运行一个 "bootstrap" PHP 文件
* **--bootstrap引导测试:** phpunit --bootstrap tests/TestInit.php tests/

* **--bootstrap美团爬取城市字母测试:** phpunit --bootstrap tests/TestInit.php tests/MeiTuanSpiderTest.php --filter testGetLetterLists
* **--bootstrap美团爬取城市列表测试:** phpunit --bootstrap tests/TestInit.php tests/MeiTuanSpiderTest.php --filter testGetCityLists
* **--bootstrap根据美团城市信息生成多国家多语言城市区域地区信息测试:** phpunit --bootstrap tests/TestInit.php tests/MeiTuanSpiderTest.php --filter testGeneratorCountryCityArea

* **--bootstrap国家城市地区列表查询测试:** phpunit --bootstrap tests/TestInit.php tests/CityAreaTest.php

D:\phpStudy\php\php-7.1.13-nts\php.exe D:\phpStudy\php\php-5.6.27-nts\composer.phar update

D:\phpStudy\php\php-7.1.13-nts\php.exe vendor\phpunit\phpunit\phpunit --bootstrap tests/TestInit.php tests/