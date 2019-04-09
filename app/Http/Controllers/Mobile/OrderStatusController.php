<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Http\Resources\Mobile\OrderStatusResource;

class OrderStatusController extends Controller
{
    public function __construct() {
		$this->middleware('auth:api');
	}

	public function getOrderStatus()
	{
		$status = OrderStatus::get();

		return $this->respondSuccess('Get order status', $status);
	}


	protected function respondSuccess($message, $data, $type = 'many', $status = 200)
    {   
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['status']  = new OrderStatusResource($data);
            break;
            case 'many':
            $res['status'] = OrderStatusResource::collection($data);
            break;
        }

        return response($res, $status);
    }

    protected function respondError($message, $status = 200)
    {   
        $res = [
            'type'    => 'error',
            'message' => $message,
            'data'    => null
        ];

        return response($res, $status);
    }

}
