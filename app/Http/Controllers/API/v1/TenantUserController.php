<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantUsers\StoreTenantUserRequest;
use App\Http\Requests\TenantUsers\UpdateTenantUserRequest;
use App\Http\Resources\TenantUserResource;
use App\Services\Users\TenantUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TenantUserController extends Controller
{
    public function __construct(private readonly TenantUserService $service) {}

    public function current(): JsonResponse
    {
        $data = $this->service->current();

        return jsonResponse([
            'data' => TenantUserResource::make($data),
            'message' => 'user_received',
        ]);
    }

    public function index(): JsonResponse
    {
        $data = $this->service->index();

        return jsonResponse([
            'data' => TenantUserResource::collection($data),
            'message' => 'users_received',
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->show($id);

        return jsonResponse([
            'data' => TenantUserResource::make($data),
            'message' => 'user_received',
        ]);
    }

    public function store(StoreTenantUserRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->store($requestData);

        return jsonResponse(
            [
                'data' => TenantUserResource::make($data),
                'message' => 'user_added',
            ],
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateTenantUserRequest $request, int $id): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->update($requestData, $id);

        return jsonResponse([
            'data' => TenantUserResource::make($data),
            'message' => 'user_updated',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $data = $this->service->destroy($id);

        return jsonResponse([
            'data' => $data,
            'message' => 'user_removed',
        ]);
    }
}
