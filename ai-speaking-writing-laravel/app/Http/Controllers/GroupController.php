<?php
namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        return Group::with('questions')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        return Group::create([
            'name' => $request->name
        ]);
    }

    public function show(Group $group)
    {
        return $group->load('questions');
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $group->update([
            'name' => $request->name
        ]);

        return $group;
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return response()->json([
            'message' => 'Group deleted successfully.'
        ]);
    }
}
