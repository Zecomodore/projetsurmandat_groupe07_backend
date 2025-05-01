<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Intervention;
use Illuminate\Support\Facades\Redis;

use function Laravel\Prompts\select;

class ListeUtilisateur extends Model
{
    use HasFactory;
    protected $fillable = ['lsu_no','lsu_present','lsu_int_no','lsu_uti_no'];
    protected $table = 'lst_utilisateur';
    protected $primaryKey = 'lsu_no';
    public $timestamps = false;


    public static function get_personne_intervenant($idIntervention){
        $lst_utilisateur = DB::table ('lst_utilisateur')
        ->join('utilisateur', 'lst_utilisateur.lsu_uti_no', '=', 'utilisateur.uti_no')
        ->where('lst_utilisateur.lsu_int_no', $idIntervention)
        ->where('lst_utilisateur.lsu_present', true)
        ->select('utilisateur.uti_nom', 'utilisateur.uti_prenom')
        ->get();
        return $lst_utilisateur;
    }


    public static function ajout_personne_intervenant(Request $request)
{
    // Étape 1 : Trouver uti_no à partir de use_uti_no
    $utilisateur = Utilisateur::where('uti_use_id', $request->uti_use_id)->first();
    $intervention = Intervention::where('int_no', $request->lsu_int_no)
    ->where('int_en_cours', true)
    ->first();

    if (!$utilisateur) {
        abort(404, 'Utilisateur non trouvé');
    }

    if (!$intervention) {
        abort(404, 'Intervention non trouvée');
    }

    // Étape 2 : Vérifier si déjà présent dans ListeUtilisateur
    $lst_utilisateur = ListeUtilisateur::where('lsu_int_no', $intervention->int_no)
                        ->where('lsu_uti_no', $utilisateur->uti_no)
                        ->first();

    if ($lst_utilisateur) {
        // Mise à jour de lsu_present à true
        $lst_utilisateur->lsu_present = true;
        $lst_utilisateur->save();
    } else {
        // Sinon on crée un nouveau
        $lst_utilisateur = new ListeUtilisateur();
        $lst_utilisateur->lsu_int_no = $intervention->int_no;
        $lst_utilisateur->lsu_uti_no = $utilisateur->uti_no;
        $lst_utilisateur->lsu_present = true;
        $lst_utilisateur->save();
    }

    return $lst_utilisateur;
}

    

    public static function suprimer_intervention(Request $request){
        $utilisateur = Utilisateur::where('uti_use_id', $request->uti_use_id)->first();
        $lst_utilisateur = ListeUtilisateur::where('lsu_int_no', $request->lsu_int_no)
        ->where('lsu_uti_no', $utilisateur->uti_no)
        ->first();
        $lst_utilisateur->lsu_present = false;
        $lst_utilisateur->save();
        return $lst_utilisateur;
    }

    public static function get_etat_personne(Request $request){
        $utilisateur = Utilisateur::where('uti_use_id', $request->uti_use_id)->first();

        // Si l'utilisateur n'existe pas, renvoyer une erreur ou false
        if (!$utilisateur) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        // Vérifier la correspondance dans `ListeUtilisateur`
        $lst_utilisateur = ListeUtilisateur::where('lsu_int_no', $request->lsu_int_no)
            ->where('lsu_uti_no', $utilisateur->uti_no)
            ->where('lsu_present', true)
            ->first();

        if (!$lst_utilisateur) {
            return false;
        }

        return true;
    }

    public static function get_est_en_intervention_utilisateur(Request $request){
        $utilisateur = Utilisateur::where('uti_use_id', $request->uti_use_id)->first();
    
        if (!$utilisateur) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }
    
        $lst_utilisateur = ListeUtilisateur::where('lsu_uti_no', $utilisateur->uti_no)
            ->where('lsu_present', true)
            ->first();
    
        if (!$lst_utilisateur) {
            return response()->json(['resultat' => false]);
        }
    
        return response()->json([
            'resultat' => true,
            'lsu_int_no' => $lst_utilisateur->lsu_int_no
        ]);
    }
}
