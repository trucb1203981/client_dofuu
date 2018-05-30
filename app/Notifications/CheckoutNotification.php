<?php

namespace App\Notifications;
use App\Models\RegularOrder;
use App\Events\CheckoutPublished;
use App\Http\Resources\Site\OrderResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
class CheckoutNotification extends Notification
{
    use Queueable;
    public $user;
    public $order;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            // 'mail',
            'database',
            'broadcast'
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //     ->line('The introduction to the notification.')
    //     ->action('Notification Action', url('/'))
    //     ->line('Thank you for using our application!');
    // }
    public function toBroadcast($notifiable)
    {   

        $timestamp = Carbon::now()->addSecond()->toDateTimeString();
        $res       = [
            'id'              => $this->order->id,
            'name'            => $this->order->name,
            'address'         => $this->order->address,
            'lat'             => $this->order->lat,
            'lng'             => $this->order->lng,
            'distance'        => $this->order->distance,
            'phone'           => $this->order->phone,
            'bookingDate'     => Carbon::createFromFormat('Y-m-d H:i:s', $this->order->created_at)->format('d-m-Y H:i'),
            'receiveDate'     => $this->order->date,
            'receiveTime'     => $this->order->time,
            'deliveryPrice'   => $this->order->delivery_price,
            'subTotal'        => $this->order->subtotal_amount,
            'total'           => $this->order->amount,
            'memo'            => $this->order->memo,
            'discountPercent' => $this->order->discount,
            'discountTotal'   => $this->order->discount_total,
            'orderNotes'      => unserialize($this->order->note),
            'statusId'        => $this->order->status_id,
            'statusName'      => $this->order->status->order_status_name,
            'statusColor'     => $this->order->status->color,
            'owner'           => [
                'id'      => $this->order->user->id,
                'name'    => $this->order->user->name,
                'email'   => $this->order->user->email,
                'phone'   => $this->order->user->phone,
                'gender'  => $this->order->user->gender,
                'address' => $this->order->user->address,
                'image'   => $this->order->user->image,
            ],
            'employee'        => $this->order->employee != null ? [
                'id'      => $this->order->employee->id,
                'name'    => $this->order->employee->name,
                'email'   => $this->order->employee->email,
                'phone'   => $this->order->employee->phone,
                'gender'  => $this->order->employee->gender,
                'address' => $this->order->employee->address,
                'image'   => $this->order->employee->image,
            ] : null,
            'shipper'         => $this->order->shipper != null ? [
                'id'      => $this->order->shipper->id,
                'name'    => $this->order->shipper->name,
                'email'   => $this->order->shipper->email,
                'phone'   => $this->order->shipper->phone,
                'gender'  => $this->order->shipper->gender,
                'address' => $this->order->shipper->address,
                'image'   => $this->order->shipper->image,
            ] : null,
            'store'           => [
                'districtId'   => $this->order->store->district_id,
                'id'           => $this->order->store->id,
                'name'         => $this->order->store->store_name,
                'slug'         => $this->order->store->store_slug,
                'prepareTime'  => $this->order->store->preparetime,
                'address'      => $this->order->store->store_address,
                'lat'          => $this->order->store->lat,
                'lng'          => $this->order->store->lng,
                'avatar'       => $this->order->store->store_avatar,
                'verified'     => $this->order->store->verified,
                'type'         => $this->order->store->type->type_name,
                'status'       => $this->order->store->status->store_status_name,
                'status_color' => $this->order->store->status->color,
                'cityId'       => $this->order->store->district->city_id,
            ],
            'payment'         => [
                'paymentId'     => $this->order->payment->id,
                'paymentMethod' => $this->order->payment->payment_name
            ],
        ];
        return new BroadcastMessage(
            array(
                'notifiable_id'   => $notifiable->id,
                'notifiable_type' => get_class($notifiable),
                'data'            => $res,
                'read_at'         => null,
                'created_at'      => $timestamp,
                'updated_at'      => $timestamp,
            )
        );
    }
    public function toDatabase($notifiable)
    {
        $res       = [
            'id'              => $this->order->id,
            'name'            => $this->order->name,
            'address'         => $this->order->address,
            'lat'             => $this->order->lat,
            'lng'             => $this->order->lng,
            'distance'        => $this->order->distance,
            'phone'           => $this->order->phone,
            'bookingDate'     => Carbon::createFromFormat('Y-m-d H:i:s', $this->order->created_at)->format('d-m-Y H:i'),
            'receiveDate'     => $this->order->date,
            'receiveTime'     => $this->order->time,
            'deliveryPrice'   => $this->order->delivery_price,
            'subTotal'        => $this->order->subtotal_amount,
            'total'           => $this->order->amount,
            'memo'            => $this->order->memo,
            'discountPercent' => $this->order->discount,
            'discountTotal'   => $this->order->discount_total,
            'orderNotes'      => unserialize($this->order->note),
            'statusId'        => $this->order->status_id,
            'statusName'      => $this->order->status->order_status_name,
            'statusColor'     => $this->order->status->color,
            'owner'           => [
                'id'      => $this->order->user->id,
                'name'    => $this->order->user->name,
                'email'   => $this->order->user->email,
                'phone'   => $this->order->user->phone,
                'gender'  => $this->order->user->gender,
                'address' => $this->order->user->address,
                'image'   => $this->order->user->image,
            ],
            'employee'        => $this->order->employee != null ? [
                'id'      => $this->order->employee->id,
                'name'    => $this->order->employee->name,
                'email'   => $this->order->employee->email,
                'phone'   => $this->order->employee->phone,
                'gender'  => $this->order->employee->gender,
                'address' => $this->order->employee->address,
                'image'   => $this->order->employee->image,
            ] : null,
            'shipper'         => $this->order->shipper != null ? [
                'id'      => $this->order->shipper->id,
                'name'    => $this->order->shipper->name,
                'email'   => $this->order->shipper->email,
                'phone'   => $this->order->shipper->phone,
                'gender'  => $this->order->shipper->gender,
                'address' => $this->order->shipper->address,
                'image'   => $this->order->shipper->image,
            ] : null,
            'store'           => [
                'districtId'   => $this->order->store->district_id,
                'id'           => $this->order->store->id,
                'name'         => $this->order->store->store_name,
                'slug'         => $this->order->store->store_slug,
                'prepareTime'  => $this->order->store->preparetime,
                'address'      => $this->order->store->store_address,
                'lat'          => $this->order->store->lat,
                'lng'          => $this->order->store->lng,
                'avatar'       => $this->order->store->store_avatar,
                'verified'     => $this->order->store->verified,
                'type'         => $this->order->store->type->type_name,
                'status'       => $this->order->store->status->store_status_name,
                'status_color' => $this->order->store->status->color,
                'cityId'       => $this->order->store->district->city_id,
            ],
            'payment'         => [
                'paymentId'     => $this->order->payment->id,
                'paymentMethod' => $this->order->payment->payment_name
            ],
        ];
        return $res;
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $res       = [
            'id'              => $this->order->id,
            'name'            => $this->order->name,
            'address'         => $this->order->address,
            'lat'             => $this->order->lat,
            'lng'             => $this->order->lng,
            'distance'        => $this->order->distance,
            'phone'           => $this->order->phone,
            'bookingDate'     => Carbon::createFromFormat('Y-m-d H:i:s', $this->order->created_at)->format('d-m-Y H:i:'),
            'receiveDate'     => $this->order->date,
            'receiveTime'     => $this->order->time,
            'deliveryPrice'   => $this->order->delivery_price,
            'subTotal'        => $this->order->subtotal_amount,
            'total'           => $this->order->amount,
            'memo'            => $this->order->memo,
            'discountPercent' => $this->order->discount,
            'discountTotal'   => $this->order->discount_total,
            'orderNotes'      => unserialize($this->order->note),
            'statusId'        => $this->order->status_id,
            'statusName'      => $this->order->status->order_status_name,
            'statusColor'     => $this->order->status->color,
            'owner'           => [
                'id'      => $this->order->user->id,
                'name'    => $this->order->user->name,
                'email'   => $this->order->user->email,
                'phone'   => $this->order->user->phone,
                'gender'  => $this->order->user->gender,
                'address' => $this->order->user->address,
                'image'   => $this->order->user->image,
            ],
            'employee'        => $this->order->employee != null ? [
                'id'      => $this->order->employee->id,
                'name'    => $this->order->employee->name,
                'email'   => $this->order->employee->email,
                'phone'   => $this->order->employee->phone,
                'gender'  => $this->order->employee->gender,
                'address' => $this->order->employee->address,
                'image'   => $this->order->employee->image,
            ] : null,
            'shipper'         => $this->order->shipper != null ? [
                'id'      => $this->order->shipper->id,
                'name'    => $this->order->shipper->name,
                'email'   => $this->order->shipper->email,
                'phone'   => $this->order->shipper->phone,
                'gender'  => $this->order->shipper->gender,
                'address' => $this->order->shipper->address,
                'image'   => $this->order->shipper->image,
            ] : null,
            'store'           => [
                'districtId'   => $this->order->store->district_id,
                'id'           => $this->order->store->id,
                'name'         => $this->order->store->store_name,
                'slug'         => $this->order->store->store_slug,
                'prepareTime'  => $this->order->store->preparetime,
                'address'      => $this->order->store->store_address,
                'lat'          => $this->order->store->lat,
                'lng'          => $this->order->store->lng,
                'avatar'       => $this->order->store->store_avatar,
                'verified'     => $this->order->store->verified,
                'type'         => $this->order->store->type->type_name,
                'status'       => $this->order->store->status->store_status_name,
                'status_color' => $this->order->store->status->color,
                'cityId'       => $this->order->store->district->city_id,
            ],
            'payment'         => [
                'paymentId'     => $this->order->payment->id,
                'paymentMethod' => $this->order->payment->payment_name
            ],
        ];
        return $res;
    }

}