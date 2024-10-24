<?php

use Livewire\Volt\Component;
use App\Models\Ship;
use Carbon\Carbon;
new class extends Component {
    public $listOfShips = [];
    public $listOfBorrowedShips = [];
    public function mount(): void
    {
        $localBorrowedShips = auth()->user()->borrowedShips()->with('ship')->whereNull('returned_at')->get();
        $this->listOfBorrowedShips = $localBorrowedShips->map(function($borrowedShip) {
            return [
                'id' => $borrowedShip->id,
                'ship_name' => $borrowedShip->ship->ship_name,
                'price' => $borrowedShip->ship->price,
                'total_price'=>$borrowedShip->total_price,
                'returned_date'=>Carbon::parse($borrowedShip->returned_date)->format('d-m-Y'),
                'borrowed_date'=>Carbon::parse($borrowedShip->created_at)->format('d-m-Y'),
            ];
        });
    }
    public function returnBorrowedShip(int $id)
    {
        try 
        {
            $borrowedShip = auth()->user()->borrowedShips()->find($id);
            $borrowedShip->returned_at = Carbon::now();
            $borrowedShip->save();
            $borrowedUser = Auth::user();
            $borrowedUser->balance -= $borrowedShip->total_price;
            $borrowedUser->balance -= $borrowedShip->penalty_price;
            $borrowedUser->save();
            $ship = Ship::find($borrowedShip->ship_id);
            $ship->available_unit += 1;
            $ship->borrowed_unit -= 1;
            $ship->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}; ?>

<div x-data="kapal" x-init="initData()" x-cloak>
    <div id="wrapper"></div>
    @script
        <script>
            Alpine.data('kapal', () => {
                        return {
                            async initData() {
                                const rawData = @json($listOfBorrowedShips);
                                const dataWithNumber = rawData.map((data, index) => {
                                    return {
                                        ...data,
                                        no: index + 1
                                    }
                                });
                                console.log(dataWithNumber);
                                new gridjs.Grid({
                                        columns: [
                                            "no", "ship_name", {name:"price",formatter:(cell,row)=>`Rp.${row.cells[2].data.toLocaleString('id-ID')}`},"borrowed_date","returned_date", {
                                                name: 'Sewa',
                                                formatter: (cell, row) => {
                                                    return gridjs.h('button', {
                                                        className: 'py-2 mb-4 px-4 border rounded-md text-white bg-green-600',
                                                        onClick: async () => {
                                                            if (confirm(
                                                                    'Are you sure you want to return this ship?'
                                                                    )) 
                                                            {
                                                                const res = await $wire.returnBorrowedShip(dataWithNumber[row.cells[0].data-1].id);
                                                                if(res) {
                                                                    alert('Ship returned successfully');
                                                                    location.reload();
                                                                } else {
                                                                    alert('Failed to borrow ship');
                                                                }
                                                            }
                                                        }
                                                    }, 'Return Ship');
                                                }
                                            }
                                            ],
                                            sort: true,
                                            pagination: true,
                                            fixedHeader: true,
                                            data: dataWithNumber,
                                            search: true,
                                        }).render(document.getElementById("wrapper"));
                                },
                            }
                        })
        </script>
    @endscript
</div>
