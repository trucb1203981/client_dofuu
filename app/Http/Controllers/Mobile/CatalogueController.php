<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\CatalogueResource;
use App\Models\Store;
class CatalogueController extends Controller
{
    public function fetch(Request $request) {
		$request->endPoint = 'fetch_menu';
		$city_id           = (int) $request->cityId;
		$store_id          = (int) $request->storeId;
		$store             = Store::where(function ($query) use ($city_id) {
				$query->ofCity($city_id);
				$query->active();
				$query->show();
			})->with(['catalogues' => function ($query) {
				return $query->show();
			}])->findOrFail($store_id);
		
		$results = $this->respondSuccess('Get catalogues ', $store->catalogues->map(function($query) {return $query;})->where('catalogue_show', 1)->sortBy('catalogue')->sortByDesc('priority'));
		return response($results);
    }
    protected function respondSuccess($message, $data, $type= 'many', $status = 200, $pagination = []) {
		$res = [
			'status'  => 'success',
			'message' => $message . ' successfully.',
		];

		switch ($type) {

			case 'one':
			$res['catalogue']  = new CatalogueResource($data);
			break;

			case 'many':
			$res['catalogues'] = CatalogueResource::collection($data);
			if (count($pagination) > 0) {
				$res['pagination'] = $pagination;
			}
			break;
		}

		return $res;
	}

	protected function pagination($data) {
		return $pagination = [
			'total'        => $data->total(),
			'per_page'     => $data->perPage(),
			'from'         => $data->firstItem(),
			'current_page' => $data->currentPage(),
			'to'           => $data->lastItem(),
			'last_page'    => $data->lastPage(),
		];
	}
}
