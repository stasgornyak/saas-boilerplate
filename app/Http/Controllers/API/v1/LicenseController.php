<?php

namespace App\Http\Controllers\API\v1;

use App\Exceptions\ForbiddenException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Licenses\IndexLicenseRequest;
use App\Http\Requests\Licenses\StoreLicenseRequest;
use App\Http\Resources\LicenseBriefResource;
use App\Http\Resources\LicenseResource;
use App\Services\Licenses\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LicenseController extends Controller
{
    public function __construct(private readonly LicenseService $service) {}

    public function getTariffs(): JsonResponse
    {
        $data = $this->service->getTariffs();

        return jsonResponse([
            'data' => $data,
            'message' => 'tariffs_received',
        ]);
    }

    public function index(IndexLicenseRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->index($requestData);

        return jsonResponse([
            'data' => LicenseBriefResource::collection($data->getCollection()),
            'message' => 'licenses_received',
            'meta' => [
                'total' => $data->total(),
                'has_more_pages' => $data->hasMorePages(),
                'current_page' => $data->currentPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->show($id);

        return jsonResponse([
            'data' => LicenseResource::make($data),
            'message' => 'license_received',
        ]);
    }

    /**
     * @throws ForbiddenException
     */
    public function store(StoreLicenseRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->store($requestData);

        return jsonResponse(
            [
                'data' => LicenseResource::make($data),
                'message' => 'license_created',
            ],
            Response::HTTP_CREATED
        );
    }
}
