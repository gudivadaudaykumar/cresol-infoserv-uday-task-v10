<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hobby;
use DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $hobbies = Hobby::select('id', 'hobbie_name')->get()->toArray();
        if ($request->ajax()) {
            $searchValue = $request->input('search.value');
            $data = User::with('hobbies')
                ->select(['id', 'first_name', 'last_name'])
                ->orderBy('created_at', 'desc');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('hobbies', function ($user) {
                    return $user->hobbies->pluck('hobbie_name')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-primary btn-sm edit-user" data-user-id="' . $row->id . '">Edit</button>';
                    $btn .= ' <button type="button" class="btn btn-danger btn-sm delete-user" data-user-id="' . $row->id . '">Delete</button>';
                    return $btn;
                })
                ->filterColumn('hobbies', function ($query) use($searchValue){
                    $searchHobbies = explode(',', $searchValue);
                    $query->whereHas('hobbies', function ($query) use ($searchHobbies) {
                        $query->whereIn('hobbie_name', $searchHobbies);
                    });
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('users', compact('hobbies'));
    }


    public function store(Request $request)
    {
        try {
            $user = new User;
            $user->first_name = $request->firstName;
            $user->last_name = $request->lastName;
            $user->save();
            $user->hobbies()->attach($request->hobbies);
            return response()->json(['data' => $user, 'message' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['user' => [], 'message' => $e->getMessage()], 500);
        }
    }

    public function userInfo(Request $request, $userId)
    {
        $user = User::with('hobbies')
            ->where('id', $userId)
            ->select(['id', 'first_name', 'last_name'])
            ->first();
        return $user;
    }

    public function update(Request $request)
    {
        try {
            $user = User::find($request->get('editUserId', null));
            if ($user) {
                $user->first_name = $request->editFirstName;
                $user->last_name = $request->editLastName;
                $user->save();
                $user->hobbies()->detach();
                $user->hobbies()->attach($request->edithobbies);
            } else {
                throw new \Exception('User not found');
            }
            return response()->json(['data' => $user, 'message' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['user' => [], 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->hobbies()->detach();
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
