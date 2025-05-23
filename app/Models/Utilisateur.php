<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;
//use App\Models\Utilisateur;
use App\Models\User;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Auth;

class Utilisateur extends Model
{
    use HasFactory;
    protected $fillable = [
        'uti_no', 
        'uti_nom', 
        'uti_prenom',
        'uti_disponible', 
        'uti_use_id']; 
    protected $table = 'utilisateur';
    protected $primaryKey = 'uti_no';
    public $timestamps = false;


    //Get all pour admin
    public static function get_all_utilisateurs(){
        return DB::table('utilisateur')
            ->join('users', 'utilisateur.uti_use_id', '=', 'users.id')
            ->select(
                'utilisateur.uti_no',
                'utilisateur.uti_nom',
                'utilisateur.uti_prenom',
                'utilisateur.uti_disponible',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->get();
        }
        


    public static function get_utilisateur_Admin($id)
    {
        return DB::table('utilisateur')
            ->join('users', 'utilisateur.uti_use_id', '=', 'users.id')
            ->where('utilisateur.uti_no', $id)
            ->select(
                'utilisateur.uti_no',
                'utilisateur.uti_nom',
                'utilisateur.uti_prenom',
                'utilisateur.uti_disponible',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->first();
    }

    public static function delete_utilisateur($id)
    {
        // Trouver l'utilisateur
        $utilisateur = self::where('uti_no', $id)->first();
        if (!$utilisateur) {
            return false; // Retourne false si l'utilisateur n'est pas trouvé
        }
    
        // Trouver le User associé
        $user = User::where('id', $utilisateur->uti_use_id)->first();
        if (!$user) {
            return false; // Retourne false si le User n'est pas trouvé
        }
    
        // Supprimer l'utilisateur et le User associé
        $utilisateur->delete();
        $user->delete();
    
        return true; // Retourne true si la suppression a réussi
    }

   public static function update_utilisateur(Request $request, $id)
    {
        $utilisateur = self::where('uti_no', $id)->first();

        if (!$utilisateur) {
            return null; // Retourne null si l'utilisateur n'est pas trouvé
        }

        $user = User::where('id', $utilisateur->uti_use_id)->first();

        if (!$user) {
            return null; // Retourne null si le User n'est pas trouvé
        }

        // Mettre à jour les informations de base
        $utilisateur->uti_nom = $request->input('nom', $utilisateur->uti_nom);
        $utilisateur->uti_prenom = $request->input('prenom', $utilisateur->uti_prenom);
        $user->email = $request->input('email', $user->email);
        $user->name = $request->input('name', $user->name);

        // Vérifier et mettre à jour le mot de passe
        if ($request->filled('ancien_mot_de_passe') && $request->filled('nouveau_mot_de_passe')) {
            if (!Hash::check($request->input('ancien_mot_de_passe'), $user->password)) {
                return false; // Ancien mot de passe incorrect
            }

            $user->password = Hash::make($request->input('nouveau_mot_de_passe'));
        }
        else {
            abort(400, 'Le mot de passe ne peut pas être vide');
        }

        $utilisateur->save();
        $user->save();

        return $utilisateur; // Retourne l'utilisateur mis à jour
    }

    public static function creerUtilisateur(Request $request)
    {
        if (trim($request->password) === '') {
            abort(400, 'Le mot de passe ne peut pas être vide');
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $utilisateur = new Utilisateur();
        $utilisateur->uti_nom = $request->nom;
        $utilisateur->uti_prenom = $request->prenom;
        $utilisateur->uti_disponible = true;
        $utilisateur->uti_use_id = $user->id;
        $utilisateur->save();

        return response()->json([
            'message' => 'Utilisateur et User créés avec succès',
            'utilisateur' => $utilisateur,
            'user' => $user
        ], 201);
    }

    public static function get_utilisteur($id){
        $utilisateur = Utilisateur::where('uti_use_id', $id)->get();
        return $utilisateur;
    }
    
    public static function utilisateur_indisponible(Request $request){
        $utilisateur = Utilisateur::where('uti_use_id', $request->uti_use_no)->first(); 

        if ($utilisateur) {
            $utilisateur->uti_disponible = false;
            $utilisateur->save();
            return $utilisateur;
        }
    }

    public static function utilisateur_disponible(Request $request){
        $utilisateur = Utilisateur::where('uti_use_id', $request->uti_use_no)->first(); 

        if ($utilisateur) {
            $utilisateur->uti_disponible = true;
            $utilisateur->save();
            return $utilisateur;
        }
    }    

    public static function code_validation(Request $request){

        $user = User::where('code', $request->code)->first(); 

        if ($user !== null) {
            $user->code = null;
            $user->save();

            // Générer un mot de passe temporaire
            $tempPassword = Str::random(8);
            $user->password = Hash::make($tempPassword);
            $user->save();

            $params = new Request(['email' => $user->email, 'password' => $tempPassword]);
            $authentification = new AuthController();
            return $authentification->auth($params);
        }
        else {
            return response()->json(['error' => 'Code incorrect'], 403);
        }
    }

    public static function changer_mot_de_passe(Request $request){
        if (trim($request->password) === '') {
            abort(400, 'Le mot de passe ne peut pas être vide');
        }
        
        $user = $request->user();

        if ($request->password === $request->password_confirmation) {
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json(['message' => 'Mot de passe modifié avec succès'], 200);
        } 
        else {
            return response()->json(['error' => 'Les mots de passe ne correspondent pas'], 400);
        }
    }

    public static function generateVerificationCode() {
        $digits = random_int(1000, 9999); // 4 chiffres
        $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2)); // 2 lettres
        return $digits . $letters;
    }
    /*
    public static function envoie_email(Request $request){
        $user = User::where('email', $request->email)->first();

        if ($user == null) {
            return response()->json(['error' => 'Cet email ne correspond à aucun compte'], 403);
        }
        else { 
           $code = self::generateVerificationCode();

            $user->code = $code;
            $user->save();

            $emailRequest = new Request([
                'email' => $user->email,
                'code' => $code
            ]);

            $emailController = new EmailController();
            $emailController->envoyerEmail($emailRequest);

            return response()->json(['message' => 'Email envoyé avec succès // CODE : ' . $code], 200);
        }
    }
    */

    public static function envoie_email(Request $request){
        $user = User::where('email', $request->email)->first();
    
        if ($user == null) {
            abort(403, 'Cet email ne correspond à aucun compte');
        }
    
        $code = self::generateVerificationCode();
    
        $user->code = $code;
        $user->save();
    
        $emailRequest = new Request([
            'email' => $user->email,
            'code' => $code
        ]);
    
        $emailController = new EmailController();
        $emailController->envoyerEmail($emailRequest);
    
        return response()->json(['message' => 'Email envoyé avec succès // CODE : ' . $code], 200);
    }

    public static function ajouter_token_fcm(Request $request){
        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
            return response()->json(['message' => 'Token FCM ajouté avec succès'], 200);
        } else {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }
    }


    public static function filtrerUtilisateurs($filters)
    {
        $query = DB::table('utilisateur')
            ->join('users', 'utilisateur.uti_use_id', '=', 'users.id')
            ->select(
                'utilisateur.uti_no',
                'utilisateur.uti_nom',
                'utilisateur.uti_prenom',
                'utilisateur.uti_disponible',
                'users.name as user_name',
                'users.email as user_email'
            );

        // Appliquer les filtres si présents
        if (!empty($filters['nom'])) {
            $query->where('utilisateur.uti_nom', 'like', '%' . $filters['nom'] . '%');
        }

        if (!empty($filters['prenom'])) {
            $query->where('utilisateur.uti_prenom', 'like', '%' . $filters['prenom'] . '%');
        }

        if (isset($filters['disponible'])) {
            $query->where('utilisateur.uti_disponible', $filters['disponible'] === 'true' ? 1 : 0);
        }

        if (!empty($filters['role'])) {
            $query->where('users.name', $filters['role']);
        }

        return $query->get();
    }
}
