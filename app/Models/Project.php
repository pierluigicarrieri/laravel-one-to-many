<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type_id',
        'description',
        'image',
        'publication_date',
        'technologies_used',
        'git_link'
    ];

    protected $casts = [
        'publication_date' => 'date:Y-m-d',
    ];

    public function type() {
        return $this->belongsTo(Type::class);
    }

    public function getter_publication_date() {
        return date('d-m-y', strtotime($this->publication_date));
    }

}
