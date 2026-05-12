<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FormController extends Controller
{
    public function index(): View
    {
        $captcha = $this->generateCaptcha();

        return view('form', [
            'captchaQuestion' => $captcha['question'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', 'unique:applicants,nik'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'ao' => ['required', 'string', 'max:255'],
            'jenis_permohonan_kredit' => ['required', 'string', 'max:255'],
            'ktp' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'nik.unique' => 'NIK sudah terdaftar',
            'captcha_answer.required' => 'Jawaban CAPTCHA wajib diisi',
            'captcha_answer.integer' => 'Jawaban CAPTCHA harus berupa angka',
        ]);

        if ((int) $validated['captcha_answer'] !== (int) $request->session()->get('ktp_captcha_answer')) {
            $this->generateCaptcha();

            return back()
                ->withErrors(['captcha_answer' => 'Jawaban CAPTCHA salah'])
                ->withInput();
        }

        $file = $request->file('ktp');
        $filename = Str::random(48).'.enc';
        $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));

        Storage::disk('local')->put('private/ktp/'.$filename, $encryptedContent);

        try {
            Applicant::create([
                'nama' => Str::upper($validated['nama']),
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'ao' => $validated['ao'],
                'jenis_permohonan_kredit' => $validated['jenis_permohonan_kredit'],
                'ktp_file' => $filename,
                'status' => Applicant::STATUS_BELUM_DIVERIFIKASI,
            ]);
        } catch (UniqueConstraintViolationException) {
            Storage::disk('local')->delete('private/ktp/'.$filename);
            $this->generateCaptcha();

            return back()
                ->withErrors(['nik' => 'NIK sudah terdaftar'])
                ->withInput();
        }

        $request->session()->forget('ktp_captcha_answer');

        return back()->with('success', 'Pengajuan KTP berhasil dikirim.');
    }

    public function checkNik(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'digits:16'],
        ]);

        return response()->json([
            'exists' => Applicant::withTrashed()
                ->where('nik', $validated['nik'])
                ->exists(),
        ]);
    }

    private function generateCaptcha(): array
    {
        $firstNumber = random_int(1, 20);
        $secondNumber = random_int(1, 20);

        session(['ktp_captcha_answer' => $firstNumber + $secondNumber]);

        return [
            'question' => $firstNumber.' + '.$secondNumber,
        ];
    }
}
