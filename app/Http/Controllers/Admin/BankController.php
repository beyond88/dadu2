<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Bank List|Bank Create|Bank Edit|Bank Delete']);
    }

    public function index()
    {
        set_page_meta(__('custom.banks'));
        $banks = Bank::with(['creator', 'updater'])
            ->when(request()->search, function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%')
                    ->orWhere('account_name', 'like', '%' . request()->search . '%')
                    ->orWhere('account_number', 'like', '%' . request()->search . '%')
                    ->orWhere('branch_name', 'like', '%' . request()->search . '%');
            })
            ->when(request()->status, function ($query) {
                if (request()->status == 'active') {
                    $query->active();
                } elseif (request()->status == 'inactive') {
                    $query->inactive();
                }
            })
            ->latest()
            ->paginate(20);

        return view('admin.banks.index', compact('banks'));
    }

    public function create()
    {
        set_page_meta(__('custom.add_bank'));
        return view('admin.banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);

        $bank = Bank::create([
            'name' => $request->name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch_name' => $request->branch_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'contact_person' => $request->contact_person,
            'contact_person_phone' => $request->contact_person_phone,
            'notes' => $request->notes,
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        flash(__('custom.bank_created_successfully'))->success();
        return redirect()->route('admin.banks.index');
    }

    public function show(Bank $bank)
    {
        set_page_meta(__('custom.bank_details'));
        $bank->load(['creator', 'updater']);
        return view('admin.banks.show', compact('bank'));
    }

    public function edit(Bank $bank)
    {
        set_page_meta(__('custom.edit_bank'));
        return view('admin.banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name,' . $bank->id,
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);

        $bank->update([
            'name' => $request->name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch_name' => $request->branch_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'contact_person' => $request->contact_person,
            'contact_person_phone' => $request->contact_person_phone,
            'notes' => $request->notes,
            'is_active' => $request->is_active ?? true,
            'updated_by' => Auth::id(),
        ]);

        flash(__('custom.bank_updated_successfully'))->success();
        return redirect()->route('admin.banks.index');
    }

    public function destroy(Bank $bank)
    {
        // Check if bank has any related records (you can add this logic later)
        // For now, allow deletion
        $bank->delete();

        flash(__('custom.bank_deleted_successfully'))->success();
        return redirect()->route('admin.banks.index');
    }

    public function toggleStatus(Bank $bank)
    {
        $bank->update([
            'is_active' => !$bank->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $bank->is_active ? 'activated' : 'deactivated';
        flash(__('custom.bank_status_updated_successfully', ['status' => $status]))->success();
        return redirect()->route('admin.banks.index');
    }
}
