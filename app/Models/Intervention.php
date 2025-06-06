<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class Intervention extends Model
{
    use HasFactory;
    protected $fillable = ['int_no', 'int_date', 'int_description', /*'int_Adresse',*/ 'int_en_cours', /*'int_commentaire',*/ 'int_heure'];
    protected $table = 'intervention';
    protected $primaryKey = 'int_no';
    public $timestamps = false;


    public static function get_interventions(){
        $interventions = Intervention::all();
        //$interventions = Intervention::where('int_en_cours', true)->get();
        return $interventions;
    }

    public static function get_interventions_dispo(){
        $interventions = Intervention::where('int_en_cours', true)->get();
        return $interventions;
    }

    public static function create_intervention(Request $request){
        $validator = Validator::make($request->all(), [
            'int_description' => 'required',
        ]);

        if ($validator->fails()) {
            abort(422, $validator->errors()->first('int_description'));
        }
        else {
            $intervention = new Intervention();
            $intervention->int_date = Carbon::now()->toDateString();
            $intervention->int_description = $request->int_description;
            //$intervention->int_Adresse = '';//$request->int_adresse;
            $intervention->int_en_cours = true;
            //$intervention->int_commentaire = '';//$request->int_commentaire;
            $intervention->int_heure = Carbon::now()->toTimeString();
            $intervention->save();
            return $intervention;
        }
    }

    public static function finish_intervention(Request $request)
{
    // On récupère l'intervention
    $intervention = Intervention::find($request->int_no);
    
    if (!$intervention) {
        return response()->json(['error' => 'Intervention not found'], 404);
    }

    // On met l'intervention à "false"
    $intervention->int_en_cours = false;
    $intervention->save();

    // On met tous les lst_utilisateur.lsu_present à false pour cette intervention
    DB::table('lst_utilisateur')
        ->where('lsu_int_no', $intervention->int_no)
        ->update(['lsu_present' => false]);

    // 1. Mettre tous les lst_vehicule à "non présent"
    DB::table('lst_vehicule')
        ->where('lsv_int_no', $intervention->int_no)
        ->update(['lsv_present' => false]);

    // 2. Mettre les véhicules liés à cette intervention à "disponible"
    DB::table('vehicule')
        ->whereIn('veh_no', function ($query) use ($intervention) {
            $query->select('lsv_veh_no')
                ->from('lst_vehicule')
                ->where('lsv_int_no', $intervention->int_no);
        })
        ->update(['veh_disponible' => true]);


    return $intervention;
}

     public static function filtrerUrgences($filters)
    {
        $query = DB::table('intervention');

        if (!empty($filters['debutDate'])) {
            $query->where('int_date', '>=', $filters['debutDate']);
        }

        if (!empty($filters['finDate'])) {
            $query->where('int_date', '<=', $filters['finDate']);
        }

        if (!empty($filters['debutHeure'])) {
            $query->where('int_heure', '>=', $filters['debutHeure']);
        }

        if (!empty($filters['finHeure'])) {
            $query->where('int_heure', '<=', $filters['finHeure']);
        }

        if (!empty($filters['typeIntervention'])) {
            $query->where('int_description', 'like', '%' . $filters['typeIntervention'] . '%');
        }

        if (isset($filters['enCours'])) {
            $query->where('int_en_cours', $filters['enCours'] === 'true' ? 1 : 0);
        }

        return $query->get();
    }
}
