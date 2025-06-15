<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class BaseTenantModel extends Model
{
    use TenantConnection;
}
