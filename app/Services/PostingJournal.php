<?php

namespace App\Services;

use App\Enum\Accounting\JournalSource;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Throwable;

class PostingJournal
{
    public function __invoke(array $data, $record, $source)
    {
        DB::beginTransaction();
        try {
            $payload = [
                'ref' => $record->code ?? $record->inv_no,
                'date' => $data['date'],
                'description' => $data['header_description'],
                'source' => $source,
                'source_id' => $record->getKey(),
                'status' => 1,
                'accounting_periods_id' => $data['accounting_periods']
            ];

            $entry = JournalEntry::create($payload);

            $payloadDetails = [];
            foreach ($data['details'] as $detail) {
                $payloadDetails[] = [
                    'journal_entry_id' => $entry->getKey(),
                    'chart_of_account_id' => $detail['chart_of_account_id'],
                    'description' => $detail['description'],
                    'debit' => $detail['debit'] ?? 0,
                    'kredit' => $detail['kredit'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // dd($payload, $payloadDetails);
            DetailJournalEntry::insert($payloadDetails);

            DB::commit();
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Opps.. Something went wrong!')
                ->body($th->getMessage())
                ->danger()
                ->send();
        }
    }
}
