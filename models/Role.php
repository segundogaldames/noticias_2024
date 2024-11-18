<?php
// example of using model with eloquent
namespace models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = [];

    #relacion de uno a muchos con usuarios
    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
