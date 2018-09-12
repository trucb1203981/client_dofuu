<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreComment;
use App\Http\Resources\Site\CommentResource;

class CommentController extends Controller
{
	const COMMENT_STORE_END_POINT       = 'commentStoreEndpoint';
	const FETCH_COMMENT_STORE_END_POINT = 'fetchCommentStoreEndpoint';

	public function __construct(){

		$this->middleware('auth:api', ['except' => ['fetchComments']]);

	}

	public function fetchComments(Request $request, $storeId) {

		if($request->name === CommentController::FETCH_COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);
			$res = [
				'type'     => 'success',
				'message'  => 'Get comments successfully',
				'comments' => CommentResource::collection($store->comments)
			];
			return response($res, 200);
		}
	}

	public function addComment(Request $request, $storeId) {
		if($request->name === CommentController::COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);
			if($store) {
				$comment = $store->comments()->create([
					'body'       => $request->body,
					'ip_address' => $request->ip(),
					'user_agent' => $request->header('User-Agent'),
					'parent_id'  => $request->parent_id,
					'user_id'    => auth()->user()->id
				]);
				var_dump($comment);
				$res = [
					'type'     => 'success',
					'message'  => 'Get comments successfully',
					'comment' => new CommentResource($comment)
				];
				return response($res, 200);
			}
		}
		$res = [
			'type'     => 'error',
			'message'  => 'Something went wrong',
			'comments' => CommentResource::collection($store->comments)
		];
		return response($res, 500);
	}
}
