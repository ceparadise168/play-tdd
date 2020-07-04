Controller
===

目標: 建立一個取得文章列表的 route  "/posts"

對 Controller 行為寫測試 (https://laravel.com/docs/7.x/http-tests)
```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    public function testArticleList()
    {
        // 用 GET 方法瀏覽網址 /article
        $response = $this->get('/article');

        // 改用 Laravel 內建方法
        // 實際就是測試是否為 HTTP 200
        $response->assertStatus(200);

        // 應取得 articles 變數
        $response->assertViewHas('articles');
    }
}
```

失敗

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.....F.                                                             7 / 7 (100%)

Time: 1.78 seconds, Memory: 30.00 MB

There was 1 failure:

1) Tests\Feature\ArticleControllerTest::testArticleList
Expected status code 200 but received 404.
Failed asserting that 200 is identical to 404.

C:\Users\cepar\Desktop\projects\play-tdd\blog\vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:185
C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Feature\ArticleControllerTest.php:16

FAILURES!
Tests: 7, Assertions: 17, Failures: 1.

```
    php artisan make:controller ArticleController --resource

在 routes/web.php 中註冊
    
    Route::resource('article', 'ArticleController');

```
$ php artisan route:list
+--------+-----------+------------------------+-----------------+------------------------------------------------+------------+
| Domain | Method    | URI                    | Name            | Action                                         | Middleware |
+--------+-----------+------------------------+-----------------+------------------------------------------------+------------+
|        | GET|HEAD  | /                      |                 | Closure                                        | web        |
|        | GET|HEAD  | api/user               |                 | Closure                                        | api        |
|        |           |                        |                 |                                                | auth:api   |
|        | GET|HEAD  | article                | article.index   | App\Http\Controllers\ArticleController@index   | web        |
|        | POST      | article                | article.store   | App\Http\Controllers\ArticleController@store   | web        |
|        | GET|HEAD  | article/create         | article.create  | App\Http\Controllers\ArticleController@create  | web        |
|        | GET|HEAD  | article/{article}      | article.show    | App\Http\Controllers\ArticleController@show    | web        |
|        | PUT|PATCH | article/{article}      | article.update  | App\Http\Controllers\ArticleController@update  | web        |
|        | DELETE    | article/{article}      | article.destroy | App\Http\Controllers\ArticleController@destroy | web        |
|        | GET|HEAD  | article/{article}/edit | article.edit    | App\Http\Controllers\ArticleController@edit    | web        |
+--------+-----------+------------------------+-----------------+------------------------------------------------+------------+

```


```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.....F.                                                             7 / 7 (100%)

Time: 1.78 seconds, Memory: 30.00 MB

There was 1 failure:

1) Tests\Feature\ArticleControllerTest::testArticleList
The response is not a view.

C:\Users\cepar\Desktop\projects\play-tdd\blog\vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:940
C:\Users\cepar\Desktop\projects\play-tdd\blog\vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:870
C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Feature\ArticleControllerTest.php:19

FAILURES!
Tests: 7, Assertions: 18, Failures: 1.

```

第一個 assert status 200 通過了，接下來換通過第二個

```
        $response = $this->get('/article');

        $response->assertStatus(200);

        $response->assertViewHas('articles');
```

新增 view
```
cepar@DESKTOP-H3LUMQB MINGW64 ~/Desktop/projects/play-tdd/blog (master)
$ mkdir -p resources/views/articles

cepar@DESKTOP-H3LUMQB MINGW64 ~/Desktop/projects/play-tdd/blog (master)
$ touch resources/views/articles/index.blade.php
```

controller index method 回傳 view


```
class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = [];

        return view('articles.index', compact('articles'));
    }
```

綠燈

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.......                                                             7 / 7 (100%)

Time: 1.77 seconds, Memory: 30.00 MB

OK (7 tests, 18 assertions)

```
