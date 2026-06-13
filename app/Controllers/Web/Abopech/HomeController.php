<?php

declare(strict_types=1);

namespace App\Controllers\Web\Abopech;

use App\Controllers\BaseController;
use App\Models\Abopech\RegionModel;
use App\Models\Abopech\TribunalModel;
use App\Services\Abopech\AbogadoBusquedaService;

final class HomeController extends BaseController
{
    public function index()
    {
        return view('abopech/home');
    }
}
