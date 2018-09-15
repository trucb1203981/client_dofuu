<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Comment;
use App\Http\Resources\Site\CommentResource;

class CommentController extends Controller
{
	const COMMENT_STORE_END_POINT       = 'commentStoreEndpoint';
	const FETCH_COMMENT_STORE_END_POINT = 'fetchCommentStoreEndpoint';

	public function __construct(){

		$this->middleware('auth:api', ['except' => ['fetchComments', 'fetchCommentReplies']]);

	}

	public function fetchComments(Request $request, $storeId) {
		$pageSize = 6;
		$offset	  = (int)$request->offset;

		if($request->name === CommentController::FETCH_COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);
			if($store) {
				$comments = $store->comments()->parentComment()->limit($pageSize)->offset($offset)->orderBy('created_at', 'desc')->get();
			}
			$res = [
				'type'     => 'success',
				'message'  => 'Get comments successfully',
				'comments' => CommentResource::collection($comments)
			];
			return response($res, 200);
		}
	}

	public function fetchCommentReplies(Request $request, $storeId) {
		$pageSize = 6;
		$offset	  = $request->offset;
		$parentId = $request->parentId;

		if($request->name === CommentController::FETCH_COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);
			if($store) {
				$comments = $store->comments()->hasParentComment($parentId)->limit($pageSize)->offset($offset)->orderBy('created_at', 'asc')->get();
			}
			$res = [
				'type'     => 'success',
				'message'  => 'Get comments successfully',
				'comments' => CommentResource::collection($comments)
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
					'likes'		 => 0,
					'user_id'    => auth()->user()->id
				]);
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
			'comments' => []
		];
		return response($res, 500);
	}

	public function addCommentReply(Request $request, $storeId) {
		if($request->name === CommentController::COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);
			if($store) {
				$comment = $store->comments()->create([
					'body'       => $request->body,
					'ip_address' => $request->ip(),
					'user_agent' => $request->header('User-Agent'),
					'parent_id'  => $request->parent_id,
					'likes'		 => 0,
					'user_id'    => auth()->user()->id
				]);
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
			'comments' => []
		];
		return response($res, 500);
	}
}
