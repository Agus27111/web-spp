<?php

namespace App\Filament\Admin\Resources\FoundationRequestResource\Pages;

use App\Filament\Admin\Resources\FoundationRequestResource;
use App\Models\FoundationRequest;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListFoundationRequests extends ListRecords
{
    protected static string $resource = FoundationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),

            'Pending' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending'))
                ->badge(FoundationRequest::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'Approved' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'approved'))
                ->badge(FoundationRequest::where('status', 'approved')->count())
                ->badgeColor('success'),
        ];
    }
}
