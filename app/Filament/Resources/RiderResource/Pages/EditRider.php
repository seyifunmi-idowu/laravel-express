<?php

namespace App\Filament\Resources\RiderResource\Pages;

use App\Filament\Resources\RiderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;


class EditRider extends EditRecord
{
    protected static string $resource = RiderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $action = $data['action'] ?? null;
        $actionReason = $data['action_reason'] ?? null;

        if ($action) {
            $statusUpdates = json_decode($this->record->status_updates) ?? [];

            switch ($action) {
                case 'APPROVE_RIDER':
                    $statusUpdates[] = ['status' => 'APPROVED', 'date' => now()];
                    $data['status'] = 'APPROVED';
                    $this->record->status_updates =json_encode($statusUpdates);
                    $this->record->save();

                    // RiderService::setRiderAvatarWithPassport($this->record);

                    NotificationService::sendEmailMessage(
                        [$this->record->user->email], 
                        "Your Documents Have Been Accepted", 
                        ['display_name' => $this->record->display_name],
                        "emails.rider_accepted"
                    );
                    break;

                case 'DISAPPROVE_RIDER':
                    $statusUpdates[] = [
                        'status' => 'DISAPPROVED',
                        'decline_reason' => $actionReason,
                        'date' => now(),
                    ];
                    $data['status'] = 'DISAPPROVED';
                    $this->record->status_updates =json_encode($statusUpdates);
                    $this->record->save();

                    NotificationService::sendEmailMessage(
                        [$this->record->user->email], 
                        "We Could Not Approve Your Documents", 
                        [
                            'display_name' => $this->record->display_name,
                            'decline_reason' => $actionReason,
                        ],
                        "emails.rider_declined"
                    );
                    break;

                case 'SUSPEND_RIDER':
                    $statusUpdates[] = [
                        'status' => 'SUSPENDED',
                        'suspend_reason' => $actionReason,
                        'date' => now(),
                    ];
                    $data['status'] = 'SUSPENDED';
                    $this->record->status_updates =json_encode($statusUpdates);
                    $this->record->save();

                    NotificationService::sendEmailMessage(
                        [$this->record->user->email], 
                        "Account Suspension", 
                        [
                            'display_name' => $this->record->display_name,
                            'suspend_reason' => $actionReason,
                        ],
                        "emails.rider_suspended"
                    );
                    break;
            }
        }

        return $data;
    }

    protected function getFormSchema(): array
    {
        return [

        ];
    }

}
