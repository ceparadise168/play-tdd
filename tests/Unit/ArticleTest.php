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
