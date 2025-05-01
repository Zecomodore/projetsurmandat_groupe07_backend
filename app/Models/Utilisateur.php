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

    public static function get_utilisteur($id){
        $utilisateur = Utilisateur::where('uti_use_id', $id)->get();
        return $utilisateur;
    }

    public static function get_all_utilisateurs(){
        $utilisateurs = Utilisateur::all();
        return response()->json($utilisateurs);
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
    
    

    
}
