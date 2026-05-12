<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_BELUM_DIVERIFIKASI = 'belum_diverifikasi';

    public const STATUS_TERVERIFIKASI = 'terverifikasi';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'nama',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'ao',
        'jenis_permohonan_kredit',
        'ktp_file',
        'status',
    ];

    protected $appends = [
        'effective_status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function getEffectiveStatusAttribute(): string
    {
        if (
            $this->status === self::STATUS_BELUM_DIVERIFIKASI
            && $this->created_at
            && $this->created_at->lt(now()->subDays(30))
        ) {
            return self::STATUS_EXPIRED;
        }

        return $this->status;
    }

    public function refreshExpiredStatus(): void
    {
        if ($this->effective_status !== self::STATUS_EXPIRED || $this->status === self::STATUS_EXPIRED) {
            return;
        }

        $this->forceFill([
            'status' => self::STATUS_EXPIRED,
        ])->save();
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_BELUM_DIVERIFIKASI => 'Belum Diverifikasi',
            self::STATUS_TERVERIFIKASI => 'Terverifikasi',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }
}
