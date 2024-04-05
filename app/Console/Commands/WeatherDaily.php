<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\WeatherRepositoryInterface;
use Illuminate\Console\Command;

class WeatherDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:weather';

    protected $weatherRepository;

    public function __construct(WeatherRepositoryInterface $weatherRepository)
    {
        parent::__construct();
        $this->weatherRepository = $weatherRepository;
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->weatherRepository->sendWeatherDaily();
        return Command::SUCCESS;
    }
}
