<?php

namespace App\Console\Commands;

use App\Models\QuoteRequest;
use Illuminate\Console\Command;

class PruneOldQuoteRequests extends Command
{
    protected $signature = 'keenguild:prune-quote-requests';

    protected $description = 'Delete project inquiries older than the approved 12-month retention period';

    public function handle(): int
    {
        $deleted = QuoteRequest::query()->where('created_at', '<', now()->subMonths(12))->delete();
        $this->info("Deleted {$deleted} expired project inquiries.");

        return self::SUCCESS;
    }
}
