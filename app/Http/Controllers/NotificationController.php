<?php

namespace App\Http\Controllers;

use App\Models\NotificationRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark a single notification as read for the current user.
     */
    public function markRead(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string|max:64',
        ]);

        $userId = auth()->id();
        if (! $userId) {
            return response()->json(['ok' => false], 401);
        }

        NotificationRead::firstOrCreate(
            ['user_id' => $userId, 'notification_id' => $request->input('id')],
            ['read_at' => now()],
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Mark every current notification as read for the current user.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['ok' => false], 401);
        }

        $ids = (array) $request->input('ids', []);
        $now = now();
        $rows = array_map(fn ($id) => [
            'user_id' => $user->id,
            'notification_id' => substr((string) $id, 0, 64),
            'read_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], $ids);

        if (! empty($rows)) {
            // upsert so re-clicks don't blow up; updated_at refreshed.
            NotificationRead::upsert(
                $rows,
                ['user_id', 'notification_id'],
                ['read_at', 'updated_at'],
            );
        }

        return response()->json(['ok' => true, 'count' => count($rows)]);
    }

    /**
     * Unread counts for a given list of notification ids (used by the UI on poll).
     */
    public function unread(Request $request): JsonResponse
    {
        $userId = auth()->id();
        if (! $userId) {
            return response()->json(['unread' => []], 401);
        }

        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['unread' => []]);
        }

        $read = NotificationRead::where('user_id', $userId)
            ->whereIn('notification_id', $ids)
            ->pluck('notification_id')
            ->all();

        $unread = array_values(array_diff($ids, $read));

        return response()->json(['unread' => $unread]);
    }
}
