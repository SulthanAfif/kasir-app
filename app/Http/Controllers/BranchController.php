<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['users', 'products'])->latest()->paginate(15);
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'boolean',
            'is_main' => 'boolean',
        ]);
        $validated['code'] = Branch::generateCode();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_main'] = $request->boolean('is_main', false);

        if ($validated['is_main']) {
            Branch::query()->update(['is_main' => false]);
        }

        Branch::create($validated);
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'boolean',
            'is_main' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_main'] = $request->boolean('is_main', false);

        if ($validated['is_main']) {
            Branch::where('id', '!=', $branch->id)->update(['is_main' => false]);
        }

        $branch->update($validated);
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->is_main) {
            return back()->with('error', 'Cabang utama tidak bisa dihapus.');
        }
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
