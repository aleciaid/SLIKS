<?php

namespace App\Filament\Resources\ApplicantResource\Widgets;

use App\Models\Applicant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicantStatusOverview extends BaseWidget
{
    protected function getStats(): array
    {
        Applicant::query()
            ->where('status', Applicant::STATUS_BELUM_DIVERIFIKASI)
            ->where('created_at', '<', now()->subDays(30))
            ->update(['status' => Applicant::STATUS_EXPIRED]);

        return [
            Stat::make('Total Data', Applicant::query()->count())
                ->description('Semua data applicant')
                ->icon('heroicon-o-users'),
            Stat::make('Belum Diverifikasi', Applicant::query()->where('status', Applicant::STATUS_BELUM_DIVERIFIKASI)->count())
                ->description('Data baru menunggu verifikasi')
                ->color('warning')
                ->icon('heroicon-o-clock'),
            Stat::make('Terverifikasi', Applicant::query()->where('status', Applicant::STATUS_TERVERIFIKASI)->count())
                ->description('Data sudah diverifikasi')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            Stat::make('Expired', Applicant::query()->where('status', Applicant::STATUS_EXPIRED)->count())
                ->description('Data lebih dari 30 hari')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
