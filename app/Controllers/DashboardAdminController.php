<?php

namespace App\Controllers;

use App\Models\KelompokPklModel;
use App\Models\KategoriModulModel;
use App\Models\TugasModel;

class DashboardAdminController extends BaseController
{
    protected KelompokPklModel   $kelompokModel;
    protected KategoriModulModel $kategoriModulModel;
    protected TugasModel         $tugasModel;

    public function __construct()
    {
        $this->kelompokModel      = new KelompokPklModel();
        $this->kategoriModulModel = new KategoriModulModel();
        $this->tugasModel         = new TugasModel();
    }

    public function index()
    {
        $data = [
            'page_title'  => 'Dashboard Admin',
            'active_menu' => 'dashboard',

            'stats'       => $this->kelompokModel->getDashboardStats(),

            'modulList'   => $this->kategoriModulModel->getForDashboard(),

            'tugasList'   => $this->tugasModel->getDashboardAdmin(),
        ];

        $data['content'] = view('dashboard_admin/dashboard', $data);

        return view('Layouts/dashboard_layout', $data);
    }
}
