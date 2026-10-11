@extends('layouts.main')

@section('content')
@php
    $me = auth()->id();
    $activeId = $activeUser?->id;

    $myUser = auth()->user();
    $avatarMe = $myUser->foto_profile
        ? asset('storage/'.$myUser->foto_profile)
        : 'https://ui-avatars.com/api/?name='.urlencode($myUser->name).'&background=E2E8F0&color=0F172A';

    $lastReadMineId = $messages
        ->where('sender_id', $me)
        ->whereNotNull('read_at')
        ->pluck('id')
        ->last();
@endphp

<div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-6">
    <div class="overflow-hidden rounded-[28px] bg-white shadow-[0_18px_70px_rgba(2,6,23,0.10)] ring-1 ring-slate-100">
        <div class="h-[78vh] min-h-[560px] lg:flex">

            {{-- LEFT --}}
            <aside class="lg:w-[420px] border-r border-slate-100 flex flex-col min-h-0">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                    <img src="{{ $avatarMe }}" class="h-11 w-11 rounded-full object-cover" alt="me">

                    <div class="flex-1">
                        <div class="relative">
                            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" placeholder="Message"
                                   class="w-full rounded-2xl bg-slate-50 border border-slate-100 pl-11 pr-4 py-3 text-sm outline-none focus:bg-white focus:border-sky-300 focus:ring-2 focus:ring-sky-100" />
                        </div>
                    </div>

                    <button class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white ring-1 ring-slate-200 hover:bg-slate-50">
                        <i class="bi bi-filter text-slate-600"></i>
                    </button>
                </div>

                {{-- THREADS BOX (penting: ada id + data url agar bisa update tanpa reload) --}}
                <div id="chatThreads"
                     class="flex-1 overflow-y-auto min-h-0 px-2 py-2"
                     data-threads-url="{{ route('chat.threads') }}"
                     data-active-id="{{ $activeId ?? '' }}">

                    @if($threads->isEmpty())
                        <div class="px-4 py-8 text-center">
                            <div class="text-sm font-semibold text-slate-900">Belum ada chat</div>
                            <div class="mt-1 text-xs text-slate-500">
                                Chat akan muncul setelah kamu mengirim pesan ke penjual.
                            </div>
                        </div>
                    @else
                        @foreach($threads as $t)
                            @php
                                $tu = $t['user'];
                                $last = $t['last'];
                                $unread = (int) $t['unread'];

                                $avatar = $tu->foto_profile
                                    ? asset('storage/'.$tu->foto_profile)
                                    : 'https://ui-avatars.com/api/?name='.urlencode($tu->name).'&background=E2E8F0&color=0F172A';

                                $snippet = $last ? \Illuminate\Support\Str::limit($last->body, 44) : 'Mulai chat…';
                                $time = $last ? $last->created_at->format('H.i') : '';
                                $active = $activeId === $tu->id;
                            @endphp

                            <a href="{{ route('chat.index', ['user' => $tu->id]) }}"
                               class="group flex items-center gap-3 rounded-2xl px-4 py-4 hover:bg-slate-50 {{ $active ? 'bg-slate-50' : '' }}">
                                <img src="{{ $avatar }}" class="h-11 w-11 rounded-full object-cover" alt="avatar">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="truncate text-sm font-semibold text-slate-900">{{ $tu->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $time }}</div>
                                    </div>
                                    <div class="mt-0.5 flex items-center justify-between gap-2">
                                        <div class="truncate text-xs text-slate-500">{{ $snippet }}</div>
                                        @if($unread > 0)
                                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-sky-500 px-1.5 text-[11px] font-semibold text-white">
                                                {{ $unread }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <div class="mx-4 h-px bg-slate-100"></div>
                        @endforeach
                    @endif
                </div>
            </aside>

            {{-- RIGHT --}}
            <section class="flex-1 flex flex-col min-h-0">
                @if($activeUser)
                    @php
                        $avatarActive = $activeUser->foto_profile
                            ? asset('storage/'.$activeUser->foto_profile)
                            : 'https://ui-avatars.com/api/?name='.urlencode($activeUser->name).'&background=E2E8F0&color=0F172A';
                    @endphp

                    <div class="px-6 py-4 flex items-center gap-3 border-b border-slate-100">
                        <img src="{{ $avatarActive }}" class="h-10 w-10 rounded-full object-cover" alt="avatar">
                        <div class="min-w-0">
                            <div class="text-base font-semibold text-slate-900">{{ $activeUser->name }}</div>
                            <div class="text-xs text-slate-500">{{ $activeUser->email }}</div>
                        </div>

                        <div class="ml-auto flex items-center gap-3">
                            <button class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100" title="Flag">
                                <i class="bi bi-flag text-slate-600"></i>
                            </button>
                            <button class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100" title="Call">
                                <i class="bi bi-telephone text-slate-600"></i>
                            </button>
                            <a href="{{ route('chat.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100" title="Close">
                                <i class="bi bi-x-lg text-slate-600"></i>
                            </a>
                        </div>
                    </div>

                    <div id="chatMessages"
                         class="flex-1 overflow-y-auto min-h-0 px-6 py-6"
                         data-fetch-url="{{ route('chat.messages', $activeUser) }}"
                         data-send-url="{{ route('chat.send', $activeUser) }}">

                        @foreach($messages as $m)
                            @php
                                $isMe = $m->sender_id === $me;
                                $time = $m->created_at?->format('H.i');
                            @endphp

                            <div class="mb-4 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%]">
                                    <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm
                                        {{ $isMe ? 'bg-[#51A3EE] text-white rounded-br-[10px]' : 'bg-slate-100 text-slate-700 rounded-bl-[10px]' }}"
                                        data-msg-id="{{ $m->id }}">
                                        {{ $m->body }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400 {{ $isMe ? 'text-right' : 'text-left' }}" data-meta-for="{{ $m->id }}">
                                        {{ $time }}
                                        @if($isMe && $m->read_at && $m->id == $lastReadMineId)
                                            <span class="ml-2">Dilihat</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-3 border-t border-slate-100">
                        <div class="flex items-center gap-4 rounded-2xl bg-slate-50 px-4 py-1 ring-1 ring-slate-100">
                            <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-white ring-1 ring-slate-200 hover:bg-slate-50">
                                <i class="bi bi-plus text-slate-700"></i>
                            </button>

                            <form id="chatForm" class="flex flex-1 items-center gap-4">
                                <input id="chatInput" type="text" placeholder="Type a message"
                                       class="h-11 w-full rounded-2xl border border-transparent bg-transparent px-2 text-sm outline-none focus:bg-white focus:border-sky-300 focus:ring-2 focus:ring-sky-100" />
                                <button type="submit"
                                        class="inline-flex h-8 w-8 text-sm items-center justify-center rounded-2xl bg-[#51A3EE] text-white hover:bg-sky-600"
                                        aria-label="Send">
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex flex-1 items-center justify-center p-10 text-center">
                        <div>
                            <div class="text-lg font-semibold text-slate-900">Pilih chat</div>
                            <div class="mt-1 text-sm text-slate-500">Klik nama di sebelah kiri untuk mulai percakapan.</div>
                        </div>
                    </div>
                @endif
            </section>

        </div>
    </div>
</div>

<script>
(function initChat() {
  const box = document.getElementById('chatMessages');
  const form = document.getElementById('chatForm');
  const input = document.getElementById('chatInput');

  const threadsBox = document.getElementById('chatThreads');
  const threadsUrl = threadsBox ? threadsBox.dataset.threadsUrl : null;

  // kalau belum pilih user, kanan kosong (tidak perlu init chat messages polling)
  if (!box || !form || !input) {
    // tetap refresh threads berkala agar unread/update bisa jalan
    if (threadsUrl) setInterval(async () => {
      try {
        const res = await fetch(threadsUrl, { headers: { 'Accept':'application/json' }});
        if (!res.ok) return;
        const data = await res.json();
        renderThreads(data.threads || []);
      } catch(e) {}
    }, 7000);
    return;
  }

  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const fetchUrl = box.dataset.fetchUrl;
  const sendUrl = box.dataset.sendUrl;

  let lastUpdatedAt = null;
  let lastReadMineId = {{ (int)($lastReadMineId ?? 0) }};

  const scrollBottom = () => { box.scrollTop = box.scrollHeight; };
  const isNearBottom = () => (box.scrollHeight - box.scrollTop - box.clientHeight) < 80;

  const getLastId = () => {
    const all = box.querySelectorAll('[data-msg-id]');
    const last = all.length ? all[all.length - 1] : null;
    return last ? parseInt(last.dataset.msgId, 10) : 0;
  };

  const escapeHtml = (s) => (s || '')
    .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');

  const fmtHHmm = (iso) => {
    if (!iso) return '';
    const d = new Date(iso);
    const hh = String(d.getHours()).padStart(2,'0');
    const mm = String(d.getMinutes()).padStart(2,'0');
    return `${hh}.${mm}`;
  };

  const renderThreads = (threads) => {
    if (!threadsBox) return;

    const activeId = threadsBox.dataset.activeId ? parseInt(threadsBox.dataset.activeId, 10) : null;

    if (!threads || threads.length === 0) {
      threadsBox.innerHTML = `
        <div class="px-4 py-8 text-center">
          <div class="text-sm font-semibold text-slate-900">Belum ada chat</div>
          <div class="mt-1 text-xs text-slate-500">
            Chat akan muncul setelah kamu mengirim pesan ke penjual.
          </div>
        </div>
      `;
      return;
    }

    threadsBox.innerHTML = threads.map(t => {
      const u = t.user;
      const last = t.last || {};
      const unread = t.unread || 0;
      const time = fmtHHmm(last.created_at);
      const snippet = last.body ? last.body : 'Mulai chat…';
      const isActive = activeId && (parseInt(u.id,10) === activeId);

      return `
        <a href="/chat?user=${u.id}"
           class="group flex items-center gap-3 rounded-2xl px-4 py-4 hover:bg-slate-50 ${isActive ? 'bg-slate-50' : ''}">
          <img src="${u.avatar}" class="h-11 w-11 rounded-full object-cover" alt="avatar">
          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
              <div class="truncate text-sm font-semibold text-slate-900">${escapeHtml(u.name)}</div>
              <div class="text-xs text-slate-500">${time}</div>
            </div>
            <div class="mt-0.5 flex items-center justify-between gap-2">
              <div class="truncate text-xs text-slate-500">${escapeHtml(snippet).slice(0, 60)}</div>
              ${unread > 0 ? `<span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-sky-500 px-1.5 text-[11px] font-semibold text-white">${unread}</span>` : ``}
            </div>
          </div>
        </a>
        <div class="mx-4 h-px bg-slate-100"></div>
      `;
    }).join('');
  };

  const refreshThreads = async () => {
    if (!threadsUrl) return;
    try {
      const res = await fetch(threadsUrl, { headers: { 'Accept': 'application/json' }});
      if (!res.ok) return;
      const data = await res.json();
      renderThreads(data.threads || []);
    } catch (e) {}
  };

  const refreshSeenLabel = () => {
    box.querySelectorAll('[data-meta-for]').forEach(el => {
      el.innerHTML = el.innerHTML.replace(/<span class="ml-2">Dilihat<\/span>/g, '');
    });

    if (!lastReadMineId) return;

    const meta = box.querySelector('[data-meta-for="'+lastReadMineId+'"]');
    if (meta && !meta.innerHTML.includes('Dilihat')) {
      meta.innerHTML += ' <span class="ml-2">Dilihat</span>';
    }
  };

  const render = (m) => {
    const isMe = parseInt(m.sender_id, 10) === {{ (int)auth()->id() }};
    const time = new Date(m.created_at || Date.now());
    const hh = String(time.getHours()).padStart(2,'0');
    const mm = String(time.getMinutes()).padStart(2,'0');

    const wrap = document.createElement('div');
    wrap.className = 'mb-4 flex ' + (isMe ? 'justify-end' : 'justify-start');
    wrap.innerHTML = `
      <div class="max-w-[70%]">
        <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm ${isMe ? 'bg-[#51A3EE] text-white rounded-br-[10px]' : 'bg-slate-100 text-slate-700 rounded-bl-[10px]'}" data-msg-id="${m.id}">
          ${escapeHtml(m.body)}
        </div>
        <div class="mt-1 text-xs text-slate-400 ${isMe ? 'text-right' : 'text-left'}" data-meta-for="${m.id}">
          ${hh}.${mm}
        </div>
      </div>
    `;
    box.appendChild(wrap);
  };

  const fetchNew = async () => {
    try {
      const shouldStickBottom = isNearBottom();

      const after = getLastId();
      const url = new URL(fetchUrl, window.location.origin);
      if (after) url.searchParams.set('after_id', after);
      if (lastUpdatedAt) url.searchParams.set('after_updated_at', lastUpdatedAt);

      const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
      if (!res.ok) return;

      const data = await res.json();
      const msgs = data.messages || [];

      msgs.forEach((m) => {
        const exists = box.querySelector('[data-msg-id="'+m.id+'"]');

        if (!exists) {
          render(m);
        }

        // update lastReadMineId
        const isMe = parseInt(m.sender_id, 10) === {{ (int)auth()->id() }};
        if (isMe && m.read_at) {
          lastReadMineId = Math.max(lastReadMineId, parseInt(m.id, 10));
        }

        // reset time (label "Dilihat" di-handle refreshSeenLabel)
        const meta = box.querySelector('[data-meta-for="'+m.id+'"]');
        if (meta) {
          const dt = new Date(m.created_at || Date.now());
          const hh = String(dt.getHours()).padStart(2,'0');
          const mm = String(dt.getMinutes()).padStart(2,'0');
          meta.innerHTML = `${hh}.${mm}`;
        }
      });

      if (data.max_updated_at) lastUpdatedAt = data.max_updated_at;

      refreshSeenLabel();
      if (msgs.length && shouldStickBottom) scrollBottom();

      // update sidebar: last msg/jam/unread
      await refreshThreads();

    } catch (e) {}
  };

  const send = async (text) => {
    const body = (text || '').trim();
    if (!body) return;

    const tempId = Date.now();

    render({ id: tempId, sender_id: {{ (int)auth()->id() }}, body, created_at: new Date().toISOString(), read_at: null });
    refreshSeenLabel();
    scrollBottom();

    try {
      const res = await fetch(sendUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ body })
      });

      if (!res.ok) throw new Error('send failed');
      const data = await res.json();

      const lastBubble = box.querySelector('[data-msg-id="'+tempId+'"]');
      if (lastBubble) lastBubble.dataset.msgId = String(data.message.id);

      // INI yang bikin sidebar langsung muncul setelah pesan pertama (tanpa reload)
      await refreshThreads();

    } catch (e) {}
  };

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const text = input.value;
    input.value = '';
    send(text);
  });

  // initial
  scrollBottom();
  refreshSeenLabel();
  refreshThreads();

  const t1 = setInterval(fetchNew, 2500);
  const t2 = setInterval(refreshThreads, 7000);

  window.addEventListener('beforeunload', () => { clearInterval(t1); clearInterval(t2); });
})();
</script>
@endsection
