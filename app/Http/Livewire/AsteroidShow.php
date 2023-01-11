<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AsteroidShow extends Component
{
    public $start_date;
    public $end_date;
    public $dataDiff;
    public $fastestAsteroid;
    public $closestAsteroid;
    public $averageSize;
    public $loader;
    public $chartData;

    

    public function render()
    {
        if($this->chartData){
            $this->emit('refreshCharts', ['date' => $this->chartData->pluck('date'), 'counts' => $this->chartData->pluck('counts')]);
        }

        return view('livewire.asteroid-show');
    }

    public function submit()
    {        $this->loader = true;

        $start_date = Carbon::parse($this->start_date);
        $end_date = Carbon::parse($this->end_date);

        throw_if($start_date->diff($end_date)->days > 7, ValidationException::withMessages(['date_diff' => 'The difference start and end must be 7 days']));
        
        $this->validate(
            [
                'start_date' => 'required',
                'end_date' => 'required',
            ],
            [
                'start_date.required' => 'Please specify a Start Date.',
                'end_date.required' => 'Please specify a End Date.',
            ],
        );

        $response = Http::get('https://api.nasa.gov/neo/rest/v1/feed?start_date='.$this->start_date.'&end_date='.$this->end_date.'&api_key=7V4OW2yFXgHlzayGTLPKuBSXIXqtYKGUvlmiQbsx');
     
        $collection = collect();
        $this->chartData = collect();
        foreach(collect(Arr::get($response, 'near_earth_objects')) as $date => $value){
            $this->chartData->push([
                'date' => $date,
                'counts' => collect($value)->count()
            ]);

            foreach ($value as $data) {
                $collection->push([
                    'id' => Arr::get($data, 'id'),
                    'date' => $date,
                    'kelometer_per_hour' => Arr::get($data, 'close_approach_data.0.relative_velocity.kilometers_per_hour'),
                    'diameter' => (Arr::get($data, 'estimated_diameter.kilometers.estimated_diameter_min')+Arr::get($data, 'estimated_diameter.kilometers.estimated_diameter_max'))/2,
                    'distance' => Arr::get($data, 'close_approach_data.0.miss_distance.kilometers'),
                ]);
            }
        };

        $this->fastestAsteroid = Arr::get($collection->where('kelometer_per_hour', $collection->pluck('kelometer_per_hour')->max())->first(), 'kelometer_per_hour');
        $this->closestAsteroid= Arr::get($collection->where('distance', $collection->pluck('distance')->min())->first(), 'distance');
        $this->averageSize = $collection->sum('diameter')/$collection->count();

        $this->loader = false;

        $this->emit('refreshCharts', ['date' => $this->chartData->pluck('date'), 'counts' => $this->chartData->pluck('counts')]);

    }
}
