<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'status'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
