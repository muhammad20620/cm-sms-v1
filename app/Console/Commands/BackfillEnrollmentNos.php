<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use Illuminate\Console\Command;

class BackfillEnrollmentNos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enrollment:backfill-nos {--school_id=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing enrollment_no values for existing enrollments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $query = Enrollment::query()->where(function ($q) {
            $q->whereNull('enrollment_no')->orWhere('enrollment_no', '=', '');
        });

        if ($this->option('school_id')) {
            $query->where('school_id', (int) $this->option('school_id'));
        }

        $total = (int) $query->count();
        $this->info("Found {$total} enrollments missing enrollment_no.");

        if ($total === 0) {
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;

        $query->orderBy('id')->chunkById(200, function ($enrollments) use (&$updated, $dryRun) {
            foreach ($enrollments as $enrollment) {
                $attempts = 0;
                do {
                    $attempts++;
                    $candidate = generate_student_number((int) $enrollment->school_id, 'enrollment');
                    $exists = Enrollment::where('enrollment_no', $candidate)->exists();
                } while ($exists && $attempts < 10);

                if ($exists) {
                    $this->warn("Skipping enrollment id={$enrollment->id} (could not generate unique enrollment_no).");
                    continue;
                }

                if ($dryRun) {
                    $updated++;
                    continue;
                }

                $enrollment->enrollment_no = $candidate;
                $enrollment->save();
                $updated++;
            }
        });

        $mode = $dryRun ? 'Dry run' : 'Updated';
        $this->info("{$mode}: {$updated} enrollments.");

        return self::SUCCESS;
    }
}

