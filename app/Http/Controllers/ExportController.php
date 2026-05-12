<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Collectiv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function applicants(Request $request): Response
    {
        Applicant::query()
            ->where('status', Applicant::STATUS_BELUM_DIVERIFIKASI)
            ->where('created_at', '<', now()->subDays(30))
            ->update(['status' => Applicant::STATUS_EXPIRED]);

        $rows = $this->applyFilters(Applicant::query(), $request)
            ->orderByDesc('created_at')
            ->get();

        return $this->xlsResponse('data-form-utama-'.now()->format('Ymd-His').'.xls', 'Data Form Utama', [
            'ID',
            'No',
            'Nama',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'AO',
            'Jenis Permohonan Kredit',
            'Status',
            'Tanggal Input',
            'Tanggal Update',
        ], $rows->map(fn (Applicant $applicant, int $index): array => [
            $applicant->id,
            $index + 1,
            $applicant->nama,
            $applicant->nik,
            $applicant->tempat_lahir,
            $applicant->tanggal_lahir?->format('Y-m-d'),
            $applicant->jenis_kelamin,
            $applicant->ao,
            $applicant->jenis_permohonan_kredit,
            Applicant::statusOptions()[$applicant->effective_status] ?? $applicant->effective_status,
            $applicant->created_at?->format('Y-m-d H:i:s'),
            $applicant->updated_at?->format('Y-m-d H:i:s'),
        ])->all());
    }

    public function collectivs(Request $request): Response
    {
        Collectiv::query()
            ->where('status', Collectiv::STATUS_BELUM_DIVERIFIKASI)
            ->where('created_at', '<', now()->subDays(30))
            ->update(['status' => Collectiv::STATUS_EXPIRED]);

        $rows = $this->applyFilters(Collectiv::query(), $request)
            ->orderByDesc('created_at')
            ->get();

        return $this->xlsResponse('data-collectiv-'.now()->format('Ymd-His').'.xls', 'Data Collectiv', [
            'ID',
            'No',
            'Nama',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'AO',
            'Jenis Permohonan Kredit',
            'Status',
            'Tanggal Input',
            'Tanggal Update',
        ], $rows->map(fn (Collectiv $collectiv, int $index): array => [
            $collectiv->id,
            $index + 1,
            $collectiv->nama,
            $collectiv->nik,
            $collectiv->tempat_lahir,
            $collectiv->tanggal_lahir?->format('Y-m-d'),
            $collectiv->jenis_kelamin,
            $collectiv->ao,
            $collectiv->jenis_permohonan_kredit,
            Collectiv::statusOptions()[$collectiv->effective_status] ?? $collectiv->effective_status,
            $collectiv->created_at?->format('Y-m-d H:i:s'),
            $collectiv->updated_at?->format('Y-m-d H:i:s'),
        ])->all());
    }

    private function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('ao')) {
            $query->where('ao', $request->string('ao')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function (Builder $query) use ($search): void {
                $query
                    ->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('nik', 'like', '%'.$search.'%')
                    ->orWhere('tempat_lahir', 'like', '%'.$search.'%')
                    ->orWhere('tanggal_lahir', 'like', '%'.$search.'%')
                    ->orWhere('jenis_kelamin', 'like', '%'.$search.'%')
                    ->orWhere('ao', 'like', '%'.$search.'%')
                    ->orWhere('jenis_permohonan_kredit', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('created_at', 'like', '%'.$search.'%')
                    ->orWhere('updated_at', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    private function xlsResponse(string $filename, string $title, array $headers, array $rows): Response
    {
        $html = view('exports.xls-table', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
