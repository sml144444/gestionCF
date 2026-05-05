<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * GET /notifications
     * Returns the latest 15 notifications + unread count for the navbar dropdown.
     */
public function index(): JsonResponse
{
    $user = Auth::user();

    $query = UserNotification::forUser($user->id)
        ->orderByDesc('created_at');

    // bell dropdown asks for everything
    $notifications = request()->boolean('all')
        ? $query->get()
        : $query->limit(15)->get();

    $unreadCount = UserNotification::forUser($user->id)->unread()->count();

    return response()->json([
        'notifications' => $notifications->map(fn ($n) => [
            'id'         => $n->id,
            'message'    => $n->message,
            'url'        => $n->url,
            'icon'       => ($n->type_config)['icon'],
            'bg'         => ($n->type_config)['bg'],
            'is_read'    => $n->is_read,
            'count'      => $n->count,
            'created_at' => $n->created_at->diffForHumans(),
        ]),
        'unread_count' => $unreadCount,
    ]);
}

    /**
     * POST /notifications/{id}/read
     * Mark a single notification as read. Returns redirect URL if set.
     */
    public function markRead(UserNotification $notification): JsonResponse
    {
        abort_if($notification->user_id !== Auth::id(), 403);

        $notification->update(['read_at' => now()]);

        $unreadCount = UserNotification::forUser(Auth::id())->unread()->count();

        return response()->json([
            'ok'           => true,
            'url'          => $notification->url,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * POST /notifications/read-all
     * Mark every unread notification of the current user as read.
     */
    public function markAllRead(): JsonResponse
    {
        UserNotification::forUser(Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true, 'unread_count' => 0]);
    }

     
public function markReadByReclamation(int $reclamationId): \Illuminate\Http\JsonResponse
{
    $updated = \App\Models\UserNotification::forUser(Auth::id())
        ->unread()
        ->whereJsonContains('data->reclamation_id', $reclamationId)
        ->update(['read_at' => now()]);
 
    $unreadCount = \App\Models\UserNotification::forUser(Auth::id())->unread()->count();
 
    return response()->json([
        'ok'           => true,
        'cleared'      => $updated,
        'unread_count' => $unreadCount,
    ]);
}

/**
 * DELETE /notifications/{id}
 * Hard-delete a single notification belonging to the current user.
 */
public function destroy(UserNotification $notification): JsonResponse
{
    abort_if($notification->user_id !== Auth::id(), 403);

    $wasUnread = is_null($notification->read_at);
    $notification->delete();

    $unreadCount = UserNotification::forUser(Auth::id())->unread()->count();

    return response()->json([
        'ok'           => true,
        'unread_count' => $unreadCount,
    ]);
}
}