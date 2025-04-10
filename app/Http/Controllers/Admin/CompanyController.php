<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CompanyDetailResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // Query builder with search functionality
        /*name
        •⁠  ⁠tin_number
        •⁠  ⁠verification_status
        •⁠  ⁠contact_info
        */
        $query = Company::select(
            'companies.id',
            'companies.name',
            'companies.tin_number',
            'companies.verification_status',
            'companies.phone',
            'companies.created_at',
            'users.name as owner_name'
        )
            ->join('company_users', function ($join) {
                $join->on('company_users.company_id', '=', 'companies.id')
                    ->where('company_users.role', 'owner');
            })
            ->join('users', 'users.id', '=', 'company_users.user_id')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('companies.name', 'like', "%{$search}%")
                        ->orWhere('companies.tin_number', 'like', "%{$search}%")
                        ->orWhere('companies.verification_status', 'like', "%{$search}%")
                        ->orWhere('companies.phone', 'like', "%{$search}%")
                        ->orWhere('users.name', 'like', "%{$search}%");
                });
            });



        // Add sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $companies = $query->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Company/Index', [
            'companies' => $companies,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
                'page' => $request->input('page', 1),
                'sort' => $sort,
                'direction' => $direction,
            ],
            'can' => [
                'view' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
            ]
        ]);
    }

    /**
     * Show 
     *
     * @return Response
     */
    public function show(Company $company): Response
    {
        $company->load(['owner']);
        return Inertia::render('Admin/Company/Show', [
            'company' => new CompanyDetailResource($company),
        ]);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.users.index');
    }
}
