<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('exams',function(Blueprint $t){$t->id();$t->foreignId('course_id')->constrained()->cascadeOnDelete();$t->string('title');$t->text('description')->nullable();$t->unsignedTinyInteger('passing_score')->default(75);$t->unsignedTinyInteger('max_attempts')->default(2);$t->boolean('is_active')->default(true);$t->timestamps();});} public function down():void{Schema::dropIfExists('exams');} };
