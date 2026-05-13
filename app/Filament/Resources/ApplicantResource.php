<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Models\Applicant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Helpers\ImageCompressor;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Applicants';

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
                    ->unique(ignoreRecord: true)
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
                    ->options(Applicant::statusOptions())
                    ->default(Applicant::STATUS_BELUM_DIVERIFIKASI),
                Forms\Components\FileUpload::make('ktp_file')
                    ->label('KTP')
                    ->image()
                    ->previewable(false)
                    ->downloadable(false)
                    ->openable(false)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(20480)
                    ->disk('local')
                    ->directory('tmp/ktp')
                    ->visibility('private')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->saveUploadedFileUsing(function ($file): string {
                        $filename = Str::random(48).'.enc';
                        $compressedContent = ImageCompressor::compress($file->getRealPath(), 4096);
                        $encryptedContent = Crypt::encryptString($compressedContent);

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
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->badge(),
                Tables\Columns\TextColumn::make('ao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_permohonan_kredit')
                    ->label('Jenis Permohonan Kredit')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('effective_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Applicant::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Applicant::STATUS_TERVERIFIKASI => 'success',
                        Applicant::STATUS_EXPIRED => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('export_xls')
                    ->label('Export XLS')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (): bool => auth()->user()?->isAdministrator())
                    ->url(fn ($livewire): string => route('admin.export.applicants', [
                        'search' => $livewire->tableSearch,
                        'ao' => data_get($livewire->tableFilters, 'ao.value'),
                        'status' => data_get($livewire->tableFilters, 'status.value'),
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ao')
                    ->label('AO')
                    ->options(fn (): array => Applicant::query()
                        ->whereNotNull('ao')
                        ->distinct()
                        ->orderBy('ao')
                        ->pluck('ao', 'ao')
                        ->all())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(Applicant::statusOptions()),
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn (): bool => auth()->user()?->isAdministrator()),
            ])
            ->actions([
                Tables\Actions\Action::make('view_data')
                    ->label('View Data')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->modalHeading(fn (Applicant $record): string => 'Data Applicant - '.$record->nama)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('4xl')
                    ->modalContent(fn (Applicant $record) => view('filament.applicants.view-data', [
                        'record' => $record,
                    ])),
                Tables\Actions\Action::make('quick_edit_status')
                    ->label('Quick Edit Status')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->modalHeading(fn (Applicant $record): string => 'Ubah Status - '.$record->nama)
                    ->modalSubmitActionLabel('Simpan Status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options(Applicant::statusOptions()),
                    ])
                    ->fillForm(fn (Applicant $record): array => [
                        'status' => $record->status,
                    ])
                    ->action(function (Applicant $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                        ]);
                    }),
                Tables\Actions\Action::make('view_ktp')
                    ->label('View KTP')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Applicant $record): string => route('admin.ktp.show', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('download_ktp')
                    ->label('Download KTP')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Applicant $record): string => route('admin.ktp.show', ['applicant' => $record, 'download' => 1]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdministrator()),
                Tables\Actions\DeleteAction::make(),
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
        Applicant::query()
            ->where('status', Applicant::STATUS_BELUM_DIVERIFIKASI)
            ->where('created_at', '<', now()->subDays(30))
            ->update(['status' => Applicant::STATUS_EXPIRED]);

        $query = parent::getEloquentQuery();

        if (auth()->user()?->isAdministrator()) {
            $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicants::route('/'),
            'create' => Pages\CreateApplicant::route('/create'),
            'edit' => Pages\EditApplicant::route('/{record}/edit'),
        ];
    }
}
