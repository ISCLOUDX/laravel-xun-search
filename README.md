Laravel 5.2 XunSearch
==============

XunSearch是一个开源免费、高性能、多功能
简单易用的专业全文检索技术方案
帮助一般开发者针对既有的海量数据，快速而方便地建立自己的全文搜索引擎。全文检索可以帮助您降低服务器搜索负荷、极大程度的提高搜索速度和用户体验

本package根据davin-bao/laravel-xun-search修改得来的

在使用这个包之前您需要先参照http://www.xunsearch.com/doc/php/guide/start.installation

进行安装Xunsearch后端程序

## 安装说明

应该执行composer require iscloudx.laravel-xun-search dev-master --dev 来安装这个包
添加依赖到 composer.json 然后运行命令 composer update:

```json
{
	"require": {
        "iscloudx/laravel-xun-search": "dev-master"
	}
}
```

然后您应该添加到 DavinBao\LaravelXunSearch\ServiceProvider::class 到 `app/config/app.php` 的 providers

```php
'providers' => [
	DavinBao\LaravelXunSearch\ServiceProvider::class,
],
```

和 'Search' => DavinBao\LaravelXunSearch\Facade::class, 到 `app/config/app.php` 的 aliases

```php
'aliases' => [
	'Search' => DavinBao\LaravelXunSearch\Facade::class,
],
```
## 配置项 

运行下面这个命令将配置项发布到项目下

```bash
php artisan vendor:publish --provider="DavinBao\LaravelXunSearch\ServiceProvider"
```
###基本
然后您应该得到类似下面的结果

```php

//@see http://www.xunsearch.com/doc/php/guide/ini.guide
"project" => [
    "project.name" => "demo",
    "project.default_charset" => "utf-8",
    "server.index" => "127.0.0.1:8383",
    "server.search" => "127.0.0.1:8384",
    //remember change FIELD_LABEL_DEFAULT_SEARCH_PK value in Config.php
    "primary_key" => [
        "type" => "id"
    ],
    //remember change FIELD_LABEL_DEFAULT_CLASS_ID value in Config.php
    "class_uid" => [
        "index" => "both"
    ],
    //remember change FIELD_LABEL_DEFAULT_DB_PK value in Config.php
    "id" => [
        "type" => "numeric"
    ],
    "username" => [
        "type" => "title"
    ],
    "email" => [
        "index" => "both"
    ],
    "last_seen" => [
        "type" => "numeric"
    ],
    "role" => [
        "index" => "both"
    ],
    "uri" => [
        "index" => "both"
    ],
    "action" => [
        "index" => "both"
    ],
],

'index' => [
	
	// ...

	namespace\FirstModel::class => [
		'fields' => [
			'name', 'full_description', // fields for indexing
		],
		'primary_key' => 'id'  //primary_key name in DB, default 'id'
	],
	
	namespace\SecondModel::class => [
		'fields' => [
			'name', 'short_description', // fields for indexing
		]
	],
	
	// ...
	
],

```

## Usage
### Artisan commands
#### Initialize or rebuild search index
重建索引应该使用下面这个命令

```bash
php artisan search:rebuild --verbose
```
#### Clear search index
清空索引使用下面这个命令

```bash
php artisan search:clear
```
#### Filtering of models in search results 
应该为您的模型实现 `SearchableInterface`.
例如:

```php

use Illuminate\Database\Eloquent\Model;
use DavinBao\LaravelXunSearch\Model\SearchableInterface;

class Dummy extends Model implements SearchableInterface
{
        // ...

        /**
         * Get id list for all searchable models.
         */
        public static function searchableIds()
        {
            return self::wherePublish(true)->lists('id');
        }

        // ...
}

```

### Partial updating of search index
For register of necessary events (save/update/delete) `use DavinBao\LaravelXunSearch\Model\SearchTrait` in target model:
使用SearchTrait是必须的，不然你得不到任何你想要的结果
```php

    use Illuminate\Database\Eloquent\Model;
    use DavinBao\LaravelXunSearch\Model\SearchableInterface;
    use DavinBao\LaravelXunSearch\Model\SearchTrait;

    class Dummy extends Model implements SearchableInterface
    {
        use SearchTrait;
    
        // ...
    }

```

### Query building
Build query in several ways:

#### Using constructor:

By default, queries which will execute search in the **phrase entirely** are created.

##### Simple queries
```php
$query = Model::getSearch()->addQuery("clock"); // search by all fields.
// or 
$query = Model::getSearch()->addQuery('name:clock'); // search by 'name' field.
// or
$query = Model::getSearch()->addQuery('name:clock'); // filter by 'short_description' field.

$Ids = Model::getSearch()->addQuery('name:clock')->getIDList(); // filter by 'short_description' field.
```

### Getting of results

For built query are available following actions:

#### Get all found models

```php
$models = $query->search();
```

#### Get all found models ID

```php
$models = $query->getIDList();
```

#### Get count of results
```php
$count = $query->count();
```

#### Get limit results with offset

```php
$models = $query->limit(5, 10)->get(); // Limit = 5 and offset = 10
```
#### Sort

```php
$query = $query->setSort('chrono', true);
```
### Highlighting of matches

Highlighting of matches is available for any html fragment encoded in **utf-8** and is executed only for the last executed request.

```php

$docs = $search->setQuery('测试')->setLimit(5)->search();
foreach ($docs as $doc)
{
   $subject = $search->highlight($doc->subject); // 高亮处理 subject 字段
   $message = $search->highlight($doc->message); // 高亮处理 message 字段
   echo $doc->rank() . '. ' . $subject . " [" . $doc->percent() . "%] - ";
   echo date("Y-m-d", $doc->chrono) . "\n" . $message . "\n";
}

```
享受高速的全文查询吧
##
## License
Package licenced under the MIT license.
