<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{

    protected $fillable = [
        'start',
        'end',
        'user_id'
    ];

    public function status()
    {
        return $this->belongsToMany('App\Models\Status');
    }

    public function entry()
    {
        return $this->belongsToMany('App\Models\Entry');
    }

    public function comments()
    {
        return $this->belongsToMany('App\Models\Comment');
    }

    public function hours()
    {
        return $this->belongsToMany('App\Models\Hourtype');
    }

    public function timesheet_hours()
    {
        return $this->belongsToMany('App\Models\HourtypeTimesheet');
    }
}
