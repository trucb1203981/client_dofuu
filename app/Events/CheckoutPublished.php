<?php

namespace App\Events;
use App\Http\Resources\Site\OrderResource;
use App\Models\User;
use App\Models\RegularOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Carbon\Carbon;
class CheckoutPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $order;
    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RegularOrder $order, User $user)
    {
        $this->order = $order;
        $this->user  = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.'.$this->user->id);
    }

    public function broadcastWith()
    {
        return new OrderResource($this->order);
    }
}
