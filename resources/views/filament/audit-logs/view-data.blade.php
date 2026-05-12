@php
    use Illuminate\Support\Str;

    $old = $record->old_values ?? [];
    $new = $record->new_values ?? [];
    $keys = collect(array_keys(array_merge($old, $new)))
        ->reject(fn ($key) => in_array($key, ['updated_at', 'created_at', 'deleted_at', 'remember_token', 'password']))
        ->values();

    $formatValue = function ($value) {
        if (is_null($value)) {
            return '—';
        }
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        $string = (string) $value;
        return Str::limit($string, 200);
    };
@endphp

<div class="space-y-4 text-sm">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-gray-500">Waktu</div>
            <div class="font-medium">{{ $record->created_at?->format('d M Y H:i:s') }}</div>
        </div>
        <div>
            <div class="text-gray-500">Operator</div>
            <div class="font-medium">{{ $record->user_name ?? '—' }} (ID: {{ $record->user_id ?? '—' }})</div>
        </div>
        <div>
            <div class="text-gray-500">Aksi</div>
            <div class="font-medium uppercase">{{ str_replace('_', ' ', $record->action) }}</div>
        </div>
        <div>
            <div class="text-gray-500">Data</div>
            <div class="font-medium">{{ class_basename($record->model_type ?? '-') }} #{{ $record->model_id ?? '-' }}</div>
        </div>
        <div>
            <div class="text-gray-500">IP Address</div>
            <div class="font-medium">{{ $record->ip_address ?? '—' }}</div>
        </div>
        <div>
            <div class="text-gray-500">User Agent</div>
            <div class="font-medium break-all">{{ $record->user_agent ?? '—' }}</div>
        </div>
    </div>

    <div>
        <div class="text-gray-500 mb-2">Perubahan Data</div>
        @if ($keys->isEmpty())
            <div class="text-gray-500 italic">Tidak ada detail perubahan.</div>
        @else
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Field</th>
                            <th class="px-3 py-2 text-left font-semibold">Dari</th>
                            <th class="px-3 py-2 text-left font-semibold">Ke</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($keys as $key)
                            @php
                                $oldVal = $old[$key] ?? null;
                                $newVal = $new[$key] ?? null;
                                $changed = $oldVal !== $newVal;
                            @endphp
                            <tr class="border-t {{ $changed ? 'bg-amber-50 dark:bg-amber-900/20' : '' }}">
                                <td class="px-3 py-2 font-medium align-top">{{ $key }}</td>
                                <td class="px-3 py-2 align-top text-rose-600 whitespace-pre-wrap break-all">{{ $formatValue($oldVal) }}</td>
                                <td class="px-3 py-2 align-top text-emerald-600 whitespace-pre-wrap break-all">{{ $formatValue($newVal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
