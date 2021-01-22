<?php

namespace App\Traits\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers as OriginalRegistersUsers;

trait RegistersUsers
{
    use OriginalRegistersUsers;

    /* Sobre escribir logica original del trait */
}
