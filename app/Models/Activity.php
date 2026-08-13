<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['module_id','title','type','instructions','points','order_index'];
    public function module(){ return $this->belongsTo(Module::class); }
}
