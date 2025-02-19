<?php

namespace Bokt\Gdpr\Jobs;

use Bokt\Gdpr\Exporter;
use Bokt\Gdpr\Models\Export;
use Bokt\Gdpr\Notifications\ExportAvailableBlueprint;
use Flarum\Notification\NotificationSyncer;
use Flarum\Queue\AbstractJob;
use Flarum\User\User;

class ExportJob extends AbstractJob
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(Exporter $exporter, NotificationSyncer $notifications)
    {
        $export = $exporter->export($this->user);

        $this->notify($export, $notifications);
    }

    public function notify(Export $export, NotificationSyncer $notifications)
    {
        $notifications->onePerUser(function () use ($export, $notifications) {
            $notifications->sync(
                new ExportAvailableBlueprint($export),
                [$export->user]
            );
        });
    }
}
