<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Mail\NewProductNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class SendNewProductNotifications extends Command
{
    protected $signature = 'products:notify-new 
                            {--days=1 : Number of days to look back for new products}
                            {--chunk=100 : Number of users to process per chunk}';

    protected $description = 'Send email notifications for newly added products to all users';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $chunkSize = (int) $this->option('chunk');

        $since = Carbon::now()->subDays($days);

        $newProducts = Product::where('created_at', '>=', $since)
            ->with(['images', 'category'])
            ->latest()
            ->get();

        if ($newProducts->isEmpty()) {
            $this->info('No new products found in the last ' . $days . ' day(s).');
            return Command::SUCCESS;
        }

        $this->info("Found {$newProducts->count()} new product(s) since {$since->toDateTimeString()}");

        $userCount = User::count();
        $this->info("Sending notifications to {$userCount} user(s)...");

        $bar = $this->output->createProgressBar($userCount);
        $bar->start();

        $totalSent = 0;

        User::chunkById($chunkSize, function ($users) use ($newProducts, &$totalSent, $bar) {
            foreach ($users as $user) {
                foreach ($newProducts as $product) {
                    Mail::to($user->email)->queue(new NewProductNotification($product));
                    $totalSent++;
                }
            }
            $bar->advance($users->count());
        });

        $bar->finish();
        $this->newLine();
        $this->info("Queued {$totalSent} notification email(s) for delivery.");

        return Command::SUCCESS;
    }
}