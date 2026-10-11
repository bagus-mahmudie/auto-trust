<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    /**
     * Data ringkas (title, excerpt, image, cover, slug).
     * Konten panjangnya ada di resources/views/pages/artikel/content/{slug}.blade.php
     */
    private function artikelData(): array
    {
        return [
            [
                'title' => 'Lorem ipsum mauris cursus purus auctor in',
                'excerpt' => 'Lorem ipsum dolor sit amet consectetur. Nulla sapien diam elit pharetra. Nam id est gravida.',
                'image' => 'assets/img/artikel/hero-article.png',
                'cover' => 'assets/img/artikel/hero-article.png', // cover detail (ubah sesuai asset kamu)
                'slug' => 'artikel-1'
            ],
            [
                'title' => 'Lorem ipsum mauris cursus purus auctor in',
                'excerpt' => 'Lorem ipsum dolor sit amet consectetur. Nulla sapien diam elit pharetra.',
                'image' => 'assets/img/artikel/artikel-1.png',
                'cover' => 'assets/img/artikel/artikel-1.png',
                'slug' => 'artikel-2'
            ],
            [
                'title' => 'Lorem ipsum mauris cursus purus auctor in',
                'excerpt' => 'Lorem ipsum dolor sit amet consectetur. Nulla sapien diam elit pharetra.',
                'image' => 'assets/img/artikel/artikel-2.png',
                'cover' => 'assets/img/artikel/artikel-2.png',
                'slug' => 'artikel-3'
            ],
            [
                'title' => 'Lorem ipsum mauris cursus purus auctor in',
                'excerpt' => 'Lorem ipsum dolor sit amet consectetur. Nulla sapien diam elit pharetra.',
                'image' => 'assets/img/artikel/artikel-4.png',
                'cover' => 'assets/img/artikel/artikel-4.png',
                'slug' => 'artikel-4'
            ],
        ];
    }

    public function index()
    {
        $articles = $this->artikelData();
        return view('pages.artikel.index', compact('articles'));
    }

    public function show($slug)
    {
        $articles = $this->artikelData();

        $article = collect($articles)->firstWhere('slug', $slug);
        abort_if(!$article, 404);

        // File konten per artikel: resources/views/pages/artikel/content/{slug}.blade.php
        $contentView = "pages.artikel.content.$slug";
        abort_unless(view()->exists($contentView), 404);

        // Related thumbnails (sesuai yang kamu pakai di index)
        $relatedImages = [
            'assets/img/artikel/artikel-1.png',
            'assets/img/artikel/artikel-2.png',
            'assets/img/artikel/artikel-3.png',
            'assets/img/artikel/artikel-4.png',
            'assets/img/artikel/artikel-5.png',
            'assets/img/artikel/artikel-6.png',
        ];

        // Related cards (slug dibuat muter dari artikel yang ada)
        $slugs = collect($articles)->pluck('slug')->values()->all();

        $related = collect($relatedImages)->values()->map(function ($img, $i) use ($slugs) {
            return [
                'title' => 'Lorem ipsum mauris cursus purus auctor in...',
                'excerpt' => 'Lorem ipsum mauris cursus purus auctor in pellentesque ut tempor elit...',
                'image' => $img,
                'slug' => $slugs[$i % count($slugs)],
            ];
        })->all();

        return view('pages.artikel.show', compact('article', 'contentView', 'related'));
    }
}
