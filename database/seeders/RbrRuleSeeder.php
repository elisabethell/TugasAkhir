<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RbrRule;

class RbrRuleSeeder extends Seeder
{
    public function run(): void
    {
        RbrRule::seedDefaultRules();
    }
}
