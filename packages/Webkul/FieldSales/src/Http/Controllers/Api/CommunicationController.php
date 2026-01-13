<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Announcement;
use Webkul\FieldSales\Models\Message;
use Webkul\User\Models\User;
use Illuminate\Support\Facades\Log;

class CommunicationController extends Controller
{
    /**
     * Get Company Announcements (News).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function announcements(): JsonResponse
    {
        $user = auth()->user();

        $announcements = Announcement::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'Announcements fetched successfully.',
            'data' => $announcements
        ]);
    }

    /**
     * Get Inbox (Messages).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function inbox(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Fetch messages where the user is sender or receiver
        // Group by 'other' user to show threads (Simplified for now: Just list of messages)
        $messages = Message::where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'Messages fetched successfully.',
            'data' => $messages
        ]);
    }

    /**
     * Send Message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        try {
            $user = auth()->user();

            $message = Message::create([
                'company_id' => $user->company_id,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
            ]);

            return response()->json([
                'message' => 'Message sent successfully.',
                'data' => $message,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Message Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to send message.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
