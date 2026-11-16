<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;

class BanksController extends Controller
{
    public function index()
    {
        $banks = Bank::active()->orderBy('bank_name')->get(['id', 'bank_name', 'account_number', 'account_name', 'is_active']);

        return response()->json($banks);
    }
}
