<?php

namespace App\Http\Controllers;

use App\Entities\Company;
use App\Entities\CompanyUser;
use App\User;
use JWTAuth;
use Validator;
use Illuminate\Http\Request;

class AuthAPIController extends Controller {

    protected $agapeController;
    /**
     * AuthAPIController constructor.
     */
    public function __construct() {
        $this->agapeController = new AgapeController();
    }
    public function authenticate(){
        try{
            $credential1 = request()->only('email','password');
            $rule1 = ['email' => 'required|max:255', 'password' => 'required|min:4'];
            $validator1 = Validator::make($credential1, $rule1);
            if($validator1->fails()) {
                $error = $validator1->messages();
                return response()->json(['error'=> $error],500);
            }
            $token = JWTAuth::attempt($credential1);
            if(!$token){
                return response()->json(['Invalid credential used!!!'],401);
            }else{
                return response()->json(['token'=>$token],200);
            }
        }catch (JWTException $exception){
            return response()->json(['error'=>$exception->getMessage()],500);
        }
    }
    public function login() {
        try{
            $token = JWTAuth::getToken();
            if($token){
                $token_user = $this->agapeController->getUserFromToken($token);
                $token_company = $this->agapeController->getCompanyFromToken($token);
                if($token_user instanceof User && $token_company instanceof Company){
                    if($token_company->status == true){
                        $user_detail = CompanyUser::with(['userRole','user','company'])->where('company_id','=',$token_company->id)->where('user_id', '=', $token_user->id)->first();
                        if($user_detail instanceof CompanyUser){
                            return response()->json(['user'=>$user_detail],200);
                        }
                    }else{
                        return response()->json(['error'=>"Your Account is not Active Yet"],500);
                    }
                }
            }
            return response()->json(['error'=>"failed to login!!! please try again"],500);
        }catch (\Exception $exception){
            return response()->json(['error'=>$exception->getMessage()],500);
        }
    }
    public function register(){
        $credential_met = request()->only('company_name','full_name','email','password');
        $rules = [
            'company_name' => 'required|max:255',
            'full_name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:4',
        ];
        $validator = Validator::make($credential_met, $rules);

        if($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['error'=> $error],500);
        }
        $old_user = User::where('email','=',$credential_met['email'])->first();
        if(! $old_user instanceof User){
            $new_company = new Company();
            $new_company->name = $credential_met['company_name'];
            if($new_company->save()){
                $new_user = new User();
                $new_user->full_name = $credential_met['full_name'];
                $new_user->email = $credential_met['email'];
                $new_user->password = bcrypt($credential_met['password']);
                if($new_user->save()){
                    $new_company_user = new CompanyUser();
                    $new_company_user->company_id = $new_company->id;
                    $new_company_user->user_id = $new_user->id;
                    $new_company_user->user_role_id = 2;
                    if($new_company_user->save()){
                        $token = JWTAuth::fromUser($new_user);
                        return response()->json(['token'=>$token, 'user' => $new_user, 'status' => true],200);
                    }
                }else{

                }
            }else{
                return response()->json(['error'=>'failed to register'],500);
            }
        }else{
            return response()->json(['error'=>'the email is already in use'],500);
        }
    }
}
