<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Users';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                IconColumn::make('is_admin')
                    ->label('Platform Admin')
                    ->boolean(),
                IconColumn::make('is_org_admin')
                    ->label('Org Admin')
                    ->boolean(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Attach User')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'email']),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                DetachBulkAction::make(),
            ]);
    }
}
