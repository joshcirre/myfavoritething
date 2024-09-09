<?php

namespace App\Console\Commands;

use App\Mail\FeedEndedNotification;
use App\Models\Feed;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyEndedFeeds extends Command
{
    protected $signature = 'app:notify-ended-feeds {--force : Send notifications even if feeds have not ended}';

    protected $description = 'Notify users about ended feeds';

    public function handle(): int
    {
        $query = Feed::query();

        if (! $this->option('force')) {
            $query->where('end_date', '<=', now())
                ->where('notifications_sent', false);
        }

        $feeds = $query->with(['favorites', 'user'])->get();

        foreach ($feeds as $feed) {
            $usersToNotify = collect([$feed->user])->merge($feed->favorites);

            $usersToNotify = $usersToNotify->unique('id');

            foreach ($usersToNotify as $user) {
                if ($user) {
                    Mail::to($user)->send(new FeedEndedNotification($feed));
                }
            }

            $feed->update(['notifications_sent' => true]);
        }

        $this->info('Notifications sent for '.$feeds->count().' feeds.');

        return Command::SUCCESS;
    }
}
