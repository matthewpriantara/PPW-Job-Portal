<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'position', 'salary', 'deadline'];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }
}
