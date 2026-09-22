<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        return LeadResource::collection(
            Lead::query()->latest()->paginate(20)
        );
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        return new LeadResource($lead);
    }
}
