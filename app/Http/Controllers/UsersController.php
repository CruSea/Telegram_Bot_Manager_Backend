<?php

namespace App\Http\Controllers;

use App\Entities\Company;
use App\Entities\CompanyUser;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

class UsersController extends Controller
{
    protected $agapeController;
    /**
     * AuthAPIController constructor.
     */
    public function __construct() {
        $this->agapeController = new AgapeController();
        $this->middleware('is_company_viewer')->only([]);
    }
    public function getAllUsers(){
        try{
            $token = JWTAuth::getToken();
            if($token){
                $token_company = $this->agapeController->getCompanyFromToken($token);
                if($token_company instanceof Company){
                    if($token_company->status == true){
                        $user_detail = CompanyUser::with(['userRole','user','company'])->where('company_id','=',$token_company->id)->get();
                        return response()->json(['users'=>$user_detail],200);
                    }else{
                        return response()->json(['error'=>"Your Account is not Active Yet"],500);
                    }
                }
            }else{
                return response()->json(['error'=>"failed to login!!! please provide your token",],500);
            }
        }catch (\Exception $exception){

        }
    }
}
