<?php

namespace App\Http\Controllers\API\v1;

use App\Exceptions\LogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\SortRoleRequest;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\Roles\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roleService) {}

    public function index(): JsonResponse
    {
        $data = $this->roleService->index();

        return jsonResponse([
            'data' => RoleResource::collection($data),
            'message' => 'roles_received',
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->roleService->show($id);

        return jsonResponse([
            'data' => RoleResource::make($data),
            'message' => 'role_received',
        ]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->roleService->store($requestData);

        return jsonResponse(
            [
                'data' => RoleResource::make($data),
                'message' => 'role_created',
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @throws LogicException
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->roleService->update($requestData, $id);

        return jsonResponse([
            'data' => RoleResource::make($data),
            'message' => 'role_updated',
        ]);
    }

    /**
     * @throws LogicException
     */
    public function destroy(int $id): JsonResponse
    {
        $data = $this->roleService->destroy($id);

        return jsonResponse([
            'data' => $data,
            'message' => 'role_deleted',
        ]);
    }

    public function sort(SortRoleRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $this->roleService->sort($requestData);

        return jsonResponse([
            'message' => 'roles_sorting_updated',
        ]);
    }

    public function permissions(): JsonResponse
    {
        $data = $this->roleService->permissions();

        return jsonResponse([
            'data' => $data,
            'message' => 'permissions_received',
        ]);
    }
}
