<?php

namespace App\Http\Controllers\Site;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CheckoutNotification;
use App\Http\Resources\Site\CouponResource;
use App\Http\Resources\Site\OrderResource;
use App\Http\Resources\Site\ProductResource;
use App\Models\Product;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\RegularOrder;
use App\Models\ActualOrder;
use App\Models\OrderStatus;
use App\Models\City;
use App\Models\Role;
use App\Models\User;
use App\Mail\OrderMail;
use Mail;
use DateTime;
use Carbon\Carbon;
use Cookie;
class CartController extends Controller
{

	public function __construct() {
		$this->employee = Role::where('name', 'Employee')->first();
		$this->shipper  = Role::where('name', 'Shipper')->first();
		$this->status   = OrderStatus::where('order_status_name', 'Hủy')->first();
		$this->middleware('auth:api')->except('checkCoupon');
	}

	//CANCEL ORDER
	public function cancelOrder(Request $request) {
		$orderId = $request->id;
		$userId  = $request->owner['id'];
		$storeId = $request->store['id'];
		if(auth('api')->user()->id == $userId) {
			$order = RegularOrder::where(function($query) use ($orderId, $userId, $storeId) {

				$query->where('id', '=', $orderId);
				$query->where('user_id', '=', $userId);
				$query->where('status_id', '!=', $this->status->id);
				$query->where('store_id', '=', $storeId);

			})->first();
			if(!is_null($order)) {

				$order->status_id = $this->status->id;
				$order->note      = serialize($request->orderNotes);
				$order->save();
				$res = [
					'type'    => 'success',
					'message' => 'Cancelled order successfully.',
					'data'    => new OrderResource($order->load('user', 'shipper', 'employee', 'store', 'payment'))
				];
				
				return response($res, 200);
			}
		}
		$res = [
			'type'    => 'error',
			'message' => 'Something went wrong.',
			'data'    => []
		];
		return response($res, 500);
		
	}

	//GET ORDER DETAILS
	public function getOrderDetail(Request $request) {
		if($request->filled('orderId')) {
			$order = RegularOrder::where('user_id', '=', auth('api')->user()->id)->where('id', '=', $request->orderId)->first();
			$res   = [
				'type'    => 'success',
				'message' => 'Get order details successfully.',
				'data'    => new OrderResource($order->load('products', 'user', 'store', 'employee', 'shipper'))
			];	
			return response($res);
		}
	}

	//GET ORDERS 
	public function orderByFilter(Request $request) {
		$fromDate = $request->fromDate;
		$toDate   = $request->toDate;
		$status   = (int)$request->statusId;
		if($status == 0) {
			$orders  = RegularOrder::where('user_id', '=', auth('api')->user()->id)->where(function($query) use ($fromDate, $toDate) {
				$query->whereDate('created_at', '>=', $fromDate);
				$query->whereDate('created_at', '<=', $toDate);
			})->orderBy('id', 'desc')->get();
		} else {
			$orders  = RegularOrder::where('user_id', '=', auth('api')->user()->id)->where(function($query) use ($fromDate, $toDate, $status) {
				$query->whereDate('created_at', '>=', $fromDate);
				$query->whereDate('created_at', '<=', $toDate);
			})->where('status_id', '=', $status)->orderBy('id', 'desc')->get();
		}

		$res     = [
			'type'    => 'success',
			'message' => 'Get history information successfully.',
			'data'    => OrderResource::collection($orders->load('user', 'shipper', 'employee', 'store', 'payment'))
		];

		return response($res, 200);
	}

	//GET PRODUCT BY STORE
	public function getProductByStore(Request $request) {
		$storeId = $request->storeId; 
		$store   = Store::findorFail($storeId);
		$res     = [
			'type'    => 'success',
			'message' => 'Get product successfully.',
			'data'    => ProductResource::collection($store->products)
		];
		return response($res, 200);
	}

	//CHECK OUT
	public function checkOut(Request $request) {

		$now = Carbon::now()->toDateTimeString();

		if($request->filled('confirmed', 'name', 'phone', 'address', 'date', 'time', 'memo', 'total', 'subTotal', 'userId', 'paymentMethod', 'distance','items', 'city', 'store')) {
			// Check confirmed
			if($request->confirmed) {
				$cityId     = $request->city['id'];
				$storeId    = $request->store['id'];
				$userId     = $request->userId;
				$secret     = $request->coupon['secret'];
				$coupon     = $request->coupon;
				//Find store has truly
				$store  = Store::ofCity($cityId)->findorFail($storeId);
				if($store && auth('api')->user()->id == $userId) {
					
					if($store->verified) {
						$reduceShippingCost = $this->reduceShippingCost($coupon);
					} else {
						$reduceShippingCost = 0;
					}
					
					$regular_order      = $this->createRegularOrder($request->all(), $reduceShippingCost);
					$actual_order       = $this->createActualOrder($regular_order, $store);

					if($coupon) {
						$coupon = Coupon::hasStore($storeId)->unexpired()->checkToken($secret)->first();	
						if($coupon) {
							$regular_order->update([
								'coupon'           => $coupon->coupon,
								'secret'           => $coupon->token,
								'discount_percent' => $coupon->discount_percent,
								'discount_price'   => $coupon->discount_price,
								'discount_total'   => $this->discountCoupon($request->subTotal, $coupon)
							]);
						}
					}

					foreach($request->items as $data) {
						$regular_order->products()->attach([$data['id'] => ['product_id' => $data['id'], 'quantity' => $data['qty'], 'price' => $data['size']['price'], 'total' => $data['subTotal'], 'memo' => $data['memo'], 'toppings' => serialize($data['toppings'])]]);
						$actual_order->products()->attach([$data['id'] => ['product_id' => $data['id'], 'quantity' => $data['qty'], 'price' => $data['size']['price'], 'total' => $data['subTotal'], 'memo' => $data['memo'], 'toppings' => serialize($data['toppings'])]]);
					}

					$this->checkFreeShip();
					//Notify to employee in City
					// $employees = User::where('role_id', '=', $this->employee->id)->get();

					// foreach($employees as $user) {
					// 	$user->notify(new CheckoutNotification($regular_order)); 
					// }		

					// Mail::to('sp.dofuu@gmail.com')->send(new OrderMail($regular_order));

					// $res = [
					// 	'type'    => 'success',
					// 	'message' => 'Check out cart successfully.',
					// 	'data'    => []
					// ];

					// return response($res, 201);
				}
			}
		}
	}

	//CREATE REGULAR ORDER
	public function createRegularOrder($request, $reduceShippingCost) {
		$regular_order = RegularOrder::create([
			'name'            => $request['name'],
			'address'         => $request['address'],
			'lat'             => $request['lat'],
			'lng'             => $request['lng'],
			'distance'        => $request['distance'],
			'phone'           => $request['phone'],
			'date'            => $request['date'],
			'time'            => $request['time'],
			'delivery_price'  => (int)$request['deliveryPrice'] - $reduceShippingCost,
			'subtotal_amount' => $request['subTotal'],
			'amount'          => $request['total'],
			'memo'            => $request['memo'],
			'payment_id'      => $request['paymentMethod'],
			'status_id'       => 1,
			'user_id'         => $request['userId'],
			'store_id'        => $request['store']['id']
		]);
		return $regular_order;
	}

	// CREATE ACTUAL ORDER
	public function createActualOrder($regular_order, $store) {
		$actual_order = ActualOrder::create([
			'delivery_price'  => (int)$regular_order['delivery_price'],
			'subtotal_amount' => $regular_order['subtotal_amount'],
			'coupon'          => $regular_order['coupon'],
			'secret'          => $regular_order['secret'],
			'discount'        => $store['discount'],
			'discount_total'  => $this->discountStore($regular_order['subtotal_amount'], $store['discount']),
			'amount'          => $this->actualTotal($regular_order['subtotal_amount'], $this->discountStore($regular_order['subtotal_amount'], $store['discount'])),
			'order_id'        => $regular_order['id']
		]);
		return $actual_order;
	}

	// ACTUAL TOTAL
	public function actualTotal($subTotal, $discountPrice) {
		return round((int)$subTotal - (int)$discountPrice, -3);
	}

	//CALCULATE DISCOUNT FOR COUPON
	public function discountCoupon($subTotal, $coupon) {
		$discountTotal = round(((float)$subTotal * (int)$coupon->discount_percent/100)+(int)$coupon->discount_price, -3);
		$maxPrice      = $coupon->max_price;
		if($discountTotal > $maxPrice && $maxPrice != 0) {
			return (float)$maxPrice;
		} else {
			return $discountTotal;
		}
	}

	//CACULATE DISCOUNT FOR STORE
	public function discountStore($subTotal, $percent) {
		return round((int)$subTotal*(int)$percent/100, -3);
	}

	//CHECK FREE SHIP
	public function checkFreeShip() {
		$currentUser = auth('api')->user();
		if($currentUser->free_ship) {
			$currentUser->update([
				'free_ship' => 0
			]);
		}
	}

	public function reduceShippingCost($coupon) {
		$price     = 0;
		$now       = Carbon::now();
		$timeNow   = (int)str_replace(':', '', $now->format('H:i'));
		$startTime = (int)str_replace(':', '', '08:00');
		$endTime   = (int)str_replace(':', '', '16:00');
		$dayOfWeek = $now->dayOfWeek;

		if(!$coupon) {
			if($dayOfWeek > 0 && $dayOfWeek < 6) {
				if($timeNow >= $startTime && $timeNow < $endTime) {
					return $price = 12000;
				} 
			}
		} 
		return $price;
	}

}
