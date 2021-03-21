<?php

declare(strict_types=1);


namespace App\Http\Controllers\Settings\Users;


use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\User\UserStoreRequest;
use App\Http\Requests\Settings\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function index(Request $request) {
        $query = QueryBuilder::for(User::class, $request)
            ->allowedFilters([
                'id',
                'name',
                'email',
            ]);
        return $query->paginate($request->per_page ?: 25);
    }

    public function show(User $user) {
        return new JsonResource($user);
    }

    public function store(UserStoreRequest $request, User $user) {
        $user->fill($request->only(['name', 'email']));
        $user->password = Hash::make($request->password);
        $user->save();

        return new JsonResource($user);
    }

    public function update(UserUpdateRequest $request, User $user) {
        $user->fill($request->only(['name', 'email']));

        if(empty($request->password) === false) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return new JsonResource($user);
    }

    public function destroy(Request $request, User $user) {
        if($request->user()->id === $user->id) {
            throw ValidationException::withMessages([
                'name' => 'Cannot delete self'
            ]);
        }

        $user->delete();

        return new JsonResource([]);
    }
}
