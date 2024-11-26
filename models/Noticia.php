<?php
// example of using model with eloquent
namespace models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $table = 'noticias';
    protected $fillable = [];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
