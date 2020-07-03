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
}
