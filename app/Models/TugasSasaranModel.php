<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasSasaranModel extends Model
{
    protected $table            = 'tugas_sasaran';
    protected $primaryKey       = 'id_sasaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'id_tugas',
        'target_tipe',
        'id_pkl',
        'id_kelompok',
        'id_tim',
    ];
}
