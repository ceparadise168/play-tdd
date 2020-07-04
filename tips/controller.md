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

改利用 Service Container (DI) 來自動注入 ArticleRepository

```
    protected $repository;

    /**
     * ArticleController constructor.
     * @param ArticleRepository $repository
     */
    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$articles = [];
        $articles = $this->repository->latest10();

        return view('articles.index', compact('articles'));
    }
```

紅燈

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.....F.                                                             7 / 7 (100%)

Time: 2.07 seconds, Memory: 30.00 MB

There was 1 failure:

1) Tests\Feature\ArticleControllerTest::testArticleList
Expected status code 200 but received 500.
Failed asserting that 200 is identical to 500.

C:\Users\cepar\Desktop\projects\play-tdd\blog\vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:185
C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Feature\ArticleControllerTest.php:17

FAILURES!
Tests: 7, Assertions: 17, Failures: 1.

```

在測試中印出回傳，看錯誤內容

```
    public function testArticleList()
    {
        // 用 GET 方法瀏覽網址 /article
        $response = $this->get('/article');

        dd($response->getContent());

        // 改用 Laravel 內建方法
        // 實際就是測試是否為 HTTP 200
        $response->assertStatus(200);

        // 應取得 articles 變數
        $response->assertViewHas('articles');
    }
```

缺少資料表

    Illuminate\Database\QueryException: SQLSTATE[HY000]: General error: 1 no such table: articles (SQL: select * from "articles" order by "id" desc limit 10)
    

由於這邊我們只要測試 controller index 行為是否正常，故不需要用到資料庫，這邊可以利用 mock 物件隔離資料庫做測試

http://docs.mockery.io/en/latest/index.html

https://laravel.com/docs/7.x/mocking#introduction

```
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Repositories\ArticleRepository;

class ArticleControllerTest extends TestCase
{
    public function testArticleList()
    {
        $this->mock(ArticleRepository::class, function ($mock) {
            $mock->shouldReceive('latest10')->once()->andReturn([]);
        });

        // 用 GET 方法瀏覽網址 /article
        $response = $this->get('/article');

        // 改用 Laravel 內建方法
        // 實際就是測試是否為 HTTP 200
        $response->assertStatus(200);

        // 應取得 articles 變數
        $response->assertViewHas('articles', []);
    }
}
```

綠燈

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.......                                                             7 / 7 (100%)

Time: 1.79 seconds, Memory: 30.00 MB

OK (7 tests, 19 assertions)

```


---

建立文章

```
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 直接從 Http\Request 取得輸入資料
        $this->repository->create($request->all());

        // 導向列表頁
        return redirect()->action('ArticleController@index');
    }
```

```
    public function testCreateArticleCSRFFailed()
    {
        $parameters = [];

        $response = $this->post('article', $parameters);

        $response->assertStatus(419);
    }
```

因為 laravel 中 get 以外的方法有 csrf 保護，所以預期要失敗，但卻得到 200

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

......F.                                                            8 / 8 (100%)

Time: 1.83 seconds, Memory: 30.00 MB

There was 1 failure:

1) Tests\Feature\ArticleControllerTest::testCreateArticleCSRFFailed
Expected status code 419 but received 200.
Failed asserting that 419 is identical to 200.

C:\Users\cepar\Desktop\projects\play-tdd\blog\vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:185
C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Feature\ArticleControllerTest.php:36

FAILURES!
Tests: 8, Assertions: 20, Failures: 1.

```

先看看 csrf middleware 註冊在哪邊

// kernel.php
```
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
```

接著可以看到測試時不驗證 csrf token

    vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php
    
```
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        throw new TokenMismatchException('CSRF token mismatch.');
    }
```

如果需要模擬驗證 csrf 過程的話，可以加上
```
   public function testCreateArticleCSRFFailed()
    {
        $this->app['env'] = 'production';

        $parameters = [];

        $response = $this->post('article', $parameters);

        $response->assertStatus(419);
    }
```

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

........                                                            8 / 8 (100%)

Time: 1.83 seconds, Memory: 30.00 MB

OK (8 tests, 20 assertions)

```

測試帶 token 通過

```
    public function testCreateArticleWithCSRFSuccess()
    {
        $this->app['env'] = 'production';

        Session::start();

        $parameters = [
            'title' => 'title 999',
            'body' => 'body 999',
            '_token' => csrf_token(), // 手動加入 _token
        ];

        $response = $this->post('article', $parameters);

        $response->assertStatus(302);
        $response->assertRedirect('article');
    }
```

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.......F.                                                           9 / 9 (100%)

Time: 2 seconds, Memory: 32.00 MB

There was 1 failure:

1) Tests\Feature\ArticleControllerTest::testCreateArticleWithCSRFSuccess
Expected status code 302 but received 500.
Failed asserting that 302 is identical to 500.

C:\Users\cepar\Desktop\projects\play-tdd\blog\vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:185
C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Feature\ArticleControllerTest.php:58

FAILURES!
Tests: 9, Assertions: 21, Failures: 1.

```

補上 mock 物件

```
    public function testCreateArticleWithCSRFSuccess()
    {
        $this->mock(ArticleRepository::class, function ($mock) {
            $mock->shouldReceive('create')->once()->andReturn(true);
        });

        $this->app['env'] = 'production';

        Session::start();

        $parameters = [
            'title' => 'title 999',
            'body' => 'body 999',
            '_token' => csrf_token(), // 手動加入 _token
        ];

        $response = $this->post('article', $parameters);

        $response->assertStatus(302);
        $response->assertRedirect('article');
    }
```

綠燈

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.........                                                           9 / 9 (100%)

Time: 1.9 seconds, Memory: 30.00 MB

OK (9 tests, 24 assertions)

```
