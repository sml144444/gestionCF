<?php

namespace App\Http\Controllers;

use App\Models\NewsEvent;
use App\Models\NewsComment;
use App\Models\NewsLike;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index()
    {
        $this->authorize('news-list');

        $news = NewsEvent::with(['auteur', 'comments', 'likes'])
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('news.index', compact('news'));
    }

    // ── CREATE ────────────────────────────────────────────────
    public function create()
    {
        $this->authorize('news-create');
        return view('news.create');
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('news-create');

        $validated = $request->validate([
            'titre'   => 'required|string|max:255',
            'contenu' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }

        NewsEvent::create([
            'id_user' => Auth::id(),
            'titre'   => $validated['titre'],
            'contenu' => $validated['contenu'],
            'image'   => $imagePath,
        ]);

        return redirect()->route('news.index')
            ->with('success', 'Publication créée avec succès !');
    }

    // ── SHOW ──────────────────────────────────────────────────
    public function show(NewsEvent $news)
    {
        $this->authorize('news-list');

        $news->load(['auteur', 'comments.auteur', 'likes']);
        $liked = $news->isLikedBy(Auth::user());

        return view('news.show', compact('news', 'liked'));
    }

    // ── EDIT ──────────────────────────────────────────────────
    public function edit(NewsEvent $news)
    {
        $this->authorize('news-edit');

        // Seul l'auteur ou l'admin peut modifier
        if (Auth::id() !== $news->id_user && Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        return view('news.edit', compact('news'));
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, NewsEvent $news): RedirectResponse
    {
        $this->authorize('news-edit');

        if (Auth::id() !== $news->id_user && Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'titre'         => 'required|string|max:255',
            'contenu'       => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'remove_image'  => 'nullable|boolean',
        ]);

        $imagePath = $news->image;

        if ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $imagePath = $request->file('image')->store('news', 'public');
        }

        $news->update([
            'titre'   => $validated['titre'],
            'contenu' => $validated['contenu'],
            'image'   => $imagePath,
        ]);

        return redirect()->route('news.show', $news)
            ->with('success', 'Publication mise à jour avec succès !');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(NewsEvent $news): RedirectResponse
    {
        $this->authorize('news-delete');

        if (Auth::id() !== $news->id_user && Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('news.index')
            ->with('success', 'Publication supprimée.');
    }

    // ── COMMENT STORE ─────────────────────────────────────────
    public function storeComment(Request $request, NewsEvent $news): RedirectResponse
    {
        $this->authorize('news-comment');

        $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        NewsComment::create([
            'news_event_id' => $news->id,
            'user_id'       => Auth::id(),
            'contenu'       => $request->contenu,
        ]);

        return back()->with('success', 'Commentaire ajouté.');
    }

    // ── COMMENT DESTROY ───────────────────────────────────────
    public function destroyComment(NewsEvent $news, NewsComment $comment): RedirectResponse
    {
        // L'auteur du commentaire ou admin/gestionnaire peut supprimer
        if (Auth::id() !== $comment->user_id && !Auth::user()->can('news-delete')) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Commentaire supprimé.');
    }

    // ── TOGGLE LIKE ───────────────────────────────────────────
    public function toggleLike(NewsEvent $news): JsonResponse
    {
        $this->authorize('news-like');

        $existing = NewsLike::where('news_event_id', $news->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            NewsLike::create([
                'news_event_id' => $news->id,
                'user_id'       => Auth::id(),
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $news->likes()->count(),
        ]);
    }
}