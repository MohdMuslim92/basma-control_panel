<?php

namespace App\Listeners;

use App\Events\CertificateRequested;
use App\Models\Notification;
use App\Models\Admin;
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
        // Generate the link
        $link = route('certificates.approval', ['certificateId' => $event->certificate->id]);

        // Check if there is an existing notification for this certificate
        $existingNotification = Notification::where('type', 'certificate_request')
            ->where('link', $link)
            ->first();

            if (!$existingNotification) {
                // Create a new notification instance for the certificate request
                $notification = new Notification([
                    'type' => 'certificate_request',
                    'title' => 'New Certificate Request',
                    'message' => 'User ' . $event->user->name . ' has requested a new certificate.',
                    'link' => $link, // Use the previously generated link
                    'user_id' => Auth::id(), // Set the authenticated user ID as the creator of the notification
                ]);
                $notification->save();
            } else {
                $notification = $existingNotification;
            }

        // Determine the recipients based on the certificate status
        switch ($event->certificate->status) {
            case 0:
                // Fetch the admin ID of the user based on admin_mail
                $adminId = User::where('email', $event->user->admin_mail)->value('id');

                if ($adminId) {
                    // Create user notification for the admin
                    $this->createUserNotification($adminId, $notification);
                }
                break;
            case 1:
                // Fetch super admin and assistant admin IDs
                $admins = Admin::where('office_id', 2)
                              ->whereIn('admin', [1, 2])
                              ->pluck('user_id')
                              ->toArray();
                
                foreach ($admins as $adminId) {
                    if ($adminId) {
                        // Create user notification for each admin
                        $this->createUserNotification($adminId, $notification);
                    }
                }
                break;
            case 2:
                // Fetch users based on user_status_id 8
                $users = User::where('user_status_id', 8)
                             ->pluck('id')
                             ->toArray();
                
                foreach ($users as $userId) {
                    if ($userId) {
                        // Create user notification for each user
                        $this->createUserNotification($userId, $notification);
                    }
                }
                break;
            case 3:
                // Certificate has been approved, notify the user
                $this->notifyUserOfApproval($event);
                break;
            default:
                // No specific handling for other status values
                break;
        }
    }

    /**
    * Create or update user notification.
    *
    * @param int $userId The user ID to notify
    * @param Notification $notification The notification instance
    */
    private function createUserNotification($userId, $notification)
    {
        // Check if the user notification already exists
        $existingUserNotification = UserNotification::where('user_id', $userId)
            ->where('notification_id', $notification->id)
            ->first();

        if ($existingUserNotification) {
            // Update the existing notification's read status
            $existingUserNotification->read = 0;
            $existingUserNotification->read_at = null;
            $existingUserNotification->save();
        } else {
            // Create a new user notification
            $userNotification = new UserNotification();
            $userNotification->user_id = $userId;
            $userNotification->notification_id = $notification->id;
            $userNotification->save();
        }
    }

    /**
     * Notify the user that their certificate has been approved.
     *
     * @param Certificate $certificate The approved certificate
     */
    private function notifyUserOfApproval($event)
    {
        // Create a new notification instance for the certificate approval
        $notification = new Notification([
            'type' => 'certificate_approval',
            'title' => 'Certificate Approved',
            'message' => 'Your certificate has been approved.',
            'link' => route('certificate.view', ['certificateId' => $event->certificate->id]), // Generate link to view and download certificate
            'user_id' => Auth::id(), // Set the authenticated user ID as the creator of the notification
        ]);

        $notification->save();

        // Create user notification for the certificate owner
        $this->createUserNotification($event->certificate->user_id, $notification);
    }
}