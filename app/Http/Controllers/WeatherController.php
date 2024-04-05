<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\WeatherRepositoryInterface;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    //
    protected $weatherRepository;

    public function __construct(WeatherRepositoryInterface $weatherRepository)
    {
        $this->weatherRepository = $weatherRepository;
    }

    public function show(Request $request) {
        return $this->weatherRepository->showInfor($request);
    }

    public function registerNotify(Request $request) {
        return $this->weatherRepository->sendNotify($request);
    }

    public function confirmEmail(Request $request) {
        return $this->weatherRepository->confirmEmail($request);
    } 

    public function weatherDetail(Request $request) {
        return $this->weatherRepository->weatherDetail($request);
    }

    public function weatherDetailAjax(Request $request) {
        return $this->weatherRepository->weatherDetailAjax($request);
    }
}
