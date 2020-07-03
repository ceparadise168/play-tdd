# laravel-tdd

#### Laravel installer
```
composer global require laravel/installer
```

#### Via Composer Create-Project
```
composer create-project --prefer-dist laravel/laravel blog
```

```
cepar@DESKTOP-H3LUMQB MINGW64 ~/Desktop/projects/play-tdd
$ composer create-project --prefer-dist laravel/laravel blog
Creating a "laravel/laravel" project at "./blog"
Installing laravel/laravel (v7.12.0)
  - Installing laravel/laravel (v7.12.0): Loading from cache
Created project in C:\Users\cepar\Desktop\projects\play-tdd\blog
> @php -r "file_exists('.env') || copy('.env.example', '.env');"
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 97 installs, 0 updates, 0 removals
  - Installing voku/portable-ascii (1.5.2): Loading from cache
  - Installing symfony/polyfill-ctype (v1.17.1): Loading from cache
  - Installing phpoption/phpoption (1.7.4): Loading from cache
  - Installing vlucas/phpdotenv (v4.1.7): Loading from cache
  - Installing symfony/css-selector (v5.1.2): Loading from cache

...
```


--prefer-source ? --prefer-dist ?

--prefer-source: There are two ways of downloading a package: source and dist. For stable versions Composer will use the dist by default. The source is a version control repository. If --prefer-source is enabled, Composer will install from source if there is one. This is useful if you want to make a bugfix to a project and get a local git clone of the dependency directly.

--prefer-dist: Reverse of --prefer-source, Composer will install from dist if possible. This can speed up installs substantially on build servers and other use cases where you typically do not run updates of the vendors. It is also a way to circumvent problems with git if you do not have a proper setup.

--prefer-dist 直接抓該套件的 distribution 版本且緩存，下次安裝就會直接從本機緩存安裝，安裝速度快，但抓下來的版本沒有保留 .git，所以通常使用 dist 後不會再去更新 vendors，另外也可以避免環境中沒有 git 的問題。

https://getcomposer.org/doc/03-cli.md#install



```
$ php artisan serve
Laravel development server started: http://127.0.0.1:8000
[Fri Jul  3 13:03:35 2020] 127.0.0.1:52275 [200]: /favicon.ico
```