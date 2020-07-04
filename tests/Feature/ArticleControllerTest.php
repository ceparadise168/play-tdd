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
