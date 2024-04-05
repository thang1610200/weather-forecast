<?php

namespace App\Repositories;

use App\Contracts\Repositories\WeatherRepositoryInterface;
use App\Jobs\SendMail;
use App\Jobs\SendWeather;
use App\Models\Notification;
use Database\Factories\NotificationFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WeatherRepository implements WeatherRepositoryInterface {
    //Lấy thông tin thời tiết
    public function showInfor(Request $request)
    {
        $apiKey = env('API_KEY_WEATHER');

        $day = $request['days'];

        $q = !$request['location'] ? 'Ho Chi Minh' : $request['location']; // Nếu người dùng không cấp quyền vị trí của google thì gán mặc định là Hồ Chí Minh

        $url = 'https://api.weatherapi.com/v1/forecast.json?q='.$q . '&days='. $day . '&key=' . $apiKey;

        $response = Http::get($url);

        return response()->json($response->json(),$response->status());
    }

    public function sendNotify(Request $request)
    {
        $email = $request['email'];
        $location = $request['location'];
        $user = Notification::where('email', $email)->first();

        // Khoảng thời gian để gửi xác nhận lại email là 5p
        if(!$user) { //Nếu người dùng không tồn tại trong DB
            $code = Str::random(20);

            $notifycation = new Notification();
            $notifycation->email = $email;
            $notifycation->location = $location;
            $notifycation->code = $code;

            $notifycation->save();

            $mailData = [
                'url' => url('/verify'.'?token=' .$code . '&email=' . $email)
            ];
    
            dispatch(new SendMail($email, $mailData));

            return response()->json([
                'message' => 'Mail send sucess'
            ], 200);
        }
        else if ($user->isVerify){ // nếu email đã xác nhận r
            return response()->json([
                'message' => 'Email confirmed'
            ], 200);
        }
        else if((strtotime(now()) - strtotime($user->updated_at)) / 60 <= 5){  // nếu khoảng thời gian gửi email tiếp theo dưới 5p
            return response()->json([
                'message' => 'Please check your email'
            ], 200);
        }
        else if((strtotime(now()) - strtotime($user->updated_at)) / 60 > 5) { // nếu khoảng thời gian gửi email tiếp theo trên 5p
            $code = Str::random(20);

            Notification::where('email', $email)->update([
                'code' => $code
            ]);

            $mailData = [
                'url' => url('/verify'.'?token=' .$code . '&email=' . $email)
            ];
    
            dispatch(new SendMail($email, $mailData));

            return response()->json([
                'message' => 'Mail send sucess'
            ], 200);
        }
    }

    public function confirmEmail(Request $request) 
    {
        $email = $request->query('email');
        $code = $request->query('token');

        $notify = Notification::where([
            'email' => $email,
            'code' => $code
        ])->first();

        if(!$notify || (strtotime(now()) - strtotime($notify->updated_at)) / 60 > 5) {
            abort(404);
        }
        else {
            Notification::where([
                'email' => $email,
                'code' => $code
            ])->update([
                'isVerify' => true
            ]);

            return redirect('/');
        }
    }

    public function sendWeatherDaily() 
    {
        $notify = Notification::where('isVerify', true)->get();

        $apiKey = env('API_KEY_WEATHER');

        foreach ($notify as $item) {
            $q = $item->location;

            $url = 'https://api.weatherapi.com/v1/current.json?q='.$q . '&key=' . $apiKey;

            $response = Http::get($url);

            $data = $response->json();

            $mailData = [
                'location' => $data['location']['name'],
                'country' => $data['location']['country'],
                'temp' => $data['current']['temp_c'],
                'wind' => $data['current']['wind_mph'],
                'humidity' => $data['current']['humidity']
            ];

            dispatch(new SendWeather($item->email, $mailData));
        }
    }

    public function weatherDetail(Request $request)
    {
        $apiKey = env('API_KEY_WEATHER');
        $location = $request->query('location');
        $date = date_format(date_create($request->query('dt')),'Y-d-m');

        $url = 'https://api.weatherapi.com/v1/history.json?q='.$location . '&dt='. $date . '&key=' . $apiKey;

        $response = Http::get($url);

        if($response->status() !== 200) {
            return abort(404);
        }

        return view('weather-deatail',[
            'weather' => $response->json()
        ]);
    }

    public function weatherDetailAjax(Request $request)
    {
        $apiKey = env('API_KEY_WEATHER');
        $location = $request['location'];
        $date = date_format(date_create($request['dt']),'Y-d-m');

        $url = 'https://api.weatherapi.com/v1/history.json?q='.$location . '&dt='. $date . '&key=' . $apiKey;

        $response = Http::get($url);

        return response()->json($response->json(), $response->status());
    }
}