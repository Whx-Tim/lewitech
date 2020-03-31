<?php
namespace App\Repositories;

use App\Models\Insurance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class InsuranceRepository
{
    private $insurance;

    public function __construct()
    {
        $this->insurance = new Insurance();
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $insurance = $user->insurances()->create($request->only($this->insurance->getFillable()));

        return $insurance;
    }
}