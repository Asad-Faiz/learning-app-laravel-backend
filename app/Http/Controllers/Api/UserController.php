<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // create User
    /**
     * 
     *@param  Request $request
     *@return User
     */
    public function createUser(Request $request)
    {


        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'avatar' => 'required',
                    'type' => 'required',
                    'open_id' => 'required',
                    //token from google account

                    'name' => 'required',
                    // 'email' => 'required|email|unique:users,email',
                    'email' => 'required',
                    // 'password' => 'required|min:6',

                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validateUser->errors()
                ], 401);

            }
            // validate will have  all user field values
            // we can save in data base

            $validated = $validateUser->validated();
            $map = [];
            $map['type'] = $validated['type'];
            $map['open_id'] = $validated['open_id'];
            $user = User::where($map)->first();

            // return response()->json([
            //     'status' => true,
            //     'message' => 'passed validation',
            //     'data' => $validated
            // ], 200);

            // empty means does not match
            // then save the user in the database for first time
            if (empty($user->id)) {
                
                // this certain user is never bin in our data base
                // noow ewe assign user to darta base

                // this token is like user id
                $validated['token'] = md5(uniqid() . rand(10000, 99999));
                $validated['created_at'] = Carbon::now();
               
                // encrypt password
                // $validated['password'] = Hash::make($validated['password']);    passwrod is commented above
                // it resturns the id of row after saving
                $userID = User::insertGetId($validated);
                // return response()->json([
                //     'status' => true,
                //     'message' => 'after user info validation',
                //     'data' => $userID
                // ], 200);
                $userInfo = User::where('id', '=', $userID)->first();

                
                // this token is use to validate
                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;
                $userInfo->access_token = $accessToken;
                User::where('id', '=', $userID)->update(['access_token' => $accessToken]);

                return response()->json([
                    'code' => 200,
                    'msg' => 'User Created Successfully ',
                    'data' => $userInfo
                ], 200);

            }
            // user preiously loggded in
            $accessToken = $user->createToken(uniqid())->plainTextToken;
            $user->access_token = $accessToken;
            User::where('open_id', '=', $validated['open_id'])->update(['access_token' => $accessToken]);

            return response()->json([
                'code' => 200,
                'msg' => 'User Loged in Successfully ',
                'data' => $user
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'msg' => $th->getMessage()
            ], 500);
        }
    }


    // login user

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [

                'email' => 'required|email',
                'password' => 'required'
            ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email and Password does not match',

                ], 401);
            }
            $user = User::where('email', $request->email)->first();
            return response()->json([
                'status' => true,
                'message' => 'Loged in Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}