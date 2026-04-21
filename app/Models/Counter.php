<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use \App\Traits\LoggedActivity;

    protected $fillable = ['name', 'code', 'status'];
}
