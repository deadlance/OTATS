<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    public function timesheet()
    {
        return $this->belongsToMany('App\Models\Timesheet');
    }
}
