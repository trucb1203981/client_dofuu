<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth('api')->user();

        return [
            'id'          => $this->id,
            'body'        => $this->body,
            'totalReply'  => $this->totalReply($this->id),
            'name'        => $this->user->name,
            'avatar'      => $this->user->avatar,
            'likes'       => $this->likes,
            'checkedLike' => ($user && $this->likes()->byUser($user->id)->first()) ? true : false,
            'owned'       => ($user && $this->user_id === $user->id) ? true : false,
            'createdAt'   => $this->created_at
        ];
    }
}
