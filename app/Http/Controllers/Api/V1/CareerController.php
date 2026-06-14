<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\CareerCatalog;
use Illuminate\Http\JsonResponse;

/**
 * CareerController — Catálogo de carreras → roles objetivo.
 * GET /careers → usado por el registro y el selector de rol objetivo.
 * Público (no expone datos sensibles).
 */
class CareerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => CareerCatalog::forApi()]);
    }
}
