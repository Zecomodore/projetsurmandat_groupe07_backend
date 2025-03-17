<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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

}
