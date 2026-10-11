@extends('layouts.main')

@section('content')
<div class="max-w-[980px] mx-auto px-4 pt-14 pb-8">

    {{-- TITLE CENTER --}}
    <h1 class="text-center text-2xl sm:text-3xl font-semibold leading-snug mb-10">
        {{ $article['title'] }}.
    </h1>

    {{-- BIG COVER IMAGE --}}
    <div class="rounded-2xl overflow-hidden shadow mb-10">
        <img src="{{ asset($article['cover'] ?? $article['image']) }}"
             class="w-full h-[220px] sm:h-[320px] md:h-[360px] object-cover"
             alt="{{ $article['title'] }}">
    </div>

    {{-- CONTENT (CUSTOM PER ARTICLE) --}}
    <div class="space-y-7 text-gray-600 leading-relaxed text-sm sm:text-base">
        @include($contentView)
    </div>

</div>

{{-- RELATED ARTICLE SECTION --}}
<div class="max-w-[1200px] mx-auto px-4 pb-16 pt-10">

    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Related Article</h2>
            <p class="text-gray-500 mt-2 max-w-xl">
                Lorem ipsum dolor sit amet consectetur. Nisl viverra ut neque fusce dolor. Amet aliquet .
            </p>
        </div>

        <a href="{{ route('artikel.index') }}"
           class="bg-[#51A3EE] text-white px-5 py-2 rounded-full text-base flex items-center gap-2">
            See More <span>→</span>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        @foreach($related as $item)
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <img src="{{ asset($item['image']) }}"
                 class="h-48 w-full object-cover"
                 alt="{{ $item['title'] }}">

            <div class="p-4">
                <h3 class="font-semibold mb-2 text-lg line-clamp-2 text-gray-900">
                    {{ $item['title'] }}
                </h3>

                <p class="text-gray-600 text-base mb-3 line-clamp-2">
                    {{ $item['excerpt'] }}
                </p>

                <a href="{{ route('artikel.show', $item['slug']) }}"
                   class="text-[#51A3EE] text-base font-medium">
                    Read more →
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
