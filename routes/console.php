<?php
use Illuminate\Support\Facades\Artisan;

Artisan::command('skillpath:hello', function () {
    $this->info('SkillPath is ready.');
})->purpose('Check SkillPath installation');
