<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $me = (int) $request->user()->id;

        // Last message per pasangan (me <-> user lain)
        $pairs = Message::query()
            ->selectRaw('LEAST(sender_id, receiver_id) as u1, GREATEST(sender_id, receiver_id) as u2, MAX(id) as last_id')
            ->where(function ($q) use ($me) {
                $q->where('sender_id', $me)->orWhere('receiver_id', $me);
            })
            ->groupBy('u1', 'u2');

        $lastMessages = Message::query()
            ->joinSub($pairs, 'pairs', function ($join) {
                $join->on('messages.id', '=', 'pairs.last_id');
            })
            ->orderByDesc('messages.id')
            ->get(['messages.*']);

        $otherIds = $lastMessages
            ->map(fn ($m) => $m->sender_id === $me ? $m->receiver_id : $m->sender_id)
            ->unique()
            ->values();

        $users = User::query()
            ->whereIn('id', $otherIds)
            ->get()
            ->keyBy('id');

        // unread count per user
        $unread = Message::query()
            ->selectRaw('sender_id, COUNT(*) as cnt')
            ->where('receiver_id', $me)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('cnt', 'sender_id');

        $threads = $lastMessages->map(function ($m) use ($me, $users, $unread) {
            $otherId = $m->sender_id === $me ? $m->receiver_id : $m->sender_id;
            $u = $users->get($otherId);
            if (!$u) return null;

            return [
                'user' => $u,
                'last' => $m,
                'unread' => (int) ($unread[$otherId] ?? 0),
            ];
        })->filter()->values();

        $activeUser = null;
        if ($request->filled('user')) {
            $activeUser = User::query()->whereKey($request->integer('user'))->first();
        }

        $messages = collect();
        if ($activeUser && $activeUser->id !== $me) {
            $messages = Message::between($me, $activeUser->id)
                ->orderBy('id')
                ->limit(200)
                ->get();

            // mark read
            Message::query()
                ->where('sender_id', $activeUser->id)
                ->where('receiver_id', $me)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('chat.index', [
            'threads' => $threads,
            'activeUser' => $activeUser,
            'messages' => $messages,
        ]);
    }

    public function show(User $user)
    {
        return redirect()->route('chat.index', ['user' => $user->id]);
    }

    /**
     * JSON Threads untuk sidebar (agar sidebar bisa update tanpa reload)
     */
    public function threads(Request $request)
    {
        $me = (int) $request->user()->id;

        $pairs = Message::query()
            ->selectRaw('LEAST(sender_id, receiver_id) as u1, GREATEST(sender_id, receiver_id) as u2, MAX(id) as last_id')
            ->where(function ($q) use ($me) {
                $q->where('sender_id', $me)->orWhere('receiver_id', $me);
            })
            ->groupBy('u1', 'u2');

        $lastMessages = Message::query()
            ->joinSub($pairs, 'pairs', function ($join) {
                $join->on('messages.id', '=', 'pairs.last_id');
            })
            ->orderByDesc('messages.id')
            ->get(['messages.*']);

        $otherIds = $lastMessages
            ->map(fn ($m) => $m->sender_id === $me ? $m->receiver_id : $m->sender_id)
            ->unique()
            ->values();

        $users = User::query()->whereIn('id', $otherIds)->get()->keyBy('id');

        $unread = Message::query()
            ->selectRaw('sender_id, COUNT(*) as cnt')
            ->where('receiver_id', $me)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('cnt', 'sender_id');

        $threads = $lastMessages->map(function ($m) use ($me, $users, $unread) {
            $otherId = $m->sender_id === $me ? $m->receiver_id : $m->sender_id;
            $u = $users->get($otherId);
            if (!$u) return null;

            $avatar = $u->foto_profile
                ? asset('storage/' . $u->foto_profile)
                : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=E2E8F0&color=0F172A';

            return [
                'user' => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'avatar' => $avatar,
                ],
                'last' => [
                    'id' => $m->id,
                    'body' => $m->body,
                    'created_at' => optional($m->created_at)->toISOString(),
                ],
                'unread' => (int) ($unread[$otherId] ?? 0),
            ];
        })->filter()->values();

        return response()->json([
            'threads' => $threads,
        ]);
    }

    public function fetch(User $user, Request $request)
    {
        $me = (int) $request->user()->id;
        abort_if($user->id === $me, 404);

        $afterId = $request->integer('after_id');
        $afterUpdatedAt = $request->string('after_updated_at');

        $q = Message::between($me, $user->id);

        // Ambil pesan baru ATAU pesan yang berubah (misal read_at diupdate)
        if ($afterId || $afterUpdatedAt) {
            $q->where(function ($qq) use ($afterId, $afterUpdatedAt) {
                if ($afterId) $qq->where('id', '>', $afterId);
                if ($afterUpdatedAt) $qq->orWhere('updated_at', '>', $afterUpdatedAt);
            });
        }

        $messages = $q->orderBy('id')->limit(300)->get();

        // mark read untuk pesan masuk ke saya
        Message::query()
            ->where('sender_id', $user->id)
            ->where('receiver_id', $me)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $maxUpdatedAt = $messages->max('updated_at');

        return response()->json([
            'messages' => $messages,
            'max_updated_at' => $maxUpdatedAt?->toISOString(),
        ]);
    }

    public function send(User $user, Request $request)
    {
        $me = (int) $request->user()->id;
        abort_if($user->id === $me, 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $msg = Message::create([
            'sender_id' => $me,
            'receiver_id' => $user->id,
            'body' => trim($data['body']),
        ]);

        return response()->json([
            'message' => $msg,
        ]);
    }
}
