<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class BaseCentralModel extends Model
{
    use CentralConnection;
}
