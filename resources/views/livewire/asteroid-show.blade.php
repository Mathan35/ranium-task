<div>
    <div class="shadow-md border border-gray-200 rounded-lg px-6 py-6 my-8">
        <form wire:submit.prevent="submit()">

            <div class="sm:flex justify-evenly items-center">
                <div class="">
                    <div class="datepicker relative shadow-sm form-floating xl:w-96" data-mdb-toggle-button="false">
                        <input type="date" wire:model='start_date'
                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                        placeholder="Select a date" />
                        <label for="floatingInput" class="text-gray-700">Select a start date</label>
                    </div>
                    <div class="">
                        @error('start_date') <span class="text-xs text-red-600 py-2 font-sans z-10">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="">
                    <div class="datepicker relative shadow-sm form-floating xl:w-96 focus:ring-0 focus:border-none focus:outline-none" data-mdb-toggle-button="false">
                        <input type="date" wire:model='end_date'
                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                        placeholder="Select a date" />
                        <label for="floatingInput" class="text-gray-700">Select a end date</label>
                    </div>
                    <div>
                        @error('end_date') <span class="text-xs text-red-600 py-2 font-sans z-10">{{ $message }}</span> @enderror
                        @error('date_diff') <span class="text-xs text-red-600 py-2 font-sans z-10">{{ $message }}</span> @enderror
                    </div>
                </div>
                <button wire:click='submit()' wire:loading.attr="disabled" class="button button-blue py-2 px-4" data-ripple-light="true">
                    Submit
                </button>
            </div>

        </form>
    </div>

    <div class="shadow-md border border-gray-200 rounded-lg px-6 py-6 my-8">
    @if(!$fastestAsteroid )
        <p  wire:loading.remove wire:target="submit" class="text-sm font-sans text-gray-600 text-center font-medium">Select the date range then you can see the results in the following....</p> 
    @endif
        <div wire:loading wire:target="submit" class="py-2 px-4 ">
           <div class="flex space-x-2 items-center">
                <svg aria-hidden="true" class="inline w-6 h-6 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg>
                <p class="text-sm font-sans text-center"> Loding...</p> 
           </div>
        </div>
        @if($fastestAsteroid)
         <div class="my-2">
            <h2 class="text-lg text-gray-800 font-sans font-normal">Fastest Asteroid :- <span class="text-gray-800 font-sans font-semibold">{{$fastestAsteroid}} km/h</span></h2>
            <h2 class="text-lg text-gray-800 font-sans font-normal">Closest Asteroid :- <span class="text-gray-800 font-sans font-semibold">{{$closestAsteroid}} km</span></h2>
            <h2 class="text-lg text-gray-800 font-sans font-normal">Average Size of the Asteroids (d) :- <span class="text-gray-800 font-sans font-semibold">{{$averageSize}}</span></h2>
         </div>

          <div class="my-4">
            <h2 class="text-center font-sans text-gray-700 font-bold py-3">The bar chart in the given date range</h2>
            <div class="z-10">
                <canvas id="myChart"></canvas>
            </div>
          </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function myFunction(item, index) {
            return item + ","; 
        }

        window.onload = function() {
            
            Livewire.on('refreshCharts', ({date, counts}) => {
                const ctx = document.getElementById('myChart');
                if (Chart.getChart("myChart")){
                    Chart.getChart("myChart").destroy();
                }
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels:Array.from(date),
                    datasets: [{
                        label: 'No of asteroids',
                        data: Array.from(counts),
                        borderWidth: 1
                    }]
                    },
                    options: {
                        scales: {
                            y: {
                            beginAtZero: true
                            }
                        }
                    }
                });
            });
        };
    </script> 
</div>
