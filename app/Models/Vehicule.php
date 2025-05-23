<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Vehicule extends Model
{
    use HasFactory;
    protected $fillable = [
        'veh_no', 
        'veh_nom', 
        'veh_disponible',
        'veh_use_id'];
    protected $table = 'vehicule';
    protected $primaryKey = 'veh_no';
    public $timestamps = false;

    public static function creerVehicule(Request $request)
    {
        if (trim($request->password) === '') {
            abort(400, 'Le mot de passe ne peut pas être vide');
        }

        // Créer un utilisateur associé au véhicule
        $user = new User();
        $user->name = $request->role; // Utiliser 'role' au lieu de 'name'
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        // Créer le véhicule
        $vehicule = new Vehicule();
        $vehicule->veh_nom = $request->veh_nom;
        $vehicule->veh_disponible = true; // Par défaut, le véhicule est disponible
        $vehicule->veh_use_id = $user->id; // Associer le véhicule à l'utilisateur créé
        $vehicule->save();

        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Véhicule et User créés avec succès',
            'vehicule' => $vehicule,
            'user' => $user
        ], 201);
    }

    public static function deleteVehicule($id){
        // Trouver le véhicule par son ID
        $vehicule = self::where('veh_no', $id)->first();
    
        if (!$vehicule) {
            return response()->json(['message' => 'Véhicule non trouvé'], 404);
        }
    
        // Trouver le User associé
        $user = User::where('id', $vehicule->veh_use_id)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur associé au véhicule non trouvé'], 404);
        }
    
        // Supprimer le véhicule
        $vehicule->delete();
    
        // Supprimer le User associé
        $user->delete();
    
        return response()->json(['message' => 'Véhicule et utilisateur associé supprimés avec succès'], 200);
    }

    public static function modifierVehicule(Request $request, $id){
        // Trouver le véhicule par son ID
        $vehicule = Vehicule::where('veh_no', $id)->first();
    
        if (!$vehicule) {
            return response()->json(['message' => 'Véhicule non trouvé'], 404);
        }
    
        // Trouver l'utilisateur associé au véhicule
        $user = User::where('id', $vehicule->veh_use_id)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Utilisateur associé au véhicule non trouvé'], 404);
        }
    
        // Mettre à jour les informations du véhicule
        $vehicule->veh_nom = $request->input('veh_nom', $vehicule->veh_nom);
        $vehicule->veh_disponible = $request->input('veh_disponible', $vehicule->veh_disponible);
        $vehicule->save();
    
        // Mettre à jour l'email de l'utilisateur
        $user->email = $request->input('email', $user->email);
        $user->save();
    
        return response()->json([
            'message' => 'Véhicule et utilisateur mis à jour avec succès',
            'vehicule' => $vehicule,
            'user' => $user
        ], 200);
    }

    public static function get_all_vehicules(){
        return DB::table('vehicule')
            ->join('users', 'vehicule.veh_use_id', '=', 'users.id')
            ->select(
                'vehicule.veh_no',
                'vehicule.veh_nom',
                'vehicule.veh_disponible',
                'vehicule.veh_use_id',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->get();
    }
    
    public static function get_vehicule_admin($id){
        return DB::table('vehicule')
            ->join('users', 'vehicule.veh_use_id', '=', 'users.id')
            ->where('vehicule.veh_no', $id)
            ->select(
                'vehicule.veh_no',
                'vehicule.veh_nom',
                'vehicule.veh_disponible',
                'vehicule.veh_use_id',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->first();
    }
    
    public static function get_vehicule($id){
        $vehicule = Vehicule::where('veh_use_id', $id)->get();
        return $vehicule;
    }

    public static function vehicule_indisponible(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_no)->first(); 

        if ($vehicule) {
            $vehicule->veh_disponible = false;
            $vehicule->save();
            return $vehicule;
        }
    }

    public static function vehicule_disponible(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_no)->first(); 

        if ($vehicule) {
            $vehicule->veh_disponible = true;
            $vehicule->save();
            return $vehicule;
        }
    }

    public static function filtrerVehicules($filters){
        $query = DB::table('vehicule')
            ->join('users', 'vehicule.veh_use_id', '=', 'users.id')
            ->select(
                'vehicule.veh_no',
                'vehicule.veh_nom',
                'vehicule.veh_disponible',
                'users.email as user_email'
            );

        // Appliquer les filtres si présents
        if (!empty($filters['veh_no'])) {
            $query->where('vehicule.veh_no', 'like', '%' . $filters['veh_no'] . '%');
        }

        if (!empty($filters['veh_nom'])) {
            $query->where('vehicule.veh_nom', 'like', '%' . $filters['veh_nom'] . '%');
        }

        if (isset($filters['veh_disponible'])) {
            $query->where('vehicule.veh_disponible', $filters['veh_disponible'] === 'true' ? 1 : 0);
        }

        return $query->get();
    }

}
