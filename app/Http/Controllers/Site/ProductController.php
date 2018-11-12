<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Site\ProductResource;
use App\Models\Store;
class ProductController extends Controller
{

	public function getProductByStore(Request $request, $id) {
		$store_id = (int) $id;
		$store = Store::with(['products' => function($query) {
			return $query->show();
		}])->findorFail($id);
		$res = [
			'type'    => 'success',
			'message' => 'Get product successfully.',
			'data'    => ProductResource::collection($store->products)
		];

		return response($res, 200);
	}
}
