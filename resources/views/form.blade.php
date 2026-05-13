<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Form Pengajuan KTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto flex min-h-screen w-full max-w-5xl items-center justify-center px-4 py-10">
        <section class="grid w-full overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200 md:grid-cols-5">
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-8 text-white md:col-span-2">
                <div class="flex h-full flex-col justify-between gap-10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-100">Pengajuan</p>
                        <h1 class="mt-4 text-4xl font-bold tracking-tight">Form SLIK</h1>
                        <p class="mt-4 text-sm leading-6 text-amber-50">Isi data dengan benar. File KTP akan dienkripsi dan disimpan secara privat.</p>
                    </div>

                </div>
            </div>

            <div class="p-6 sm:p-8 md:col-span-3">
                <div class="mb-6 grid grid-cols-2 rounded-2xl bg-slate-100 p-1 text-sm font-semibold text-slate-600">
                    <a href="{{ route('form-ktp.index') }}" class="rounded-xl bg-white px-4 py-3 text-center text-slate-950 shadow-sm">
                        Form KTP
                    </a>
                    <a href="{{ route('form-collectiv.index') }}" class="rounded-xl px-4 py-3 text-center transition hover:bg-white hover:text-slate-950">
                        Form Collectiv
                    </a>
                </div>

                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold">Periksa kembali data Anda:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="ktpForm" action="{{ url('/form-ktp') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="nama" class="block text-sm font-semibold text-slate-700">Nama</label>
                        <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-semibold text-slate-700">NIK</label>
                        <input id="nik" name="nik" type="text" inputmode="numeric" pattern="[0-9]{16}" maxlength="16" value="{{ old('nik') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-semibold text-slate-700">Tempat Lahir</label>
                            <input id="tempat_lahir" name="tempat_lahir" type="text" value="{{ old('tempat_lahir') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-semibold text-slate-700">Tanggal Lahir</label>
                            <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-semibold text-slate-700">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                                <option value="">Pilih</option>
                                <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="ao" class="block text-sm font-semibold text-slate-700">AO</label>
                            <input id="ao" name="ao" type="text" value="{{ old('ao') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                    </div>

                    <div>
                        <label for="jenis_permohonan_kredit" class="block text-sm font-semibold text-slate-700">Jenis Permohonan Kredit</label>
                        <input id="jenis_permohonan_kredit" name="jenis_permohonan_kredit" type="text" value="{{ old('jenis_permohonan_kredit') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                    </div>

                    <div>
                        <label for="ktp" class="block text-sm font-semibold text-slate-700">Upload KTP</label>
                        <input id="ktpOriginal" type="file" accept="image/jpeg,image/png,image/webp" required class="mt-2 w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm outline-none transition file:mr-4 file:rounded-lg file:border-0 file:bg-amber-500 file:px-4 file:py-2 file:font-semibold file:text-white hover:bg-slate-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        <input id="ktp" name="ktp" type="file" class="hidden">
                        <div id="fileSizeInfo" class="mt-2 hidden text-xs text-slate-500"></div>
                        <img id="preview" class="mt-4 hidden max-h-64 rounded-2xl border border-slate-200 object-contain shadow-sm" alt="Preview KTP">
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <label for="captcha_answer" class="block text-sm font-semibold text-slate-700">Verifikasi CAPTCHA</label>
                        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="rounded-xl bg-white px-5 py-3 text-center text-m font-bold tracking-wide text-slate-950 shadow-sm ring-1 ring-amber-100">
                                {{ $captchaQuestion }} =
                            </div>
                            <input id="captcha_answer" name="captcha_answer" type="number" inputmode="numeric" value="{{ old('captcha_answer') }}" required placeholder="Jawaban" class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>

                    </div>

                    <button id="submitButton" type="submit" class="flex w-full items-center justify-center gap-3 rounded-xl bg-slate-950 px-5 py-3 font-semibold text-white shadow-lg shadow-slate-300 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-70">
                        <svg id="spinner" class="hidden h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="buttonText">Kirim Pengajuan</span>
                    </button>
                </form>
            </div>
        </section>
    </main>

    <script>
        const originalInput = document.getElementById('ktpOriginal');
        const hiddenInput = document.getElementById('ktp');
        const preview = document.getElementById('preview');
        const fileSizeInfo = document.getElementById('fileSizeInfo');
        const form = document.getElementById('ktpForm');
        const button = document.getElementById('submitButton');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('buttonText');

        const MAX_SIZE_KB = 4096;
        const MAX_DIMENSION = 2400;

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(2) + ' MB';
        }

        function compressImage(file) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    let { width, height } = img;

                    if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
                        const ratio = Math.min(MAX_DIMENSION / width, MAX_DIMENSION / height);
                        width = Math.round(width * ratio);
                        height = Math.round(height * ratio);
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.85;
                    const tryCompress = () => {
                        canvas.toBlob((blob) => {
                            if (blob.size > MAX_SIZE_KB * 1024 && quality > 0.2) {
                                quality -= 0.1;
                                tryCompress();
                            } else {
                                resolve(blob);
                            }
                        }, 'image/jpeg', quality);
                    };
                    tryCompress();
                };
                img.src = URL.createObjectURL(file);
            });
        }

        originalInput.addEventListener('change', async () => {
            const file = originalInput.files[0];
            if (!file) {
                preview.classList.add('hidden');
                preview.removeAttribute('src');
                fileSizeInfo.classList.add('hidden');
                return;
            }

            const originalSize = file.size;
            fileSizeInfo.classList.remove('hidden');

            if (originalSize > MAX_SIZE_KB * 1024) {
                fileSizeInfo.innerHTML = '<span class="text-amber-600 font-medium">⏳ Mengkompresi gambar dari ' + formatSize(originalSize) + '...</span>';

                const compressedBlob = await compressImage(file);
                const compressedFile = new File([compressedBlob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });

                const dt = new DataTransfer();
                dt.items.add(compressedFile);
                hiddenInput.files = dt.files;

                fileSizeInfo.innerHTML = '<span class="text-emerald-600 font-medium">✅ Dikompresi: ' + formatSize(originalSize) + ' → ' + formatSize(compressedBlob.size) + '</span>';
                preview.src = URL.createObjectURL(compressedBlob);
            } else {
                const dt = new DataTransfer();
                dt.items.add(file);
                hiddenInput.files = dt.files;

                fileSizeInfo.innerHTML = '<span class="text-emerald-600 font-medium">✅ Ukuran OK: ' + formatSize(originalSize) + '</span>';
                preview.src = URL.createObjectURL(file);
            }
            preview.classList.remove('hidden');
        });

        form.addEventListener('submit', (e) => {
            if (!hiddenInput.files || hiddenInput.files.length === 0) {
                e.preventDefault();
                fileSizeInfo.classList.remove('hidden');
                fileSizeInfo.innerHTML = '<span class="text-red-600 font-medium">⚠️ Pilih file KTP terlebih dahulu</span>';
                return;
            }
            button.disabled = true;
            spinner.classList.remove('hidden');
            buttonText.textContent = 'Mengirim...';
        });
    </script>
</body>
</html>
