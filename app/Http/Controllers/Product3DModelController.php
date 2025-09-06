<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product3dModels;
use Illuminate\Http\Request;

class Product3DModelController extends Controller
{
    public function saveClipping(Request $request, $id)
    {
        $model = Product3dModels::findOrFail($id);
        $model->clipping_planes_data = [
            'xPos' => $request->xPos,
            'xNeg' => $request->xNeg,
            'zPos' => $request->zPos,
            'zNeg' => $request->zNeg,
        ];
        $model->save();

        return response()->json(['status' => 'success']);
    }
}
