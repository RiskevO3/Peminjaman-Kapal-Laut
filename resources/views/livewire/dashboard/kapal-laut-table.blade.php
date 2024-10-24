<?php

use Livewire\Volt\Component;
use App\Models\Ship;
use Carbon\Carbon;
new class extends Component {
    public $listOfShips = [];
    public int $totalBorrowedShip = 0;
    public int $balance = 0;
    public function mount(): void
    {
        $localShips = Ship::with('categories')->get();
        $this->balance = auth()->user()->balance;
        $this->listOfShips = $localShips->map(function ($ship) {
            return [
                'id' => $ship->id,
                'ship_name' => $ship->ship_name,
                'available_unit' => $ship->available_unit,
                'price' => $ship->price,
                'penalty_fee' => $ship->penalty_fee,
                'list_category' => $ship->categories->pluck('name')->toArray(),
            ];
        });
        $this->totalBorrowedShip = auth()->user()->borrowedShips()->whereNull('returned_at')->count();
    }
    public function borrowShip(int $id)
    {
        try {
            $user = Auth::user();
            $ship = Ship::find($id);
            if($user->balance < $ship->price) {
                return 'Saldo anda tidak cukup';
            }
            // total of current user borrowed ship
            $totalBorrowedShip = auth()->user()->borrowedShips()->whereNull('returned_at')->count();
            if ($totalBorrowedShip + 1 > 2) {
                return 'Anda sudah meminjam 2 kapal';
            }
            $relationBorrowedShip = auth()
                ->user()
                ->borrowedShips()
                ->create([
                    'ship_id' => $ship->id,
                    'total_price' => $ship->price,
                    'returned_date' => Carbon::now()->addDays(5),
                ]);
            $relationBorrowedShip->save();
            $ship->available_unit -= 1;
            $ship->borrowed_unit += 1;
            $ship->save();
            $user->balance -= $ship->price;
            $user->save();
            return 'Berhasil meminjam kapal';
        } catch (\Throwable $th) {
            return 'Gagal meminjam kapal';
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
                        new gridjs.Grid({
                            columns: [
                                "id",
                                "ship_name",
                                {
                                    name: 'list_category',
                                    formatter: (cell, row) => {
                                        return cell.join(', ');
                                    }
                                },
                                "available_unit",
                                {
                                    name: "price",
                                    formatter: (cell, row) => `Rp.${cell.toLocaleString('id-ID')}`
                                },
                                {
                                    name: 'Sewa',
                                    formatter: (cell, row) => {
                                        const classNameColor = row.cells[3].data > 0 ?
                                            'cursor-pointer bg-blue-600' : 'cursor-not-allowed bg-gray-400';
                                        return gridjs.h('div', {
                                            className: `text-center py-2 mb-4 px-4 border rounded-md text-white ${classNameColor}`,
                                            onClick: async () => {
                                                if(row.cells[3].data == 0) {
                                                    return;
                                                }
                                                if ($wire.totalBorrowedShip > 1) {
                                                    Swal.fire("Anda sudah meminjam 2 kapal atau lebih", "", "info");
                                                    return;
                                                }
                                                if($wire.balance < row.cells[4].data) {
                                                    Swal.fire("Saldo anda tidak cukup", "", "info");
                                                    return;
                                                }
                                                if (row.cells[3].data > 0) {
                                                    Swal.fire({
                                                        title: `Apakah anda yakin ingin menyewa kapal ${row.cells[1].data}?`,
                                                        showCancelButton: true,
                                                        confirmButtonText: "Pinjam",
                                                    }).then(async (result) => {
                                                        if (result.isConfirmed) 
                                                        {
                                                            const res = await $wire.borrowShip(row.cells[0].data);
                                                            if (res =="Berhasil meminjam kapal") 
                                                            {
                                                                Swal.fire(res, "","success")
                                                                .then((res)=>location.reload())
                                                                return;
                                                            }
                                                            Swal.fire(res, "", "error")
                                                            .then((res)=>location.reload())
                                                        } 
                                                    });
                                                }
                                            }
                                        }, 'Borrow');
                                    }
                                }
                            ],
                            sort: true,
                            pagination: true,
                            fixedHeader: true,
                            data: @json($listOfShips),
                            search: true,
                        }).render(document.getElementById("wrapper"));
                    },
                }
            })
        </script>
    @endscript
</div>
