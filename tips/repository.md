test the repository
===

Repository 用來對 Model 操作，

開始測試前，先建立 Repositories 目錄，並新增

    ArticleRepository.php

建立 ArticleRepositoryTest.php ，設定測試前後準備

```
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Article;
use App\Repositories\ArticleRepository;

class ArticleRepositoryTest extends TestCase
{
    /**
     * @var ArticleRepository
     */
    protected $repository = null;

    /**
     * 建立 100 筆假文章
     */
    protected function seedData()
    {
        for ($i = 1; $i <= 100; $i ++) {
            Article::create([
                'title' => 'title ' . $i,
                'body'  => 'body ' . $i,
            ]);
        }
    }

    // 跟前面一樣，每次都要初始化資料庫並重新建立待測試物件
    // 以免被其他 test case 影響測試結果
    public function setUp():void
    {
        parent::setUp();

        $this->initDatabase();
        $this->seedData();

        // 建立要測試用的 repository
        $this->repository = new ArticleRepository();
    }

    public function tearDown():void
    {
        $this->resetDatabase();
        $this->repository = null;
    }
}
```

現在我們要在 Repository 中新增一個方法，可以取得最新的十筆文章。

首先 TDD 的第一步，在開始寫 code 之前先寫測試

```
    public function testFetchLatest10Articles()
    {
        // 從 repository 中取得最新 10 筆文章
        $articles = $this->repository->latest10();
        $this->assertEquals(10, count($articles));

        // 確認標題是從 100 .. 91 倒數
        // "title 100" .. "title 91"
        $i = 100;
        foreach ($articles as $article) {
            $this->assertEquals('title ' . $i, $article->title);
            $i -= 1;
        }
    }
```

第二步，執行測試得到紅燈(測試不通過)，這時候應該只能有一個紅燈，接著想辦法讓它變綠燈

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

E....                                                               5 / 5 (100%)

Time: 1.47 seconds, Memory: 28.00 MB

There was 1 error:

1) Tests\Unit\ArticleRepositoryTest::testFetchLatest10Articles
Error: Call to undefined method App\Repositories\ArticleRepository::latest10()

C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Unit\ArticleRepositoryTest.php:52

ERRORS!
Tests: 5, Assertions: 4, Errors: 1.

```

第三步，只針對紅燈原因修正，並且不應該去修正其他不相關的問題

```
<?php
namespace App\Repositories;

use App\Models\Article;

class ArticleRepository
{
    public function latest10()
    {
        return Article::query()->orderBy('id', 'desc')->limit(9)->get();
    }
}
```

回到第一步，並重複 1~3 反覆驗證結果直到綠燈(通過測試)，流程如下:

紅燈
```
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

F....                                                               5 / 5 (100%)

Time: 1.45 seconds, Memory: 28.00 MB

There was 1 failure:

1) Tests\Unit\ArticleRepositoryTest::testFetchLatest10Articles
Failed asserting that 9 matches expected 10.

C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Unit\ArticleRepositoryTest.php:52

FAILURES!
Tests: 5, Assertions: 5, Failures: 1.

```

修正

```
<?php
namespace App\Repositories;

use App\Models\Article;

class ArticleRepository
{
    public function latest10()
    {
        return Article::query()->orderBy('id', 'desc')->limit(10)->get();
    }
}
```

綠燈

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.....                                                               5 / 5 (100%)

Time: 1.45 seconds, Memory: 28.00 MB

OK (5 tests, 15 assertions)
```

---

ArticleRepositroy 新增 Create Method

先寫測試

ArticleRepositroyTest.php

```
    public function testCreateArticles()
    {
        $maxId = Article::max('id');
        $latestId = ++$maxId;

        $article = $this->repository->create([
            'title' => 'title ' . $latestId,
            'body'  => 'body ' . $latestId,
        ]);

        $this->assertEquals($latestId, $article->id);
    }
```

紅燈

```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

.E....                                                              6 / 6 (100%)

Time: 1.76 seconds, Memory: 28.00 MB

There was 1 error:

1) Tests\Unit\ArticleRepositoryTest::testCreateArticles
Error: Call to undefined method App\Repositories\ArticleRepository::create()

C:\Users\cepar\Desktop\projects\play-tdd\blog\tests\Unit\ArticleRepositoryTest.php:68

```

重構

ArticleRepositroy.php

```
    public function create($attributes)
    {
        return Article::create($attributes);
    }
```

綠燈
```
$ ./vendor/bin/phpunit
PHPUnit 8.5.8 by Sebastian Bergmann and contributors.

......                                                              6 / 6 (100%)

Time: 1.73 seconds, Memory: 28.00 MB

OK (6 tests, 16 assertions)
```
