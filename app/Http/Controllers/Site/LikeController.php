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
					'likes'   => $store->likes,
				];
				
				return response($res, 200);
			}
		}

		return $this->respondError();
	}

	public function toggleLikeComment(Request $request, $id) {
		if($request->endpoint === LikeController::LIKE_COMMENT_END_POINT) {

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
					'likes'   => $comment->likes
				];
				
				return response($res, 200);
			}
		}

		return $this->respondError();
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
}
