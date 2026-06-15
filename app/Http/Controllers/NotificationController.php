<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = $user->notifications()->take(12)->get()->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->data['title'] ?? 'Notification',
            'body' => $n->data['body'] ?? null,
            'url' => route('notifications.go', $n->id),
            'read' => $n->read_at !== null,
            'ago' => $n->created_at->diffForHumans(),
        ]);

        return response()->json([
            'unread' => $user->unreadNotifications()->count(),
            'items' => $items,
        ]);
    }

    public function go(Request $request, string $notification): RedirectResponse
    {
        $n = $request->user()->notifications()->findOrFail($notification);
        $n->markAsRead();

        return redirect($n->data['url'] ?? url('/dashboard'));
    }

    public function readAll(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }

    // Admin: rolling activity counter so the admin panel can chime on new platform activity.
    public function activity(): JsonResponse
    {
        return response()->json([
            'count' => QuoteRequest::count() + Quote::count() + Booking::count() + Payment::count(),
        ]);
    }
}
