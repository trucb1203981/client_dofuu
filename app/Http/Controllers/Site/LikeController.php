<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Like;
use App\Models\Comment;
use App\Http\Resources\Site\StoreResource;
use App\Http\Resources\Site\CommentResource;

class LikeController extends Controller
{
	const END_POINT                    = 'likeEndpoint';
	const LIKE_COMMENT_END_POINT       = 'likeCommentEndPoint';
	const CHECK_LIKE_COMMENT_END_POINT = 'checkLikeCommentEndPoint';

	public function __construct(){

		$this->middleware('auth:api');

	}

	public function toggleLikeStore(Request $request, $id) {
		if($request->name === LikeController::END_POINT) {

			$store = Store::find($id);

			if($store) {

				$user = auth()->user();
				$like = $store->likes()->byUser($user->id)->first();

				if(!$like) {

					$store->likes()->create([
						'ip_address' => $request->ip(),
						'user_agent' => $request->header('User-Agent'),
						'user_id'    => $user->id
					]);

					$store->update(['likes' => ++$store->likes]); 

					$status  = 'success';
					$message = 'Đã thích thành công';

				} else {

					$like->delete();
					$store->update(['likes' => --$store->likes]); 

					$status  = 'error';
					$message = 'Bỏ thích thành công';
				}

				$res = [
					'type'    => $status,
					'message' => $message,
					'store'   => new StoreResource($store->load('activities', 'catalogues', 'toppings'))
				];
				
				return response($res, 200);
			}
		}

		$res    = [
			'type'     => 'error',
			'messsage' => 'Something went wrong!!!',
			'data'     => []
		];
		return response($res, 500);
	}

	public function checkLikeStore(Request $request, $id) {
		if($request->name === LikeController::END_POINT) {
			$store = Store::find($id);
			if($store) {
				$user = auth()->user();
				$like = $store->likes()->byUser($user->id)->first();

				if($like) {

					$status  = 'success';
					$message = 'Đã like';
					$update  = true;

				} else {

					$status  = 'error';
					$message = 'Chưa like';
					$update  = false;

				}

				$res = [
					'type'    => $status,
					'message' => $message,
					'like'    => $update
				];

				return response($res, 200);
			}
		}
	}

	public function toggleLikeComment(Request $request, $id) {
		if($request->name === LikeController::LIKE_COMMENT_END_POINT) {

			$comment = Comment::find($id);

			if($comment) {

				$user = auth()->user();

				$like = $comment->likes()->byUser($user->id)->first();

				if(!$like) {

					$comment->likes()->create([
						'ip_address' => $request->ip(),
						'user_agent' => $request->header('User-Agent'),
						'user_id'    => $user->id
					]);

					$comment->update(['likes' => ++$comment->likes]); 

					$status  = 'success';
					$message = 'Đã thích thành công';

				} else {

					$like->delete();
					$comment->update(['likes' => --$comment->likes]); 

					$status  = 'error';
					$message = 'Bỏ thích thành công';
				}

				$res = [
					'type'    => $status,
					'message' => $message,
					'comment' => new CommentResource($comment)
				];
				
				return response($res, 200);
			}
		}

		$res    = [
			'type'     => 'error',
			'messsage' => 'Something went wrong!!!',
			'data'     => []
		];
		return response($res, 500);
	}

	public function checkLikeComment(Request $request, $id) {
		if($request->name === LikeController::CHECK_LIKE_COMMENT_END_POINT) {
			$store = Store::find($id);
			if($store) {
				$user = auth()->user();
				var_dump($store->comments()->where('id', 32)->likes()->get());
				die();
				$like = $store->comments()->likes()->byUser($user->id)->first();

				// if($like) {

				// 	$status  = 'success';
				// 	$message = 'Đã like';
				// 	$update  = true;

				// } else {

				// 	$status  = 'error';
				// 	$message = 'Chưa like';
				// 	$update  = false;

				// }

				// $res = [
				// 	'type'    => $status,
				// 	'message' => $message,
				// 	'like'    => $update
				// ];

				return response($like, 200);
			}
		}
	}
}
