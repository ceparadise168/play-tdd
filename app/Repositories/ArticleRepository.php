<?php
namespace App\Repositories;

use App\Models\Article;

class ArticleRepository
{
    public function latest10()
    {
        return Article::query()->orderBy('id', 'desc')->limit(10)->get();
    }

    public function create($attributes)
    {
        return Article::create($attributes);
    }
}
