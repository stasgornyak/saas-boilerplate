<?php

namespace App\Services\E2E;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

class E2EService
{
    public const TEST_TENANT_SLUG = 'instance';

    public function resetDB(bool $central): array
    {
        try {
            if ($central) {
                Artisan::call('migrate:fresh --seed');

                $message = 'central_database_refreshed_successfully';
            } else {
                Artisan::call('tenants:migrate-fresh', ['--tenants' => [self::TEST_TENANT_SLUG]]);
                Artisan::call('tenants:seed', ['--tenants' => [self::TEST_TENANT_SLUG]]);

                $message = 'tenant_database_refreshed_successfully';
            }

            return [
                'message' => $message,
                'data' => ['artisanOutput' => Artisan::output()],
                'code' => Response::HTTP_OK,
            ];

        } catch (\Exception $e) {
            return [
                'message' => 'database_refreshing_completed_with_an_error',
                'description' => $e->getMessage(),
                'data' => ['artisanOutput' => Artisan::output()],
                'code' => Response::HTTP_BAD_REQUEST,
            ];
        }

    }
}
