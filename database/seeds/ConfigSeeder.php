<?php

use App\Config as AppConfig;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Config::create([
            'key'  => "help",
            'value' => 'This is helper',
        ]);
    }
}
