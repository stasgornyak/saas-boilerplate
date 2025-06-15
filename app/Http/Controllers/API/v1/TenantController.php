<?php

namespace App\Http\Controllers\API\v1;

use App\Exceptions\ForbiddenException;
use App\Exceptions\LogicException;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\SortTenantsRequest;
use App\Http\Requests\Tenants\StoreTenantRequest;
use App\Http\Requests\Tenants\UpdateTenantRequest;
use App\Http\Resources\TenantResource;
use App\Services\Tenants\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class TenantController extends Controller
{
    public function __construct(private readonly TenantService $tenantService) {}

    public function index(): JsonResponse
    {
        $data = $this->tenantService->index();

        return jsonResponse([
            'data' => TenantResource::collection($data),
            'message' => 'tenants_received',
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->tenantService->show($id);

        return jsonResponse([
            'data' => TenantResource::make($data),
            'message' => 'tenant_received',
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->tenantService->store($requestData);

        return jsonResponse([
            'data' => TenantResource::make($data),
            'message' => 'tenant_created',
        ], Response::HTTP_CREATED);
    }

    /**
     * @throws ForbiddenException
     * @throws ValidationException
     * @throws TenantCouldNotBeIdentifiedById
     * @throws LogicException
     */
    public function update(UpdateTenantRequest $request, int $id): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->tenantService->update($requestData, $id);

        return jsonResponse([
            'data' => TenantResource::make($data),
            'message' => 'tenant_updated',
        ]);
    }

    /**
     * @throws ForbiddenException
     */
    public function destroy(int $id): JsonResponse
    {
        $data = $this->tenantService->destroy($id);

        return jsonResponse([
            'data' => $data,
            'message' => 'tenant_deleted',
        ]);
    }

    public function sort(SortTenantsRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $this->tenantService->sort($requestData);

        return jsonResponse([
            'message' => 'tenants_sorting_updated',
        ]);
    }
}
