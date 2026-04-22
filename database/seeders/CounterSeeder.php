<?php
  
namespace Database\Seeders;

use App\Models\Counter;
use Illuminate\Database\Seeder;

class CounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Counter::create([
            'name' => 'Loket 1',
            'code' => 'A',
            'status' => 'active',
        ]);

        Counter::create([
            'name' => 'Loket 2',
            'code' => 'B',
            'status' => 'active',
        ]);
    }
}
