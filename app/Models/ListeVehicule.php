<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Vehicule;
use App\Models\Intervention;

class ListeVehicule extends Model
{
    use HasFactory;
    protected $fillable = ['lsv_no','lsv_depart','lsv_arrivee','lsv_present','lsv_veh_no','lsv_int_no'];
    public $timestamps = false;
    protected $table = 'lst_vehicule';
    protected $primaryKey = 'lsv_no';


    public static function get_vehicules_par_intervention($idIntervention){
        return DB::table('lst_vehicule')
            ->join('vehicule', 'lst_vehicule.lsv_veh_no', '=', 'vehicule.veh_no')
            ->where('lst_vehicule.lsv_int_no', $idIntervention)
            ->select('vehicule.veh_nom', 'lst_vehicule.lsv_depart', 'lst_vehicule.lsv_arrivee', 'lst_vehicule.lsv_present')
            ->get();
    }

    public static function get_vehicule_intervention($idIntervention){
        $lst_vehicule = DB::table ('lst_vehicule')
        ->join('vehicule', 'lst_vehicule.lsv_veh_no', '=', 'vehicule.veh_no')
        ->where('lst_vehicule.lsv_int_no', $idIntervention)
        ->where('lst_vehicule.lsv_present', true)
        ->select('vehicule.veh_nom')
        ->get();
        return $lst_vehicule;
    }


    public static function ajout_vehicule_intervenant(Request $request){
        // Étape 1 : Trouver veh_no à partir de veh_use_id
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_id)->first();
        $intervention = Intervention::where('int_no', $request->lsv_int_no)
        ->where('int_en_cours', true)
        ->first();

        if (!$intervention) {
            abort(404, 'Intervention non trouvée');
        }
        
        // Étape 2 : Ajouter la personne dans ListeVehicule
        $lst_vehicule = new ListeVehicule();
        $lst_vehicule->lsv_depart = now(); // On peut utiliser la fonction now() pour obtenir l'heure actuelle
        $lst_vehicule->lsv_present = true;
        $lst_vehicule->lsv_int_no = $intervention->int_no;
        $lst_vehicule->lsv_veh_no = $vehicule->veh_no; // On utilise veh_no trouvé
        $lst_vehicule->save();

        $vehicule->veh_disponible = false;
        $vehicule->save();

    
        
        return $lst_vehicule;
    
    }

    public static function mettre_arrive(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_id)->first();
        $lst_vehicule = ListeVehicule::where('lsv_int_no', $request->lsv_int_no)
        ->where('lsv_veh_no', $vehicule->veh_no)
        ->where('lsv_present', true)
        ->first();
        $lst_vehicule->lsv_arrivee = now();
        $lst_vehicule->save();
        return $lst_vehicule;
    }

    public static function mettre_fin_intervention(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_id)->first();
        $lst_vehicule = ListeVehicule::where('lsv_int_no', $request->lsv_int_no)
        ->where('lsv_veh_no', $vehicule->veh_no)
        ->where('lsv_present', true)
        ->first();
        $lst_vehicule->lsv_present = false;
        $lst_vehicule->save();

        $vehicule->veh_disponible = true;
        $vehicule->save();
        return $lst_vehicule;
    }

    public static function get_etat_vehicule(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_id)->first();

        // Si l'utilisateur n'existe pas, renvoyer une erreur ou false
        if (!$vehicule) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        // Vérifier la correspondance dans `ListeVehicule`
        $lst_vehicule = ListeVehicule::where('lsv_int_no', $request->lsv_int_no)
            ->where('lsv_veh_no', $vehicule->veh_no)
            ->where('lsv_present', true)
            ->first();

        if (!$lst_vehicule) {
            return false;
        }
        return true;
    }

    public static function get_est_en_intervention_vehicule(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_id)->first();
    
        if (!$vehicule) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }
    
        $lst_vehicule = ListeVehicule::where('lsv_veh_no', $vehicule->veh_no)
            ->where('lsv_present', true)
            ->first();
    
        if (!$lst_vehicule) {
            return response()->json(['resultat' => false]);
        }
    
        return response()->json([
            'resultat' => true,
            'lsv_int_no' => $lst_vehicule->lsv_int_no
        ]);
    }
    
}
