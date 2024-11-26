<?php
// example of using model with eloquent
namespace models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $fillable = [];

    #relacion de pertenencia con el modelo Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function noticias()
    {
        return $this->hasMany(Noticia::class);
    }
}
