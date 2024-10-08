<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FarmController extends Controller
{
    public function index()
    {
        $farms = Farm::where('user_id', Auth::id())
            ->with('animals')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return Inertia::render('Farm/Index', [
            'farms' => $farms,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'message' => session('message'),
            ],
        ]);
    }
    public function show(int $farmId)
    {
        $farm = Farm::find($farmId);
        if ( ! $farm) {
            return redirect(route('farms.index'))
                ->with('error', 'Farm not found.');
        }
        return Inertia::render('Farm/Update', [
            'farm' => $farm,
        ]);
    }
    public function create()
    {
        return Inertia::render('Farm/Create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);
        Farm::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website
        ]);
        return redirect(route('farms.index'))->with('success', 'Farm has been created.');
    }
    public function destroy(Request $request)
    {
        $request->validate([
            'farm_id' => ['required', 'numeric', 'exists:farms,id'],
        ]);
        $farm = Farm::find($request->farm_id);
        if ( ! $farm) {
            return redirect(route('farms.index'))
                ->with('error', 'Farm not found.');
        }
        if($farm->user_id != Auth::id()){
            return redirect(route('farms.index'))
                ->with('error', 'Oops, something went wrong.');
        }
        if( $farm->animals()->count() > 0 ) {
            foreach ($farm->animals as $animal) {
                $animal->delete();
            }
        }
        $farm->delete();
        return redirect(route('farms.index'))->with('success', 'Farm has been deleted.');
    }
    public function update(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => ['required', 'numeric', 'exists:farms,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);
        $farm = Farm::find($request->farm_id);
        $farm->update($validated);
        return redirect(route('farms.index'))->with('success', 'Farm has been updated.');
    }
}
