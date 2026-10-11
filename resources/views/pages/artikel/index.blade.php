@extends('layouts.main')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 py-10">

    {{-- TOP SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-14 items-stretch">

        {{-- LEFT CARD --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
            <img src="{{ asset($articles[0]['image']) }}"
                class="w-full h-[380px] object-cover">

            <div class="p-6 flex flex-col flex-1">
                <h2 class="text-xl font-semibold mb-2">
                    {{ $articles[0]['title'] }}
                </h2>

                <p class="text-gray-600 mb-4 flex-1">
                    {{ $articles[0]['excerpt'] }}
                </p>

                <a href="{{ route('artikel.show', $articles[0]['slug']) }}"
                class="text-[#51A3EE] font-medium">
                    Read more →
                </a>
            </div>
        </div>

        {{-- RIGHT 3 CARDS (SAME TOTAL HEIGHT AS LEFT) --}}
        <div class="grid grid-rows-3 gap-4 h-full">
            @foreach(array_slice($articles, 1, 3) as $item)
            <div class="bg-white rounded-xl shadow p-5 flex flex-col justify-between">
                <div>
                    <h3 class="font-semibold mb-2 text-lg leading-snug">
                        {{ $item['title'] }}
                    </h3>

                    <p class="text-gray-600 text-base line-clamp-3">
                        {{ $item['excerpt'] }}
                    </p>
                </div>

                <a href="{{ route('artikel.show', $item['slug']) }}"
                class="text-[#51A3EE] text-base font-medium mt-4">
                    Read more →
                </a>
            </div>
            @endforeach
        </div>

    </div>



    {{-- RELATED ARTICLE --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Related Article</h2>
        <a href="#" class="bg-[#51A3EE] text-white px-4 py-2 rounded-full text-base">
            See More →
        </a>
    </div>

    <p class="text-gray-500 mb-8 max-w-xl">
        Lorem ipsum dolor sit amet consectetur. Nisl viverra ut neque fusce dolor.
    </p>

    @php
        $relatedImages = [
            'assets/img/artikel/artikel-1.png',
            'assets/img/artikel/artikel-2.png',
            'assets/img/artikel/artikel-3.png',
            'assets/img/artikel/artikel-4.png',
            'assets/img/artikel/artikel-5.png',
            'assets/img/artikel/artikel-6.png',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($relatedImages as $image)
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <img src="{{ asset($image) }}"
                class="h-48 w-full object-cover">

            <div class="p-4">
                <h3 class="font-semibold mb-2 text-lg line-clamp-2">
                    Lorem ipsum mauris cursus purus auctor in
                </h3>

                <p class="text-gray-600 text-base mb-3">
                    Lorem ipsum mauris cursus purus auctor in pellentesque ut tempor elit.
                </p>

                <a href="{{ route('artikel.show', 'artikel-1') }}" class="text-[#51A3EE] text-base font-medium">
                    Read more →
                </a>
            </div>
        </div>
        @endforeach
    </div>


</div>
@endsection
