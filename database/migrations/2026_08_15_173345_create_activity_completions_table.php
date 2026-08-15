<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('activity_completions',function(Blueprint $t){$t->id();$t->foreignId('enrollment_id')->constrained()->cascadeOnDelete();$t->foreignId('module_activity_id')->constrained()->cascadeOnDelete();$t->timestamp('completed_at');$t->timestamps();$t->unique(['enrollment_id','module_activity_id']);});} public function down():void{Schema::dropIfExists('activity_completions');} };
