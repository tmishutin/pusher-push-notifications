<?php

namespace NotificationChannels\PusherPushNotifications;

use Pusher\PushNotifications\PushNotifications;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Events\NotificationFailed;

class PusherChannel
{
    /**
     * @var \Pusher\PushNotifications\PushNotifications
     */
    protected $pusher;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    private $events;

    /**
     * @param Pusher\PushNotifications\PushNotifications $pusher
     */
    public function __construct(PushNotifications $pusher, Dispatcher $events)
    {
        $this->pusher = $pusher;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $interest = $notifiable->routeNotificationFor('PusherPushNotifications')
            ?: $this->interestName($notifiable);

        $interest = is_string($interest) ? [$interest] : $interest;

        $response = $this->pusher->publish(
            $interest,
            $notification->toPushNotification($notifiable)->toArray()
        );

        if (! array_has($response, 'publishId')) {
            $this->events->fire(
                new NotificationFailed($notifiable, $notification, 'pusher-push-notifications', $response)
            );
        }
    }

    /**
     * Get the interest name for the notifiable.
     *
     * @param $notifiable
     *
     * @return string
     */
    protected function interestName($notifiable)
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class.'.'.$notifiable->getKey();
    }
}
