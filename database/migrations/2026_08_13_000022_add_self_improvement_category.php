<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')->updateOrInsert(
            ['slug' => 'self-improvement'],
            [
                'name' => 'Self-Improvement',
                'icon' => '✧',
                'description' => 'Pengembangan diri anak melalui kemampuan sosial, emosional, percaya diri, kemandirian, dan kebiasaan positif.',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]
        );

        DB::table('interests')->updateOrInsert(
            ['slug' => 'pengembangan-diri'],
            [
                'name' => 'Pengembangan Diri',
                'icon' => '✧',
                'description' => 'Percaya diri, mengenali emosi, kemampuan sosial, dan kebiasaan positif.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        $categoryId = DB::table('categories')->where('slug', 'self-improvement')->value('id');
        if ($categoryId && ! DB::table('category_learning_path')->where('category_id', $categoryId)->exists()) {
            DB::table('categories')->where('id', $categoryId)->delete();
        }

        $interestId = DB::table('interests')->where('slug', 'pengembangan-diri')->value('id');
        if ($interestId && ! DB::table('child_interest')->where('interest_id', $interestId)->exists()
            && ! DB::table('interest_learning_path')->where('interest_id', $interestId)->exists()) {
            DB::table('interests')->where('id', $interestId)->delete();
        }
    }
};
