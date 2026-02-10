<?php

namespace App\Console\Commands;

use App\Models\Guardian;
use App\Models\StudentFeeManager;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillFeeGuardianIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:backfill-guardian-ids {--school_id=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill student_fee_managers.guardian_id based on student_guardians (fallback to parent_id mapping)';

    public function handle(): int
    {
        $schoolId = $this->option('school_id') ? (int) $this->option('school_id') : null;
        $dryRun = (bool) $this->option('dry-run');

        $query = StudentFeeManager::query()
            ->where(function ($q) {
                $q->whereNull('guardian_id')->orWhere('guardian_id', '=', 0);
            });

        if (!empty($schoolId)) {
            $query->where('school_id', $schoolId);
        }

        $total = (int) $query->count();
        $this->info("Found {$total} fee invoices missing guardian_id.");

        if ($total === 0) {
            return self::SUCCESS;
        }

        $updated = 0;

        $query->orderBy('id')->chunkById(200, function ($invoices) use (&$updated, $dryRun) {
            foreach ($invoices as $invoice) {
                $guardianId = StudentGuardian::where('student_id', (int) $invoice->student_id)
                    ->orderByDesc('is_fee_payer')
                    ->orderByDesc('is_primary')
                    ->value('guardian_id');

                if (empty($guardianId) && !empty($invoice->parent_id)) {
                    $parent = User::find((int) $invoice->parent_id);
                    if (!empty($parent)) {
                        $info = json_decode((string) $parent->user_information, true);
                        $info = is_array($info) ? $info : [];
                        $cnic = (string) ($info['id_card_no'] ?? '');
                        $normalized = normalize_id_card_no($cnic);
                        if ($normalized !== '') {
                            $guardianId = Guardian::where('school_id', (int) $invoice->school_id)
                                ->where('id_card_no_normalized', $normalized)
                                ->value('id');
                        }
                    }
                }

                if (empty($guardianId)) {
                    continue;
                }

                $updated++;
                if ($dryRun) {
                    continue;
                }

                StudentFeeManager::where('id', (int) $invoice->id)->update([
                    'guardian_id' => (int) $guardianId,
                ]);
            }
        });

        $mode = $dryRun ? 'Dry run' : 'Updated';
        $this->info("{$mode}: {$updated} invoices.");

        return self::SUCCESS;
    }
}

