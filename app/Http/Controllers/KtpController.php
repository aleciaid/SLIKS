<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Collectiv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class KtpController extends Controller
{
    public function show(Request $request, Applicant $applicant): Response
    {
        return $this->response($request, $applicant, 'applicant');
    }

    public function showCollectiv(Request $request, Collectiv $collectiv): Response
    {
        return $this->response($request, $collectiv, 'collectiv');
    }

    private function response(Request $request, Applicant|Collectiv $record, string $type): Response
    {
        abort_unless($request->user(), 403);
        abort_if(str_contains($record->ktp_file, '/') || str_contains($record->ktp_file, '\\'), 404);

        $path = 'private/ktp/'.$record->ktp_file;
        abort_unless(Storage::disk('local')->exists($path), 404);

        Log::info('KTP file accessed', [
            'type' => $type,
            'record_id' => $record->id,
            'user_id' => $request->user()->id,
            'mode' => $request->boolean('download') ? 'download' : 'view',
        ]);

        $decryptedContent = Crypt::decryptString(Storage::disk('local')->get($path));
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';
        $safeName = 'ktp-'.$record->nik.'.jpg';

        return response($decryptedContent, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => $disposition.'; filename="'.$safeName.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
