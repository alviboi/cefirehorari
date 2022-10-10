<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('control')->insert([
            'id' => 1,
            'aparell' => 0,
            'temps' => 0,
            'registra' => 1,
            'max_compensacions' => 3
        ]);
    }
}
