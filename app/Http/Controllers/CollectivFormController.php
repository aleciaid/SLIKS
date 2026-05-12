<?php

namespace App\Http\Controllers;

use App\Models\Collectiv;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollectivFormController extends Controller
{
    public function index(): View
    {
        $captcha = $this->generateCaptcha();

        return view('collectiv-form', [
            'captchaQuestion' => $captcha['question'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'ao' => ['required', 'string', 'max:255'],
            'jenis_permohonan_kredit' => ['required', 'string', 'max:255'],
            'ktp' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'captcha_answer.required' => 'Jawaban CAPTCHA wajib diisi',
            'captcha_answer.integer' => 'Jawaban CAPTCHA harus berupa angka',
        ]);

        if ((int) $validated['captcha_answer'] !== (int) $request->session()->get('collectiv_captcha_answer')) {
            $this->generateCaptcha();

            return back()
                ->withErrors(['captcha_answer' => 'Jawaban CAPTCHA salah'])
                ->withInput();
        }

        $file = $request->file('ktp');
        $filename = Str::random(48).'.enc';
        $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));

        Storage::disk('local')->put('private/ktp/'.$filename, $encryptedContent);

        Collectiv::create([
            'nama' => Str::upper($validated['nama']),
            'nik' => $validated['nik'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'ao' => $validated['ao'],
            'jenis_permohonan_kredit' => $validated['jenis_permohonan_kredit'],
            'ktp_file' => $filename,
            'status' => Collectiv::STATUS_BELUM_DIVERIFIKASI,
        ]);

        $request->session()->forget('collectiv_captcha_answer');

        return back()->with('success', 'Data Collectiv berhasil dikirim.');
    }

    private function generateCaptcha(): array
    {
        $firstNumber = random_int(1, 20);
        $secondNumber = random_int(1, 20);

        session(['collectiv_captcha_answer' => $firstNumber + $secondNumber]);

        return [
            'question' => $firstNumber.' + '.$secondNumber,
        ];
    }
}
