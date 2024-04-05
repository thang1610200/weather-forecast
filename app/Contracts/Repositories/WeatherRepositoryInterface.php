<?php

namespace App\Contracts\Repositories;

use Illuminate\Http\Request;

interface WeatherRepositoryInterface {
    public function showInfor(Request $request);
    public function sendNotify(Request $request);
    public function confirmEmail(Request $request);
    public function sendWeatherDaily();
    public function weatherDetail(Request $request);
    public function weatherDetailAjax(Request $request);
}