<?php
  
namespace App\Http\Resources;
  
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;
  
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        if($this->relationLoaded('category'))
            $category = $this->when($this->relationLoaded('category'), new CategoryResource($this->category));
        else
            $category = $this->category_id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'due_date_at' => $this->due_date_at,
            'category' => $category,
        ];
    }
}
