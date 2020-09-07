<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['timesheet_id', 'user_id','comment'];

    public function timesheet()
    {
        return $this->belongsToMany('App\Models\Timesheet');
    }
}
