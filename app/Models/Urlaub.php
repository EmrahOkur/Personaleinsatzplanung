<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urlaub extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable =
    [
    'verfügbare_tage',
    'genommene_tage',
    'verplante_tage',
    'verbleibende_tage',
    'abwesenheitsart',
    'datum_start',
    'zeit_start',
    'datum_ende',
    'zeit_ende',
    'status',
    'genehmigender',
    'kontingentverbrauch',
    'selectedDates'];









   
}
