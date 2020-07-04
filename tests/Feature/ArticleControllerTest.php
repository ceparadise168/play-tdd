<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Session;
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

    public function testCreateArticleCSRFFailed()
    {
        $this->app['env'] = 'production';

        $parameters = [];

        $response = $this->post('article', $parameters);

        $response->assertStatus(419);
    }

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
}
