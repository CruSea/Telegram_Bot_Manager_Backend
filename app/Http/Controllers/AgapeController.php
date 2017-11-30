<?php

namespace App\Http\Controllers;

use App\Entities\Company;
use App\Entities\CompanyUser;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

class AgapeController extends Controller
{
    /**
     * @param $token
     * @return User
     */
    public function getUserFromToken($token){
        return JWTAuth::toUser($token);
    }
    /**
     * @param $token
     * @return Company
     */
    public function getCompanyFromToken($token){
        $user = $this->getUserFromToken($token);
        if($user != null){
            $companyUser = CompanyUser::where('user_id','=', $user->id)->first();
            return $companyUser->company;
        }
        return null;
    }
}
