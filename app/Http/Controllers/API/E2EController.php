<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\E2E\E2EService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class E2EController extends Controller
{
    public function __construct(private readonly E2EService $service) {}

    public function resetDB(Request $request): JsonResponse
    {
        $central = $request->input('central', true);

        $data = $this->service->resetDB($central);

        return jsonResponse(Arr::only($data, ['data', 'message', 'description']), $data['code']);
    }
}
