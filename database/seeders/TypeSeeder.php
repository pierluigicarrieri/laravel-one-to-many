<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{

    private $types = [
        [
            'name' => 'Front-End'
        ],
        [
            'name' => 'Back-End'
        ],
    ];


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        foreach ($this->types as $type) {
            $newType = new Type();
            $newType->name = $type['name'];
            $newType->save();
        }
    }
}
