model
===
在開始測試前 Laravel 已經有一些預設的測試案例了，先跑跑看...

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 803 ms, Memory: 20.00 MB

OK (2 tests, 2 assertions)
```

phpunit.xml 中定義了一些參數，其中 database 為了跟正是環境分開，使用了 sqlite 這組 connection 設定，

```xml
    <php>
        <server name="APP_ENV" value="testing"/>
        <server name="BCRYPT_ROUNDS" value="4"/>
        <server name="CACHE_DRIVER" value="array"/>
        <server name="DB_CONNECTION" value="sqlite"/>
        <server name="DB_DATABASE" value=":memory:"/>
        <server name="MAIL_MAILER" value="array"/>
        <server name="QUEUE_CONNECTION" value="sync"/>
        <server name="SESSION_DRIVER" value="array"/>
        <server name="TELESCOPE_ENABLED" value="false"/>
    </php>
```

詳細 database connection 設定內容可以參照 `config/database.php`
```
    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
```

建立一個 model，其中 -m 參數為順便建立 migration 檔案
```
$ php artisan make:model Article -m
Model created successfully.
Created Migration: 2020_07_03_052244_create_articles_table
```

執行完會發現多了這兩個檔案
```
app/Article.php
database/migrations/2020_07_03_052244_create_articles_table.php
```

修改 Model，設定可以被修改的欄位，保護其他欄位不會被修改
```
class Article extends Model
{
    protected $fillable = ['title', 'body'];
}
```

更新 migration，替欲新增的 Article 資料庫結構加上 title 與 body 欄位

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
```

為了目錄結構乾淨，建立一個 Models 資料夾，並統一將 Model 放到下面管理

    app/Models/Articel.php

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'body'];
}
```

---


為了讓每一次的測試都獨立互不影響，我們需要將每一次測試前的資料都重新建立，並在測試後還原

```
<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function initDatabase()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    protected function resetDatabase()
    {
        Artisan::call('migrate:reset');
    }
}
```

在 Unit 底下 建立 ArticleTest.php

```
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Article;

class ArticleTest extends TestCase
{
    protected  function setUp():void
    {
        // 一定要先呼叫，建立 Laravel Service Container 以便測試
        parent::setUp();

        $this->initDatabase();
    }

    protected  function tearDown():void
    {
        $this->resetDatabase();
    }

    public function testEmptyResult()
    {
        $articles = Article::all();
        
        $this->assertEquals(0, count($articles));
    }
    
    public function testCreateAndList()
    {
        for ($i = 1; $i <= 10; $i ++) {
            Article::create([
                'title' => 'title ' . $i,
                'body'  => 'body ' . $i,
            ]);
        }

        $articles = Article::all();
        $this->assertEquals(10, count($articles));
    }
}
```

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

....                                                                4 / 4 (100%)

Time: 1.17 seconds, Memory: 26.00 MB

OK (4 tests, 4 assertions)

```
