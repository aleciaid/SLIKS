<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->nama }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">NIK</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->nik }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tempat Lahir</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->tempat_lahir }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Lahir</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->tanggal_lahir?->format('d M Y') }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">AO</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->ao }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Permohonan Kredit</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->jenis_permohonan_kredit }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->created_at?->format('d M Y H:i:s') }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Dibuat</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->created_at?->format('d M Y H:i:s') }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Diupdate</p>
            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $record->updated_at?->format('d M Y H:i:s') }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Preview KTP</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Ditampilkan melalui route admin yang terlindungi.</p>
            </div>
            <a href="{{ route('admin.ktp.show', ['applicant' => $record, 'download' => 1]) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-500">
                Download
            </a>
        </div>

        <img src="{{ route('admin.ktp.show', $record) }}" alt="KTP {{ $record->nama }}" class="max-h-[420px] w-full rounded-lg border border-gray-200 object-contain dark:border-gray-700">
    </div>
</div>
