<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\CheckoutNotification;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\RegularOrder;
use App\Models\ActualOrder;
use App\Models\OrderStatus;
use App\Models\Role;
use App\Models\User;
use App\Mail\OrderMail;
use Mail;
use DateTime;
use Carbon\Carbon; 

class CartController extends Controller
{
	public function __construct() {
		$this->employee = Role::where('name', 'Employee')->first();
		$this->shipper  = Role::where('name', 'Shipper')->first();
		$this->status   = OrderStatus::where('order_status_name', 'Hủy')->first();
		$this->middleware('auth:api')->except('checkCoupon');
	}

    public function checkout(Request $request) {

		$now = Carbon::now()->toDateTimeString();
		$storeId    = $request->cart['store']['id'];
		$userId     = $request->userId;
		$secret     = $request->coupon['secret'];
		$coupon     = $request->coupon;

		//Find store has truly
		$store  = Store::findorFail($storeId);


		if($store && auth('api')->user()->id === $userId) {
			
				
			$regular_order      = $this->createRegularOrder($request->all());
			$actual_order       = $this->createActualOrder($regular_order, $store);
			
			if($coupon) {
				$coupon = Coupon::hasStore($storeId)->unexpired()->checkToken($secret)->first();	
				if($coupon) {
					$regular_order->update([
						'coupon'           => $coupon->coupon,
						'secret'           => $coupon->token,
						'discount_percent' => $coupon->discount_percent,
						'discount_price'   => $coupon->discount_price,
						'discount_total'   => $this->discountCoupon($request->cart['total'], $coupon)
					]);
				}
			}
			
			foreach($request->cart['items'] as $data) {
				$regular_order->products()->attach([$data['id'] => ['product_id' => $data['id'], 'quantity' => $data['qty'], 'price' => $data['size']['price'], 'total' => $data['subTotal'], 'memo' => $data['memo'], 'toppings' => serialize($data['toppings'])]]);
				$actual_order->products()->attach([$data['id'] => ['product_id' => $data['id'], 'quantity' => $data['qty'], 'price' => $data['size']['price'], 'total' => $data['subTotal'], 'memo' => $data['memo'], 'toppings' => serialize($data['toppings'])]]);
			}

		
			// Notify to employee in City
			$employees = User::where('role_id', '=', $this->employee->id)->get();

			foreach($employees as $user) {
				$user->notify(new CheckoutNotification($regular_order)); 
			}		

			Mail::to('sp.dofuu@gmail.com')->send(new OrderMail($regular_order));

			$res = [
				'type'    => 'success',
				'message' => 'Check out cart successfully.',
				'data'    => []
			];

			return response($res, 201);
		}
	}

	//CREATE REGULAR ORDER
	public function createRegularOrder($request) {
		$regular_order = RegularOrder::create([
			'name'                => $request['name'],
			'address'             => $request['address'],
			'address_description' => $request['addressDescription'],
			'lat'                 => $request['lat'],
			'lng'                 => $request['lng'],
			'place_id'            => $request['placeId'],
			'distance'            => $request['distance'],
			'phone'               => $request['phone'],
			'date'                => $request['date'],
			'time'                => $request['time'],
			'delivery_price'      => (int)$request['deliveryPrice'],
			'subtotal_amount'     => $request['cart']['total'],
			'amount'              => $request['total'],
			'memo'                => $request['memo'],
			
			'payment_id'          => $request['paymentMethod'],
			'status_id'           => 1,
			'user_id'             => $request['userId'],
			'store_id'            => $request['cart']['instance']
		]);
		return $regular_order;
	}

	// CREATE ACTUAL ORDER
	public function createActualOrder($regular_order, $store) {
		$discountTotal = $this->discountStore($regular_order['subtotal_amount'], $store['discount']);
		$actual_order = ActualOrder::create([
			'delivery_price'  => (int)$regular_order['delivery_price'],
			'subtotal_amount' => $regular_order['subtotal_amount'],
			'coupon'          => $regular_order['coupon'],
			'secret'          => $regular_order['secret'],
			'discount'        => $store['discount'],
			'discount_total'  => $discountTotal,
			'amount'          => $this->actualTotal($regular_order['subtotal_amount'], $discountTotal),
			'order_id'        => $regular_order['id']
		]);
		return $actual_order;
	}

	//CACULATE DISCOUNT FOR STORE
	public function discountStore($subTotal, $percent) {
		return round((int)$subTotal*(int)$percent/100, -3);
	}

	// ACTUAL TOTAL
	public function actualTotal($subTotal, $discountPrice) {
		return round((int)$subTotal - (int)$discountPrice, -3);
	}

	public function checkCoupon($coupon, $storeId) {
		
	}

	//CALCULATE DISCOUNT FOR COUPON
	public function discountCoupon($subTotal, $coupon) {
		$discountTotal = ((float)$subTotal * (int)$coupon->discount_percent/100)+(int)$coupon->discount_price;
		$maxPrice      = $coupon->max_price;
		if($discountTotal > $maxPrice && $maxPrice != 0) {
			return (float)$maxPrice;
		} else {
			return $discountTotal;
		}
	}


}
