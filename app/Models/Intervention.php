<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Intervention extends Model
{
    use HasFactory;
    protected $fillable = ['int_no', 'int_date', 'int_description', 'int_Adresse', 'int_en_cours', 'int_commentaire', 'int_heure'];
    protected $table = 'intervention';
    protected $primaryKey = 'int_no';
    public $timestamps = false;


    public static function get_interventions(){
        $interventions = Intervention::where('int_en_cours', true)->get();
        return $interventions;
    }

    public static function create_intervention(Request $request){
        $intervention = new Intervention();
        $intervention->int_date = Carbon::now()->toDateString();
        $intervention->int_description = $request->int_description;
        $intervention->int_Adresse = $request->int_adresse;
        $intervention->int_en_cours = true;
        $intervention->int_commentaire = $request->int_commentaire;
        $intervention->int_heure = Carbon::now()->toTimeString();
        $intervention->save();
        return $intervention;
    }

    public static function finish_intervention(Request $request){
        $intervention = Intervention::find($request->int_no);
        $intervention->int_en_cours = false;
        $intervention->save();
        return $intervention;
    }
}
