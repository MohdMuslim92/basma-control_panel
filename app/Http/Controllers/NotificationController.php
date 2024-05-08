<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class NotificationController extends Controller
{
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();
        
        // Fetch notifications related to the current user, ordered by creation timestamp in descending order
        $userNotifications = UserNotification::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();
        
        // Count unread notifications
        $unreadCount = $userNotifications->where('read', false)->count();
        
        // Fetch the full notifications data related to the user notifications
        $notifications = $userNotifications->map(function ($userNotification) {
            
            // Fetch the related notification data
            $notification = Notification::find($userNotification->notification_id);
            
            // Add the notification data to the user notification object
            $userNotification->notification = $notification;

            // Set the read property based on the read column in user_notifications table
            $userNotification->notification->read = $userNotification->read;
            
            return $userNotification;
        });
        
        return response()->json([
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            // Find the user notification by ID and ensure it belongs to the authenticated user
            $userNotification = UserNotification::where('notification_id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            // Mark the notification as read
            $userNotification->update([
                'read' => true,
                'read_at' => now(),
            ]);
    
            return response()->json(['message' => 'Notification marked as read successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark notification as read'], 500);
        }
    }

}
