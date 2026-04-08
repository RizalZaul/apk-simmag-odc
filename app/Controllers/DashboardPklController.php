<?php

namespace App\Controllers;

use App\Models\KategoriModulModel;
use App\Models\TugasModel;

class DashboardPklController extends BaseController
{
    protected KategoriModulModel $kategoriModulModel;
    protected TugasModel         $tugasModel;

    public function __construct()
    {
        $this->kategoriModulModel = new KategoriModulModel();
        $this->tugasModel         = new TugasModel();
    }

    public function index()
    {
        $idPkl      = (int) session()->get('id_pkl');
        $idKelompok = (int) session()->get('id_kelompok');

        $data = [
            'page_title'  => 'Dashboard PKL',
            'active_menu' => 'dashboard',

            'statsT'      => $this->tugasModel->getStatsPkl($idPkl, $idKelompok),

            'modulList'   => $this->kategoriModulModel->getForDashboard(),

            'tugasList'   => $this->tugasModel->getDashboardPkl($idPkl, $idKelompok),
        ];

        $data['content'] = view('dashboard_pkl/dashboard', $data);

        return view('Layouts/dashboard_layout', $data);
    }
}
