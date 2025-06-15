<?php

namespace App\Http\Controllers\API\v1;

use App\Exceptions\ForbiddenException;
use App\Exceptions\LogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\IndexPaymentRequest;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $service) {}

    public function index(IndexPaymentRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->index($requestData);

        return jsonResponse([
            'data' => PaymentResource::collection($data->getCollection()),
            'message' => 'payments_received',
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
            'data' => PaymentResource::make($data),
            'message' => 'payment_received',
        ]);
    }

    /**
     * @throws ForbiddenException|LogicException
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $data = $this->service->store($requestData);

        return jsonResponse(
            [
                'data' => PaymentResource::make($data),
                'message' => 'payment_created',
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @throws ForbiddenException|LogicException
     */
    public function getCheckoutUrl(int $id): JsonResponse
    {
        $data = $this->service->getCheckoutUrl($id);

        return jsonResponse([
            'data' => ['url' => $data],
            'message' => 'checkout_url_received',
        ]);
    }

    /**
     * @throws ForbiddenException|LogicException
     */
    public function checkPaymentStatus(int $id): JsonResponse
    {
        $data = $this->service->checkPaymentStatus($id);

        return jsonResponse([
            'data' => PaymentResource::make($data),
            'message' => 'payment_status_received',
        ]);
    }

    public function handleCallback(Request $request): JsonResponse
    {
        $this->service->handleCallback($request);

        return response()->json();
    }
}
