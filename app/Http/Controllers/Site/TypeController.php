<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Type;
use App\Http\Resources\Site\TypeResource;
class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $types  = Type::show()->get();
        $res    = [
            'type'     => 'success',
            'messsage' => 'Get types successfully!!!',
            'data'     => TypeResource::collection($types)
    	];
        return response($res, 200);
    }

}
