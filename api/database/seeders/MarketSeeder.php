<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketSeeder extends Seeder
{
    public function run()
    {
        DB::table('markets')->insert([
            'url' => 'https://www.wildberries.ru/',
            'title' => 'wildberries',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('markets')->insert([
            'url' => 'https://www.ozon.ru/',
            'title' => 'ozon',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
