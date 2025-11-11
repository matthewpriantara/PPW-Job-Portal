<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'department'];

    public function vacancies()
    {
        return $this->hasMany(JobVacancy::class);
    }
}
