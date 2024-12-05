<?php

namespace App\Http\Resources;

use App\Models\Organisation;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchInstituteContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Organisation) {
            return $this->institute();
        }

        return $this->contact();
    }

    /**
     * @return array
     */
    public function contact(): array
    {
        return [
            'type' => 'contact',
            'id'   => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
        ];
    }

    /**
     * @return array
     */
    public function institute(): array
    {
        return [
            'type' => 'institute',
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
