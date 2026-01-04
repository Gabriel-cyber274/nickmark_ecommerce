<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Widgets\UserOrdersAnalysis;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected $queryString = ['activeRelationManager'];

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }


    protected function getFooterWidgets(): array
    {

        return [
            UserOrdersAnalysis::class
        ];
    }
}
