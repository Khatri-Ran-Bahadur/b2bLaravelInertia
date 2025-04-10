<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountStoreUpdateRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccountController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function my(): JsonResponse
    {
        $perPage = request('perPage', 10);

        $accounts = Account::where('user_id', auth()->id())
            ->paginate($perPage);

        return $this->collectionRespond(
            AccountResource::collection($accounts)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AccountStoreRequest $request
     * @return JsonResponse
     */
    public function store(AccountStoreUpdateRequest $request): JsonResponse
    {
        $data = $request->only('account_name', 'account_number', 'bic');
        $data['user_id'] = auth()->id();
        Account::create($data);
        return $this->respondSuccess(__('acciunt.account_created'));
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $account = Account::findOrFail($id);
        return $this->detailRespond(new AccountResource($account));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, Account $account): JsonResponse
    {
        $data = $request->only('account_name', 'account_number', 'bic');
        $account->update($data);

        return $this->respondSuccess(__('account.account_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        Account::findOrFail($id)->delete();
        return $this->respondSuccess(__('account.account_deleted'));
    }
}
