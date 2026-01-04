<?php

namespace App\Filament\Resources\CompanyReviews\Pages;

use App\Filament\Resources\CompanyReviews\CompanyReviewResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyReview extends ViewRecord
{
    protected static string $resource = CompanyReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
