<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\ServiceProvider;
use Pusher\PushNotifications\PushNotifications;

class PusherPushNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(PusherChannel::class)
            ->needs(PushNotifications::class)
            ->give(function () {
                $pusherConfig = config('broadcasting.connections.pusher');

                return new PushNotifications(array(
                    "instanceId" => $pusherConfig['beams_id'],
                    "secretKey" => $pusherConfig['beams_secret'],
                ));
            });
    }
}
