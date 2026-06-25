<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function fetchCities(Request $request)
    {
        $term = $request->get('term');
        
        $cities = City::where('city_name', 'LIKE', '%' . $term . '%')
                      ->where('is_active', true)
                      ->orderBy('city_name')
                      ->limit(10)
                      ->get(['city_name', 'state_name']);
        
        return response()->json($cities);
    }
}
