<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('wishlists',function(Blueprint $t){$t->id();$t->foreignId('parent_id')->constrained('users')->cascadeOnDelete();$t->foreignId('course_id')->constrained()->cascadeOnDelete();$t->timestamps();$t->unique(['parent_id','course_id']);});} public function down():void{Schema::dropIfExists('wishlists');} };
