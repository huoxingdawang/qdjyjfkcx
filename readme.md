# 青岛市教育局新搞的饭卡的交易查询

## 简介 
青岛市教育局新搞的饭卡的交易查询
    
知道学籍号即可查询

## 使用方法
自行安装PHP，并打开CURL,PDO扩展

然后
```bash
php main.php 学籍号
```

比如
```bash
php main.php 2017370201880930***
```

查询学籍号为2017370201880930**的交易记录

## 一些扩展
### 使用已经搞好的数据库
修改\jry_wb_configs\jry_wb_config_database_user.php
```php
<?php
	include_once('jry_wb_config_default_user.php');
	define('JRY_WB_DATABASE_NAME'			,'qiafan');
	define('JRY_WB_DATABASE_ADDR'			,'juruoyun.top');
	define('JRY_WB_DATABASE_USER_NAME'		,'qiafan');
	define('JRY_WB_DATABASE_USER_PASSWORD'	,'qiafan');
	define('JRY_WB_DATABASE_ALL_PREFIX'		,'');
?>
```

然后

```bash
php db_find.php [-data][-logs] [name/xjh] balabalabala
```
比如
```bash
php db_find.php -data-logs name "丁宁"
```
可以查询 丁宁 同志的信息和交易记录

注:我也不知道这是谁。。。。

注:悠着点，服务器1核2G1M....

注:现在聚合一遍数据要3小时左右，9000左右的学生，10^6左右的log

注:包含一中2017级，一中2018级，一中2019级，二中2017级，二中2018级，二中2019级，十五中2017级，十五中2018级，九中2017级，九中2018级，九十七中2017级，九十七中2018级，三十九中2017级，三十九中2018级，六中2017级，六中2018级，十九中2017级，十九中2018级

差不多这样？


~~这玩意真水，啥都没有，QQ音乐和网易云音乐起码还有防盗链以及参数加密之类的蛇皮玩意。。。~~