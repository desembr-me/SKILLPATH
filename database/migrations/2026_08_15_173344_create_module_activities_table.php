<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('module_activities',function(Blueprint $t){$t->id();$t->foreignId('course_module_id')->constrained()->cascadeOnDelete();$t->string('title');$t->enum('type',['materi','latihan','refleksi'])->default('materi');$t->unsignedSmallInteger('sequence')->default(1);$t->timestamps();});} public function down():void{Schema::dropIfExists('module_activities');} };
