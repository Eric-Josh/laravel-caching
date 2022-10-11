<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Article;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 1;

        if($request->has('page')) {
            $page = $request->query('page');
        }

        $key = 'articles_'.$page;
        $tag = 'articles'; // append user id for real app

        return Cache::tags($tag)->remember($key, now()->addMinutes(10), function() {
            return Article::orderBy('id','desc')->paginate(5);
        });
    }

    // Returns all 500 without Caching 
    public function allWithoutCache()
    {
        return Article::orderBy('id','desc')->paginate(5);
    }

    // other cache way
    public function otherCacheWay(Request $request)
    {
        $page = 1;

        if($request->has('page')) {
            $page = $request->query('page');
        }

        $key = 'articles_'.$page;

        $articles = Cache::get($key);

        if(!$articles) {
            $articles = Article::orderBy('id','desc')->paginate(5);

            Cache::put($key, $articles, now()->addMinutes(10));

            print_r('cached missed');
        }else {
            print_r('cached hit');
        }

        return $articles;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $article = Article::create([
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $key = 'articles_1';
        $tag = 'articles'; // append user id for real app

        Cache::tags($tag)->flush();

        Cache::tags($tag)->remember($key, now()->addMinutes(10), function() {
            return Article::orderBy('id','desc')->paginate(5);
        });

        return response([
            'article' => $article,
            'message' => 'Article added!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
