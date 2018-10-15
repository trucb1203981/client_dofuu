<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Type;
use App\Http\Resources\Site\TypeResource;

class TypeController extends Controller
{
    public function fetchType(Request $request) {
    	$types  = Type::show()->get();

        return $this->respondSuccess('Get all type', $types, 200, 'many');
    }

    protected function respondSuccess($message, $data, $status = 200, $type) {
        $res = [
            'status'    => 'success',
            'message' => $message . ' successfully.',
        ];

        switch ($type) {

            case 'one':
            $res['type']  = new TypeResource($data);
            break;

            case 'many':
            $res['types'] = TypeResource::collection($data);
            break;
        }

        return response($res, $status);
    }
}
