<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MainService extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'status'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
