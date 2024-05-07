<?php

namespace App\Listeners;

use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\UserRegistered;


class UserRegisteredNotification implements ShouldQueue
{
    public function handle(UserRegistered $event)
    {
        // Access the user object from the event
        $user = $event->user;
        // Create a notification instance
        $notification = Notification::create([
            'type' => 'user_registered',
            'title' => 'New User Registration',
            'message' => 'A new user has registered: ' . $user->name,
            'user_id' => $user->id,
            'link' => route('user.approval', ['userId' => $user->id]), // Generate link
        ]);


        // Persist the notification with potential optimization
        $notification->save();

        // Fetch users who belong to the membership affair office (office_id = 2)
        $usersInOffice = Admin::where('office_id', 2)->pluck('user_id');

        // Create user notifications for each user in the office
        foreach ($usersInOffice as $userId) {
            $userNotification = new UserNotification();
            $userNotification->user_id = $userId;
            $userNotification->notification_id = $notification->id;
            $userNotification->save();
        }

    }
}