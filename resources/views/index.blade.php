@extends('layout')

@section('content')
    <div class="p-4">
        <div class="row">
            <div class="col-3">
                <label for="city" class="form-label fw-bold">Enter a City Name</label>
                <input type="text" class="form-control" id="city" placeholder="E.g. New York, London, Tokyo">
                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-primary search">Search</button>
                </div>
                <div class="mt-4">
                    <div class="position-relative">
                        <div class="border border-1"></div>
                        <div class="position-relative d-flex justify-content-center position-absolute top-0 start-50 translate-middle">
                            <span class="bg-white px-2">
                                Or
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-secondary button-gps">Use Current Location</button>
                </div>
                <div class="mt-4">
                    <label for="email" class="form-label fw-bold">Sign up to receive notifications</label>
                    <input type="email" class="form-control" id="email" placeholder="E.g. abc@gmail.com">
                    <div class="d-grid mt-4">
                        <button type="button" class="btn btn-primary button-send">Register</button>
                    </div>
                </div>
            </div>
            <div class="col-9 px-4">
                <div class="card bg-primary">
                    <div class="row text-white" id='current'>
                    </div>
                </div>
                <div class="my-4">
                    <h4 class="fw-bold">4-Days Forecase</h4>
                </div>
                <div id="next-day" class="mt-3 row g-3">
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    <button class='load-more'>Load more</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" style="background: none !important;" id="loading" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="background: none !important;">
          <div class="modal-content" style="background: none !important; border: none;">
            <div class="modal-body" style="background: none !important; text-align: center;">
              <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        var modal = new bootstrap.Modal(document.getElementById("loading"), {});
        $(document).on('ajaxStart', function() {
            modal.show();
        });

        $(document).on('ajaxComplete', function() {
            modal.hide();
        });

        function getLocation() {  
            if (navigator.geolocation) {  
                var options = {  
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 10  
                };  
                navigator.geolocation.getCurrentPosition(showPositionSuccess, showPositionError, options);  
            } else {  
                alert("Geolocation không hỗ trợ trên trình duyệt này.");  
            }  
        }

        function getInforWeather(location, days) {
            $.ajax({
                url: `/weather-location`,
                data:{
                    days,
                    location
                },
                type: 'GET',
                success: function (res) {
                    $("#current").empty();
                    $("#next-day").empty();

                    $("#current").append(`
                        <div class="col-md-8">
                            <div class="card-body px-4">
                                <h3 class="card-title"><a class='text-white' href='/weather-detail?location=${res.location.name}&dt=${new Date().toLocaleDateString()}'>${res.location.name}</a> (${res.location.country})</h3>
                                <p class="card-text">Temperature: ${res.current.temp_c}°C</p>
                                <p class="card-text">Wind: ${res.current.wind_mph} M/S</p>
                                <p class="card-text">Humidity: ${res.current.humidity}%</p>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <div class="d-block text-center">
                                <img src="${res.current.condition.icon}" style="height: 100px; width: 100px" />
                                <div>
                                    ${res.current.condition.text}
                                </div>
                            </div>
                        </div>
                    `);

                    res.forecast.forecastday.map((item) => {
                        $("#next-day").append(`
                            <div class="col-3">
                                <div class="card bg-secondary">
                                    <div class="card-body text-white">
                                        <h6 class="card-title">(${item.date})</h6>
                                        <img src="${item.day.condition.icon}" style="height: 100px; width: 90px" />
                                        <p class="card-text">Temp: ${item.day.avgtemp_c}°C</p>
                                        <p class="card-text">Wind: ${item.day.maxwind_mph} M/S</p>
                                        <p class="card-text">Humidity: ${item.day.avghumidity}%</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });

                    var newurl = window.location.protocol + "//" + window.location.host +
                                window.location.pathname +
                                `?location=${location}&day=${days}`;
                    window.history.pushState({path: newurl}, '', newurl);
                },
                error: function() {
                    window.alert('Something went error!');
                }
            });
        }

        function showPositionSuccess(position) {  
            let location = `${position.coords.latitude},${position.coords.longitude}`
            let day = 4;

            getInforWeather(location, day);
        }  

        function showPositionError(error) {
            let location = 'Ho Chi Minh';
            let day = 4;

            getInforWeather(location, day);
            // switch(error.code) {  
            //     case error.PERMISSION_DENIED:  
            //     window.alert( "Người dùng từ chối yêu cầu từ Geolocation.");  
            //     break;  
            // case error.POSITION_UNAVAILABLE:  
            //     window.alert("Không thể tìm ra vị trí của người dùng.");  
            //     break;  
            // case error.TIMEOUT:  
            //     window.alert("Yêu cầu xác nhận từ người dùng hết thời gian chờ");
            //     break;  
            // case error.UNKNOWN_ERROR:  
            //     window.alert("Không tìm ra lỗi.");  
            //     break;  
            // }  
        }

        $(window).on('load', function () {
            getLocation();
        }) 

        $('.search').on('click', function() {
            let searchParams = new URLSearchParams(window.location.search);

            let location = $("#city").val();
            let day = !searchParams.get('day') ? 4 : searchParams.get('day');

            if(!location) {
                return window.alert('Empty input');
            }

            getInforWeather(location, day);
        })

        $(".load-more").on('click', function() {
            let searchParams = new URLSearchParams(window.location.search);

            let location = searchParams.get('location');
            let day = !searchParams.get('day') ? 4 : searchParams.get('day');

            if(day < 14) {
                getInforWeather(location, Number(day) + 4);
            }
        });

        $(".button-gps").on('click', function() {
            getLocation()
        });

        $(".button-send").on('click', function() {
            if(!$("#email").val()) {
                return window.alert("Empty email");
            }

            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '/register-notify',
                        type: "POST",
                        data: {
                            email: $("#email").val(),
                            location: `${position.coords.latitude},${position.coords.longitude}`
                        },
                        dataType: 'json',
                        success: function(res) {
                            window.alert(res.message);
                        },
                        error: function() {
                            window.alert('Something went error!');
                        }
                    });
                }, (err) => {
                    window.alert('Please enable location');
                });
            }
        });
    </script>
@stop