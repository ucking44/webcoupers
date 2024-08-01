<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;


class AuthController extends Controller
{
    public function profile($id)
    {
        if (auth()->user()->id == $id)
        {
            $profile = User::findOrFail($id);

            return response()->json([
                'status' => true,
                'data'   => $profile
            ]);
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to access this resource'
            ], 500);
        }
    }

    public function getUsers()
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            ///// FETCH ALL USERS
            $allUsers = User::all();

            return response()->json([
                'status' => true,
                'data'   => $allUsers
            ]);
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to access this resource'
            ], 500);
        }
    }

    public function getSingleUser($id)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            ////  CHECK IF USER ID EXISTS
            if (User::where('id', $id)->exists())
            {
                $singleUser = User::findOrFail($id);

                return response()->json([
                    'status'  => true,
                    'data'    => $singleUser
                ], 200);
            }
            else
            {
                return response()->json([
                    'status'  => false,
                    'message' => 'User with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
                ], 404);
            }
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to access this resource'
            ], 500);
        }
    }

    public function getNonStaff()
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            $nonStaff = User::where('usertype', null)->get();

            return response()->json([
                'status'  => true,
                'data'    => $nonStaff
            ], 200);
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to access this resource'
            ], 500);
        }
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            //'usertype' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->usertype = request()->usertype;
        $user->password = bcrypt(request()->password);
        $user->save();

        return response()->json($user, 201);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials))
        {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUche()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
