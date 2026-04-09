<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AppSettingsModel
 *
 * Model untuk tabel `app_settings`.
 * Struktur tabel:
 *   id_setting, key, value, created_at, updated_at
 */
class AppSettingsModel extends Model
{
    protected $table         = 'app_settings';
    protected $primaryKey    = 'id_setting';
    protected $returnType    = 'array';
    protected $protectFields = true;

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'key',
        'value',
        'label',
    ];

    /**
     * Ambil value setting berdasarkan key.
     * Return null jika key tidak ditemukan.
     */
    public function getValue(string $key): ?string
    {
        $row = $this->where('key', $key)->first();
        return $row['value'] ?? null;
    }

    /**
     * Simpan/update value setting berdasarkan key.
     */
    public function setValue(string $key, string $value): void
    {
        $existing = $this->where('key', $key)->first();

        if ($existing) {
            $this->update($existing['id_setting'], ['value' => $value]);
        } else {
            $this->insert(['key' => $key, 'value' => $value]);
        }
    }
}
