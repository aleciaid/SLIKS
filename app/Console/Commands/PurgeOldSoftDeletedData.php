<?php

namespace App\Console\Commands;

use App\Models\Applicant;
use App\Models\Collectiv;
use Illuminate\Console\Command;

class PurgeOldSoftDeletedData extends Command
{
    protected $signature = 'app:purge-old-soft-deleted-data';

    protected $description = 'Permanently delete applicant and collectiv records that were soft deleted more than 30 days ago.';

    public function handle(): int
    {
        $threshold = now()->subDays(30);

        $applicants = Applicant::onlyTrashed()
            ->where('deleted_at', '<', $threshold)
            ->forceDelete();

        $collectivs = Collectiv::onlyTrashed()
            ->where('deleted_at', '<', $threshold)
            ->forceDelete();

        $this->info("Purged {$applicants} applicants and {$collectivs} collectiv records.");

        return self::SUCCESS;
    }
}
