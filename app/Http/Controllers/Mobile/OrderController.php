<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\OrderResource;
use App\Models\Role;
use App\Models\OrderStatus;
use App\Models\RegularOrder;
class OrderController extends Controller
{
	public function __construct() {
		$this->shipper  = Role::where('name', 'Shipper')->first();
		$this->status   = OrderStatus::where('order_status_name', 'Hủy')->first();
		$this->middleware('auth:api');
	}

    public function orderByFilter(Request $request) {
		$fromDate          = $request->fromDate;
		$toDate            = $request->toDate;
		$statusId          = (int)$request->statusId;
		$request->endPoint = 'fetch_order';
    	if($statusId == 0) {
			$orders  = RegularOrder::byUserId(auth('api')->user()->id)->withRange($fromDate, $toDate)->orderByDesc('id')->get();
		} else {
			$orders  = RegularOrder::byUserId(auth('api')->user()->id)->withRange($fromDate, $toDate)->byStatusId($statusId)->orderByDesc('id')->get();
		}
		
		return $this->respondSuccess('Get history', $orders->load('user', 'shipper', 'employee', 'store', 'payment'));
	}

	public function show(Request $request, $orderId) {
		$request->endPoint = 'show_order';
    	
		$order  = RegularOrder::byUserId(auth('api')->user()->id)->findorFail($orderId);
	
		return $this->respondSuccess('Get history', $order->load('products', 'user', 'shipper', 'employee', 'store', 'payment'), 'one');
	}

	//CANCEL ORDER
	public function cancel(Request $request) {
		$orderId = $request->id;
		$userId  = $request->owner['id'];
		$storeId = $request->store['id'];
		$cancelId = $this->status->id;
		if(auth('api')->user()->id == $userId) {
			$order = RegularOrder::byUserId($userId)->hasNotStatusId($cancelId)->byStoreId($storeId)->findOrFail($orderId);
			if(!is_null($order)) {

				$order->status_id = $cancelId;
				$order->note      = serialize($request->orderNotes);
				$order->save();
				
				return $this->respondSuccess('Cancelled order', $order->load('products', 'user', 'shipper', 'employee', 'store', 'payment'), 'one');
			}
		}
		return $this->respondError('Something went wrong.', 500);
	}

	protected function respondSuccess($message, $data, $type = 'many', $status = 200)
    {   
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['order']  = new OrderResource($data);
            break;
            case 'many':
            $res['orders'] = OrderResource::collection($data);
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
