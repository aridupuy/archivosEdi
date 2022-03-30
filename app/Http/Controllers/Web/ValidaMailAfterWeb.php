<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ValidaMailAfterController
 *
 * @author ariel
 */
namespace App\Http\Controllers\Web;

class ValidaMailAfterWeb{
    //put your code here
    
    public function validar(\Illuminate\Http\Request $request){
    
        return view("ValidaMailError",["mensaje"=>"Error el mail ya fue validado."]);
    }
    
}
