<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('course_modules',function(Blueprint $t){$t->id();$t->foreignId('course_id')->constrained()->cascadeOnDelete();$t->string('title');$t->text('description')->nullable();$t->unsignedSmallInteger('sequence')->default(1);$t->timestamps();});} public function down():void{Schema::dropIfExists('course_modules');} };
