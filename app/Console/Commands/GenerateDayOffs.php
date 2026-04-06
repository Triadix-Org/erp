<?php

namespace App\Console\Commands;

use App\Models\PersonnelData;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

use function Symfony\Component\Clock\now;

class GenerateDayOffs extends Command
{
    const DAYS_OFF_PER_MONTH = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:generate-day-offs {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate day offs for employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('user_id')) {
            $this->info('Starting to generate day offs for user ID: ' . $this->option('user_id'));

            $this->processSingleEmployee(
                (int) $this->option('user_id')
            );

            return;
        }

        $this->info('Starting to generate day offs for all eligible employees');

        $this->processEachEmployee(
            $this->getEligibleEmployees()
        );
    }

    protected function processSingleEmployee(int $userId): void
    {
        $employee = PersonnelData::where('id', $userId)
            ->workedOverOneYear()
            ->first();

        if (blank($employee)) {
            return;
        }

        $months = $this->calculateMonthsUntilEndOfYear();
        $this->addLeaveQuota($employee, $months);
    }

    protected function getEligibleEmployees(): Collection
    {
        return PersonnelData::workedOverOneYear()
            ->get();
    }

    protected function processEachEmployee(Collection $employees): void
    {
        if ($employees->isEmpty()) {
            $this->info('No eligible employees found.');
            return;
        }

        foreach ($employees as $employee) {
            $months = $this->calculateMonthsUntilEndOfYear();
            $this->addLeaveQuota($employee, $months);
        }
    }

    protected function calculateMonthsUntilEndOfYear(): int
    {
        $now = \Carbon\Carbon::now();
        $endOfYear = \Carbon\Carbon::now()->endOfYear();

        return $now->diffInMonths($endOfYear) + 1;
    }

    protected function addLeaveQuota(PersonnelData $personnelData, int $months): void
    {
        $additionalLeave = $months * self::DAYS_OFF_PER_MONTH;

        $personnelData->update([
            'leave_quota' => $additionalLeave,
        ]);
    }
}
