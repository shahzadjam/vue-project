<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try
        {
            $users = User::all();
            
            if ($users->isNotEmpty())
            {
                return response()->json([
                    'success' => true,
                    'message' => 'Users Fetched Successfully',
                    'data' => $users,
                ], 200);
            }
            else
            {
                return response()->json([
                    'success' => true,
                    'message' => 'No User Found',
                    'data' => null,
                ], 204);
            }
        }
        catch (\Exception $error)
        {

            return response()->json([
                'success' => false,
                'message' => $error->getMessage() ?? 'Unknown Error Occured',
                'error' => $error,
            ], (int) $error->getCode() != 0 ? (int) $error->getCode() : 422);
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|max:16',
            ]);
 
            if ($validator->fails())
            {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->messages() ?? 'Validation Failed',
                    'error' => $validator->errors(),
                ], 422);
            }
            else
            {
                $user = User::create([
                    'first_name' => $request->input('first_name') ?? null,
                    'last_name' => $request->input('last_name') ?? null,
                    'email' => $request->input('email') ?? null,
                    'password' => bcrypt($request->input('password')) ?? null,
                ]);

                if ($user && $user->id)
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'User Created Successfully',
                        'data' => $user,
                    ], 201);
                }
                else
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown Error Occured while Creating New User',
                        'error' => null,
                    ], 500);
                }
            }
        }
        catch (\Exception $error)
        {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage() ?? 'Unknown Error Occured',
                'error' => $error,
            ], (int) $error->getCode() != 0 ? (int) $error->getCode() : 422);
        }
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try
        {
            $user = User::find($id);
 
            if ($user && $user->id)
            {
                return response()->json([
                    'success' => true,
                    'message' => 'User Found',
                    'data' => $user,
                ], 200);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User Not Found',
                    'error' => null,
                ], 404);
            }
        }
        catch (\Exception $error)
        {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage() ?? 'Unknown Error Occured',
                'error' => $error,
            ], (int) $error->getCode() != 0 ? (int) $error->getCode() : 422);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try
        {
            $user = User::find($id);
 
            if ($user && $user->id)
            {
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'email'      => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($user->id),
                    ],
                    // 'password' => 'required|string|min:8|max:16',
                ]);
     
                if ($validator->fails())
                {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->messages() ?? 'Validation Failed',
                        'error' => $validator->errors(),
                    ], 422);
                }
                else
                {
                    $updateUser = $user->update($request->only(['first_name', 'last_name', 'email']));
    
                    if ($updateUser)
                    {
                        return response()->json([
                            'success' => true,
                            'message' => 'User Updated Successfully',
                            'data' => $request->only(['first_name', 'last_name', 'email']),
                        ], 200);
                    }
                    else
                    {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unknown Error Occured while Updating Existing User',
                            'error' => null,
                        ], 500);
                    }
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User Not Found',
                    'error' => null,
                ], 404);
            }
        }
        catch (\Exception $error)
        {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage() ?? 'Unknown Error Occured',
                'error' => $error,
            ], (int) $error->getCode() != 0 ? (int) $error->getCode() : 422);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try
        {
            $user = User::find($id);
 
            if ($user && $user->id)
            {
                $deleteUser = $user->delete();
    
                if ($deleteUser)
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'User Deleted Successfully',
                        'data' => null,
                    ], 200);
                }
                else
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown Error Occured while Deleting Existing User',
                        'error' => null,
                    ], 500);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User Not Found',
                    'error' => null,
                ], 404);
            }
        }
        catch (\Exception $error)
        {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage() ?? 'Unknown Error Occured',
                'error' => $error,
            ], (int) $error->getCode() != 0 ? (int) $error->getCode() : 422);
        }
    }
}
