<!doctype html>
<html data-auth="{{ auth()->check() ? '1' : '0' }}" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AutoTrust' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-white text-slate-900 antialiased " x-data >
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>
    @include('partials.footer')
    @include('partials.auth-modal')

    @php($openAuth = session()->pull('open_auth_modal'))
    @if ($openAuth)
    <script>
    window.addEventListener('DOMContentLoaded', () => {
        const mode = {!! json_encode($openAuth) !!};
        if (window.Alpine && Alpine.store) Alpine.store('authModal').open(mode);
    });
    </script>
    @endif
    
<script>
document.addEventListener('click', async (e) => {
    // support dua jenis tombol: mobil & motor
    const mobilBtn = e.target.closest('[data-like-btn]');
    const motorBtn = e.target.closest('[data-motor-like-btn]');
    const btn = mobilBtn || motorBtn;
    if (!btn) return;

    const type = mobilBtn ? 'mobil' : 'motor';
    const id = mobilBtn ? btn.dataset.mobilId : btn.dataset.motorId;
    if (!id) return; // guard

    const isLoggedIn = document.documentElement.dataset.auth === '1';
    if (!isLoggedIn) {
        window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: { tab: 'login' } }));
        return;
    }

    try {
        const res = await fetch(`/${type}/${id}/like-toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        if (res.status === 401) {
            window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: { tab: 'login' } }));
            return;
        }

        const data = await res.json();

        // update heart untuk semua button yang id-nya sama (card list + detail)
        const btnSelector = (type === 'mobil')
            ? `[data-like-btn="${CSS.escape(id)}"]`
            : `[data-motor-like-btn="${CSS.escape(id)}"]`;

        document.querySelectorAll(btnSelector).forEach((b) => {
            const icon = b.querySelector('i');
            if (!icon) return;

            if (data.liked) {
                icon.classList.add('text-red-500');
                icon.classList.remove('text-slate-400');
            } else {
                icon.classList.remove('text-red-500');
                icon.classList.add('text-slate-400');
            }
        });

        // OPTIONAL: update angka like kalau backend mengembalikan { likes: ... }
        const countSelector = (type === 'mobil')
            ? `[data-like-count="${CSS.escape(id)}"]`
            : `[data-motor-like-count="${CSS.escape(id)}"]`;

        document.querySelectorAll(countSelector).forEach((el) => {
            if (typeof data.likes !== 'undefined') el.textContent = data.likes;
        });

    } catch (err) {
        console.error(err);
    }
});
</script>



</body>
</html>
