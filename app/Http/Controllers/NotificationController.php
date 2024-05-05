<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserNotification;

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
            
            return $userNotification;
        });
        
        return response()->json([
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
