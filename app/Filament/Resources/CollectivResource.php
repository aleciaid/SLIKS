<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectivResource\Pages;
use App\Models\Collectiv;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectivResource extends Resource
{
    protected static ?string $model = Collectiv::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Collectiv';

    protected static ?string $navigationGroup = 'Data SLIK';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (string $state): string => Str::upper($state)),
                Forms\Components\TextInput::make('nik')
                    ->required()
                    ->length(16)
                    ->numeric()
                    ->maxLength(16),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->required(),
                Forms\Components\Select::make('jenis_kelamin')
                    ->required()
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                Forms\Components\TextInput::make('ao')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jenis_permohonan_kredit')
                    ->label('Jenis Permohonan Kredit')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(Collectiv::statusOptions())
                    ->default(Collectiv::STATUS_BELUM_DIVERIFIKASI),
                Forms\Components\FileUpload::make('ktp_file')
                    ->label('KTP')
                    ->image()
                    ->previewable(false)
                    ->downloadable(false)
                    ->openable(false)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->disk('local')
                    ->directory('tmp/ktp')
                    ->visibility('private')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->saveUploadedFileUsing(function ($file): string {
                        $filename = Str::random(48).'.enc';
                        $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));

                        Storage::disk('local')->put('private/ktp/'.$filename, $encryptedContent);

                        return $filename;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_permohonan_kredit')
                    ->label('Jenis Permohonan Kredit')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('effective_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Collectiv::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Collectiv::STATUS_TERVERIFIKASI => 'success',
                        Collectiv::STATUS_EXPIRED => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('export_xls')
                    ->label('Export XLS')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (): bool => auth()->user()?->isAdministrator())
                    ->url(fn ($livewire): string => route('admin.export.collectivs', [
                        'search' => $livewire->tableSearch,
                        'ao' => data_get($livewire->tableFilters, 'ao.value'),
                        'status' => data_get($livewire->tableFilters, 'status.value'),
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ao')
                    ->label('AO')
                    ->options(fn (): array => Collectiv::query()
                        ->whereNotNull('ao')
                        ->distinct()
                        ->orderBy('ao')
                        ->pluck('ao', 'ao')
                        ->all())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(Collectiv::statusOptions()),
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn (): bool => auth()->user()?->isAdministrator()),
            ])
            ->actions([
                Tables\Actions\Action::make('view_data')
                    ->label('View Data')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->modalHeading(fn (Collectiv $record): string => 'Data Collectiv - '.$record->nama)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('4xl')
                    ->modalContent(fn (Collectiv $record) => view('filament.collectivs.view-data', [
                        'record' => $record,
                    ])),
                Tables\Actions\Action::make('quick_edit_status')
                    ->label('Quick Edit Status')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->modalHeading(fn (Collectiv $record): string => 'Ubah Status - '.$record->nama)
                    ->modalSubmitActionLabel('Simpan Status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options(Collectiv::statusOptions()),
                    ])
                    ->fillForm(fn (Collectiv $record): array => [
                        'status' => $record->status,
                    ])
                    ->action(function (Collectiv $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                        ]);
                    }),
                Tables\Actions\Action::make('view_ktp')
                    ->label('View KTP')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Collectiv $record): string => route('admin.collectiv.ktp.show', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('download_ktp')
                    ->label('Download KTP')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Collectiv $record): string => route('admin.collectiv.ktp.show', ['collectiv' => $record, 'download' => 1]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdministrator()),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdministrator()),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdministrator()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isAdministrator()),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isAdministrator()),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        Collectiv::query()
            ->where('status', Collectiv::STATUS_BELUM_DIVERIFIKASI)
            ->where('created_at', '<', now()->subDays(30))
            ->update(['status' => Collectiv::STATUS_EXPIRED]);

        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollectivs::route('/'),
            'create' => Pages\CreateCollectiv::route('/create'),
            'edit' => Pages\EditCollectiv::route('/{record}/edit'),
        ];
    }
}
