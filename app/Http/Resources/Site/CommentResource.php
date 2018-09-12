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
            'totalReply' => $this->where('parent_id', $this->id)->count(),
            'name'       => $this->user->name,
            'avatar'     => $this->user->avatar,
            'created_at' => $this->created_at
        ];
    }
}
