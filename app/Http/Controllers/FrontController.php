<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAdvertisement;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $articles = ArticleNews::with(['category'])
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        $featured_articles = ArticleNews::with(['category'])
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->take(6)
            ->get();

        $entertainment_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertainment');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        $entertainment_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertainment');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        $business_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        $business_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        $automotive_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        $automotive_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        $authors = Author::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        return view('front.index', compact('categories', 'articles', 'authors', 'featured_articles', 'bannerads', 'entertainment_articles', 'entertainment_featured_articles', 'business_featured_articles', 'business_articles', 'automotive_featured_articles', 'automotive_articles'));
    }

    public function category(Category $category)
    {
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();
        return view('front.category', compact('category', 'categories', 'bannerads'));
    }

    public function author(Author $author)
    {
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();
        return view('front.author', compact('categories', 'author', 'bannerads'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
        ]);

        $categories = Category::all();
        $keyword = $request->keyword;

        $articles = ArticleNews::with(['category', 'author'])
            ->where('name', 'like', '%' . $keyword . '%')
            ->paginate(6);

        return view('front.search', compact('articles', 'keyword', 'categories'));
    }

    public function details($slug)
    {
        $articleNews = ArticleNews::where('slug', $slug)->firstOrFail();
        $categories = Category::all();
        $articles = ArticleNews::with(['category'])
            ->where('is_featured', 'not_featured')
            ->where('id', '!=', $articleNews->id)
            ->latest()
            ->take(3)
            ->get();
            $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();
            $squareBannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'square')
            ->inRandomOrder()
            ->take(2)
            ->get();
            if($squareBannerads->count() < 2)
            {
                $squareBannerads_1 = $squareBannerads->first();
                $squareBannerads_2 = null;
            }else
            {
                $squareBannerads_1 = $squareBannerads->get(0);
                $squareBannerads_2 = $squareBannerads->get(1);
            }
            $author_news = ArticleNews::where('author_id', $articleNews->author_id)
    ->where('id', '!=', $articleNews->id)
    ->inRandomOrder()
    ->get();

        return view('front.details', compact('articleNews', 'categories', 'articles', 'bannerads', 'squareBannerads', 'squareBannerads_1', 'squareBannerads_2', 'author_news'));
    }
}
