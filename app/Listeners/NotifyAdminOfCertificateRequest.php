<?php

namespace App\Listeners;

use App\Events\CertificateRequested;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class NotifyAdminOfCertificateRequest implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * This method is triggered when a CertificateRequested event is fired.
     * It creates a notification for the admin(s) responsible for managing certificate requests.
     */
    public function handle(CertificateRequested $event)
    {
        // Create a new notification instance for the certificate request
        $notification = new Notification([
            'type' => 'certificate_request',
            'title' => 'New Certificate Request',
            'message' => 'User ' . $event->user->name . ' has requested a new certificate.',
            'link' => route('certificates.approval', ['certificateId' => $event->certificate->id]), // Generate link to approval page
            'user_id' => Auth::id(), // Set the authenticated user ID as the creator of the notification
        ]);
        
        $notification->save();

        // Fetch the admin ID of the user based on admin_mail
        $adminId = User::where('email', $event->user->admin_mail)->value('id');

        if ($adminId) {
            // Create user notification for the admin
            $userNotification = new UserNotification();
            $userNotification->user_id = $adminId; // Insert the admin ID
            $userNotification->notification_id = $notification->id; // Associate the notification with the created notification
            $userNotification->save();
        }
    }
}