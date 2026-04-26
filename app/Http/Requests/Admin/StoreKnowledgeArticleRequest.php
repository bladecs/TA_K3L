<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKnowledgeArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true || $this->user()?->isSatgas() === true;
    }

    public function rules(): array
    {
        return [
            'knowledge_category_id' => ['nullable', 'integer', 'exists:knowledge_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string'],
            'reading_time' => ['nullable', 'string', 'max:50'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', Rule::in(['draft', 'review', 'published', 'archived'])],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'string', 'max:100'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.body' => ['nullable', 'string'],
            'sections.*.list_style' => ['nullable', Rule::in(['paragraph', 'dash', 'bullet', 'number'])],
            'sections.*.media_type' => ['nullable', Rule::in(['none', 'image', 'video'])],
            'sections.*.media_url' => ['nullable', 'string', 'max:255'],
            'sections.*.caption' => ['nullable', 'string', 'max:255'],
            'sections.*.media_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
