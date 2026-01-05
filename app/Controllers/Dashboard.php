<?php

namespace App\Controllers;

use App\Models\WaterParameterModel;

class Dashboard extends BaseController
{
    protected $waterParameterModel;

    public function __construct()
    {
        $this->waterParameterModel = new WaterParameterModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Dashboard',
            'parameters' => $this->waterParameterModel->getCurrentParameters(),
            'setpoints' => $this->waterParameterModel->getSetpoints(),
            'fishTypes' => $this->waterParameterModel->getFishTypes(),
            'chartData' => $this->waterParameterModel->getChartData(),
            'notifications' => $this->waterParameterModel->getNotifications()
        ];

        return view('dashboard/index', $data);
    }

    public function setpoint()
    {
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Set-Point',
            'setpoints' => $this->waterParameterModel->getSetpoints(),
            'fishTypes' => $this->waterParameterModel->getFishTypes()
        ];

        return view('dashboard/setpoint', $data);
    }

    public function manualControl()
    {
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Kontrol Manual',
            'controls' => $this->waterParameterModel->getControls()
        ];

        return view('dashboard/manual_control', $data);
    }

    public function grafik()
    {
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Grafik',
            'chartData' => $this->waterParameterModel->getChartData()
        ];

        return view('dashboard/grafik', $data);
    }

    public function notifikasi()
    {
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Notifikasi',
            'notifications' => $this->waterParameterModel->getNotifications()
        ];

        return view('dashboard/notifikasi', $data);
    }
}

