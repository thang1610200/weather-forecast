@extends('layout')

@section('content')
    <div class="p-4">
        <div class="row">
            <div class="col-3">
                <div>
                    <button class="mb-4 back">Back</button>
                </div>
                <label for="day" class="form-label fw-bold">Date</label>
                <input type="date" class="form-control" id="day" value="{{ $weather['forecast']['forecastday'][0]['date'] }}">
                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-primary enter">Enter</button>
                </div>
            </div>
            <div class="col-9 px-4">
                <div class="card bg-primary">
                    <div class="row text-white" id='current'>
                        <div class="col-md-8">
                            <div class="card-body px-4">
                                <h3 class="card-title">{{ $weather['location']['name'] }} ({{ $weather['location']['country'] }}) ({{ $weather['forecast']['forecastday'][0]['date'] }})</h3>
                                <p class="card-text">Temperature: {{ $weather['forecast']['forecastday'][0]['day']['avgtemp_c'] }}°C</p>
                                <p class="card-text">Wind: {{ $weather['forecast']['forecastday'][0]['day']['maxwind_mph'] }} M/S</p>
                                <p class="card-text">Humidity: {{ $weather['forecast']['forecastday'][0]['day']['avghumidity'] }}%</p>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <div class="d-block text-center">
                                <img src="{{ $weather['forecast']['forecastday'][0]['day']['condition']['icon'] }}" style="height: 100px; width: 100px" />
                                <div>
                                    {{ $weather['forecast']['forecastday'][0]['day']['condition']['text'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-4">
                    <h4 class="fw-bold">Detail</h4>
                </div>
                <div id="next-day" class="mt-3 row g-3">
                    @foreach ($weather['forecast']['forecastday'][0]['hour'] as $item)
                    <div class="col-3">
                        <div class="card bg-secondary">
                            <div class="card-body text-white">
                                <h6 class="card-title">({{ $item['time'] }})</h6>
                                <img src="{{ $item['condition']['icon'] }}" style="height: 100px; width: 90px" />
                                <p class="card-text">Temp: {{ $item['temp_c'] }}°C</p>
                                <p class="card-text">Wind: {{ $item['wind_mph'] }} M/S</p>
                                <p class="card-text">Humidity: {{ $item['humidity'] }}%</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
        $('.back').on('click', function () {
            window.location.href = '/';
        })

        var modal = new bootstrap.Modal(document.getElementById("loading"), {});
        $(document).on('ajaxStart', function() {
            modal.show();
        });

        $(document).on('ajaxComplete', function() {
            modal.hide();
        });

        $('.enter').on('click', function() {
            let searchParams = new URLSearchParams(window.location.search);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: 'weather-detail-ajax',
                type: 'GET',
                data: {
                    location: searchParams.get('location'),
                    dt: new Date($('#day').val()).toLocaleDateString()
                },
                success: function(res) {
                    $("#current").empty();
                    $("#next-day").empty();

                    $("#current").append(`
                        <div class="col-md-8">
                            <div class="card-body px-4">
                                <h3 class="card-title">${ res.location.name } (${ res.location.country }) (${ res.forecast.forecastday[0].date })</h3>
                                <p class="card-text">Temperature: ${ res.forecast.forecastday[0].day.avgtemp_c }°C</p>
                                <p class="card-text">Wind: ${ res.forecast.forecastday[0].day.maxwind_mph } M/S</p>
                                <p class="card-text">Humidity: ${ res.forecast.forecastday[0].day.avghumidity }%</p>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <div class="d-block text-center">
                                <img src="${ res.forecast.forecastday[0].day.condition.icon }" style="height: 100px; width: 100px" />
                                <div>
                                    ${ res.forecast.forecastday[0].day.condition.text }
                                </div>
                            </div>
                        </div>
                    `);

                    res.forecast.forecastday[0].hour.map((item) => {
                        $("#next-day").append(`
                        <div class="col-3">
                            <div class="card bg-secondary">
                                <div class="card-body text-white">
                                    <h6 class="card-title">(${ item.time })</h6>
                                    <img src="${ item.condition.icon }" style="height: 100px; width: 90px" />
                                    <p class="card-text">Temp: ${ item.temp_c }°C</p>
                                    <p class="card-text">Wind: ${ item.wind_mph } M/S</p>
                                    <p class="card-text">Humidity: ${ item.humidity }%</p>
                                </div>
                            </div>
                        </div>
                        `);
                    });

                    var newurl = window.location.protocol + "//" + window.location.host +
                                window.location.pathname +
                                `?location=${searchParams.get('location')}&dt=${new Date($('#day').val()).toLocaleDateString()}`;
                    window.history.pushState({path: newurl}, '', newurl);
                },
                error: function() {
                    window.alert("Something went error!");
                }
            })
        });
    </script>
@stop