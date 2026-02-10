<?php

namespace App\Console\Commands;

use App\Models\Guardian;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillGuardiansFromParentLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guardians:backfill-from-parent-links {--school_id=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create guardians/student_guardians records from existing users.parent_id links';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $schoolId = $this->option('school_id') ? (int) $this->option('school_id') : null;
        $dryRun = (bool) $this->option('dry-run');

        $studentsQuery = User::query()
            ->where('role_id', 7)
            ->whereNotNull('parent_id');

        if (!empty($schoolId)) {
            $studentsQuery->where('school_id', $schoolId);
        }

        $total = (int) $studentsQuery->count();
        $this->info("Found {$total} students with parent_id.");

        if ($total === 0) {
            return self::SUCCESS;
        }

        $createdGuardians = 0;
        $linked = 0;

        $studentsQuery->orderBy('id')->chunkById(200, function ($students) use (&$createdGuardians, &$linked, $dryRun) {
            foreach ($students as $student) {
                $parent = User::find($student->parent_id);
                if (empty($parent)) {
                    continue;
                }

                $schoolId = (int) $student->school_id;
                $parentInfo = json_decode((string) $parent->user_information, true);
                $parentInfo = is_array($parentInfo) ? $parentInfo : [];
                $cnic = (string) ($parentInfo['id_card_no'] ?? '');
                $normalized = normalize_id_card_no($cnic);

                $guardian = null;
                if ($normalized !== '') {
                    $guardian = Guardian::where('school_id', $schoolId)
                        ->where('id_card_no_normalized', $normalized)
                        ->first();
                } else {
                    $guardian = Guardian::where('school_id', $schoolId)
                        ->where('user_id', (int) $parent->id)
                        ->first();
                }

                if (empty($guardian)) {
                    $createdGuardians++;
                    if ($dryRun) {
                        $guardianId = 0;
                    } else {
                        $guardian = Guardian::create([
                            'school_id' => $schoolId,
                            'user_id' => (int) $parent->id,
                            'name' => $parent->name,
                            'id_card_no' => $cnic !== '' ? $cnic : null,
                            'id_card_no_normalized' => $normalized !== '' ? $normalized : null,
                            'phone' => $parentInfo['phone'] ?? null,
                            'address' => $parentInfo['address'] ?? null,
                        ]);
                        $guardianId = (int) $guardian->id;
                    }
                } else {
                    $guardianId = (int) $guardian->id;
                }

                $linked++;
                if ($dryRun || $guardianId === 0) {
                    continue;
                }

                StudentGuardian::firstOrCreate(
                    ['student_id' => (int) $student->id, 'guardian_id' => $guardianId, 'relation' => 'father'],
                    ['is_primary' => 1, 'is_fee_payer' => 1]
                );
            }
        });

        $mode = $dryRun ? 'Dry run' : 'Completed';
        $this->info("{$mode}. Guardians created: {$createdGuardians}. Links processed: {$linked}.");

        return self::SUCCESS;
    }
}

