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

		$this->middleware('auth:api', ['except' => ['fetchComments', 'fetchReplies']]);

	}

	public function fetchComments(Request $request, $storeId) {
		$pageSize = 6;
		$offset	  = (int)$request->offset;

		if($request->endpoint === CommentController::FETCH_COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);
			if($store) {
				$comments = $store->comments()->parentComment()->limit($pageSize)->offset($offset)->orderBy('created_at', 'desc')->get();

				return $this->respondSuccess('Get comments', $comments, 200, 'many');
			}

		}

		return $this->respondError();
	}

	public function fetchReplies(Request $request, $storeId) {
		$pageSize = 6;
		$offset	  = $request->offset;
		$parentId = $request->parentId;

		if($request->endpoint === CommentController::FETCH_COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);
			if($store) {
				$comments = $store->comments()->hasParentId($parentId)->limit($pageSize)->offset($offset)->orderBy('created_at', 'asc')->get();
			}

			return $this->respondSuccess('Get replies', $comments, 200, 'many');
		}

		return $this->respondError();
	}

	public function addComment(Request $request, $storeId) {
		if($request->endpoint === CommentController::COMMENT_STORE_END_POINT) {
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

				return $this->respondSuccess('Added comment', $comment, 201, 'one');
			}
		}

		return $this->respondError();
	}

	public function addReply(Request $request, $storeId) {
		if($request->endpoint === CommentController::COMMENT_STORE_END_POINT) {
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

				return $this->respondSuccess('Added reply', $comment, 201, 'one');
			}
		}
		return $this->respondError();
	}

	public function updateComment(Request $request, $storeId) {
		$body      = $request->body;
		$commentId = $request->id;
		$userId    = auth()->user()->id;

		if($request->endpoint == CommentController::COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);

			if($store) {
				$comment = $store->comments()->byUser($userId)->byId($commentId)->first();
				if($comment) {

					$comment->body       = $body;
					$comment->ip_address = $request->ip();
					$comment->user_agent = $request->header('User-Agent');
					$comment->save();

					return $this->respondSuccess('Updated comment', $comment, 200, 'one');
				}
			}
		}

		return $this->respondError();
	}

	public function removeComment(Request $request, $storeId) {
		
		$commentId = $request->id;
		$userId    = auth()->user()->id;
		
		if($request->endpoint == CommentController::COMMENT_STORE_END_POINT) {
			$store = Store::find($storeId);

			if($store) {
				$comment = $store->comments()->byUser($userId)->byId($commentId)->first();

				if($comment) {

					$store->comments()->hasParentId($comment->id)->delete();
					$comment->delete();
					
					return $this->respondSuccess('Removed comment', $comment, 200, 'one');
				}
			}
		}
	}

	protected function respondError()
	{
		$res = [
			'type'    => 'error',
			'message' => 'Something went wrongs.',
			'data'    =>  []
		];
		
		return response($res, 500);
	}

	protected function respondSuccess($message, $data, $status = 200, $type)
	{	
		$res = [
			'type'    => 'success',
			'message' => $message. ' successfully.',
		];

		switch ($type) {
			case 'one':
			$res['comment']  = new CommentResource($data);
			break;
			case 'many':
			$res['comments'] = CommentResource::collection($data);
			break;
		}

		return response($res, $status);
	}
}
