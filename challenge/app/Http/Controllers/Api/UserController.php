<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function index (): UserCollection
    {
        $users = $this->user->paginate(10)->orderBy('created_at', 'desc');

        return new UserCollection($users);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'cpf'     => $request->cpf,
            'password'=> bcrypt($request->password),
            'birthday'=> $request->birthday
        ];

        try {
            
            $user = $this->user->create($data);

            $user->cpf = $this->formatCpf($user->cpf);

            return response()->json([
                'data' => [
                    'message' => 'User created successfully!',
                    'user' => $user
                ]], 201);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->user->findOrFail($id);

            $user->cpf = $this->formatCpf($user->cpf);

            return response()->json([
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->all();

        //verifying if the password field has been set
        if ($request->has('password') && $request->get('password')) {
            //validating password
            Validator::make($data, [
                'password' => 'required|confirmed'
            ])->validate();

            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        try {

            $user = $this->user->findOrFail($id);
            $user->update($data);

            $user->cpf = $this->formatCpf($user->cpf);

            return response()->json(['data' => [
                'message' => 'User updated successfully!', 
                'user' => $user]
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = $this->user->findOrFail($id);

            $user->delete();
            return response()->json(['msg' => 'User deleted successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    private function formatCpf($cpf)
    {
        $rep = '.';
        $cpf = substr_replace ( $cpf, $rep, 3, 0 );
        $cpf = substr_replace ( $cpf, $rep, 7, 0 );
        $rep = '-';

        return $cpf = substr_replace ( $cpf, $rep, 11, 0 );
    }
}