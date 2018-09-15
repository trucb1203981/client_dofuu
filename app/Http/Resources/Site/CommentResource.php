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
        return [
            'id'         => $this->id,
            'body'       => $this->body,
            'totalReply' => $this->totalReply($this->id),
            'name'       => $this->user->name,
            'avatar'     => $this->user->avatar,
            'likes'      => $this->likes,
            'createdAt'  => $this->created_at
        ];
    }
}
