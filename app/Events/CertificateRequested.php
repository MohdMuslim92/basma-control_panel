<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Certificate;
use App\Models\User;

class CertificateRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user; // User who requested the certificate
    public $certificate; // Certificate being requested

    /**
     * Create a new event instance.
     *
     * @param User $user The user who requested the certificate
     * @param Certificate $certificate The certificate being requested
     */
    public function __construct(User $user, Certificate $certificate)
    {
        $this->user = $user; // Assign the user object to the event property
        $this->certificate = $certificate; // Assign the certificate object to the event property
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel> The private channel for admin notifications
     */
    public function broadcastOn()
    {
        return new PrivateChannel('admin.' . $this->user->admin_id); // Broadcast on a private channel for the admin associated with the user
    }

    /**
     * Get the name of the broadcast event.
     *
     * @return string The event name
     */
    public function broadcastAs()
    {
        return 'certificate.requested'; // The name of the broadcast event
    }
}