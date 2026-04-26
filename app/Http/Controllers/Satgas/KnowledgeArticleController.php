<?php

namespace App\Http\Controllers\Satgas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKnowledgeArticleRequest;
use App\Http\Requests\Admin\UpdateKnowledgeArticleRequest;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KnowledgeArticleController extends Controller
{
    public function index(): View
    {
        $articles = KnowledgeArticle::query()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('satgas.knowledge-articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('satgas.knowledge-articles.create', [
            'knowledgeArticle' => new KnowledgeArticle(),
            'categories' => KnowledgeCategory::query()->orderBy('name')->get(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(StoreKnowledgeArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isPublished = ($validated['status'] ?? null) === 'published';
        $thumbnailPath = $request->hasFile('cover_image')
            ? $request->file('cover_image')->store('knowledge-covers', 'public')
            : null;
        $sections = $this->syncSections(
            $validated['sections'] ?? [],
            $request,
        );

        KnowledgeArticle::query()->create([
            ...$validated,
            'content' => KnowledgeArticle::encodeStructuredSections($sections),
            'thumbnail_path' => $thumbnailPath,
            'slug' => $this->generateUniqueSlug($validated['slug'] ?? null, $validated['title']),
            'created_by' => $request->user()->id,
            'approved_by' => $isPublished ? $request->user()->id : null,
            'published_at' => $isPublished ? now() : null,
        ]);

        return redirect()
            ->route('satgas.knowledge-articles.index')
            ->with('status', 'Materi knowledge berhasil ditambahkan oleh Satgas.');
    }

    public function edit(KnowledgeArticle $knowledgeArticle): View
    {
        return view('satgas.knowledge-articles.edit', [
            'knowledgeArticle' => $knowledgeArticle,
            'categories' => KnowledgeCategory::query()->orderBy('name')->get(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateKnowledgeArticleRequest $request, KnowledgeArticle $knowledgeArticle): RedirectResponse
    {
        $validated = $request->validated();
        $isPublished = ($validated['status'] ?? null) === 'published';
        $thumbnailPath = $knowledgeArticle->thumbnail_path;
        $sections = $this->syncSections(
            $validated['sections'] ?? [],
            $request,
            $knowledgeArticle->structuredSections(),
        );

        if ($request->hasFile('cover_image')) {
            if ($thumbnailPath) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            $thumbnailPath = $request->file('cover_image')->store('knowledge-covers', 'public');
        }

        $knowledgeArticle->update([
            ...$validated,
            'content' => KnowledgeArticle::encodeStructuredSections($sections),
            'thumbnail_path' => $thumbnailPath,
            'slug' => $this->generateUniqueSlug($validated['slug'] ?? null, $validated['title'], $knowledgeArticle->id),
            'approved_by' => $isPublished ? $request->user()->id : $knowledgeArticle->approved_by,
            'published_at' => $isPublished
                ? ($knowledgeArticle->published_at ?? now())
                : null,
        ]);

        return redirect()
            ->route('satgas.knowledge-articles.index')
            ->with('status', 'Materi knowledge berhasil diperbarui oleh Satgas.');
    }

    public function destroy(KnowledgeArticle $knowledgeArticle): RedirectResponse
    {
        if ($knowledgeArticle->thumbnail_path) {
            Storage::disk('public')->delete($knowledgeArticle->thumbnail_path);
        }

        foreach ($knowledgeArticle->structuredSections() as $section) {
            if (! empty($section['media_path'])) {
                Storage::disk('public')->delete($section['media_path']);
            }
        }

        $knowledgeArticle->delete();

        return redirect()
            ->route('satgas.knowledge-articles.index')
            ->with('status', 'Materi knowledge berhasil dihapus oleh Satgas.');
    }

    protected function generateUniqueSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug ?: $title);
        $candidate = $baseSlug !== '' ? $baseSlug : 'materi-k3';
        $counter = 1;

        while (
            KnowledgeArticle::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->withTrashed()
                ->exists()
        ) {
            $candidate = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    protected function statusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'review' => 'Review',
            'published' => 'Published',
            'archived' => 'Archived',
        ];
    }

    protected function syncSections(array $sections, Request $request, array $existingSections = []): array
    {
        $existingById = collect($existingSections)->keyBy('id');
        $keptImagePaths = [];

        $normalized = collect($sections)
            ->map(function (array $section, int $index) use ($request, $existingById, &$keptImagePaths) {
                $id = (string) ($section['id'] ?? 'section-' . ($index + 1));
                $existing = $existingById->get($id, []);
                $mediaType = $section['media_type'] ?? 'none';
                $mediaPath = $existing['media_path'] ?? null;

                if ($mediaType === 'image' && $request->hasFile("sections.$index.media_image")) {
                    if ($mediaPath) {
                        Storage::disk('public')->delete($mediaPath);
                    }

                    $mediaPath = $request->file("sections.$index.media_image")->store('knowledge-sections', 'public');
                }

                if ($mediaType !== 'image' && $mediaPath) {
                    Storage::disk('public')->delete($mediaPath);
                    $mediaPath = null;
                }

                if ($mediaPath) {
                    $keptImagePaths[] = $mediaPath;
                }

                return [
                    'id' => $id,
                    'title' => trim((string) ($section['title'] ?? '')),
                    'body' => trim((string) ($section['body'] ?? '')),
                    'list_style' => $section['list_style'] ?? 'paragraph',
                    'media_type' => $mediaType,
                    'media_path' => $mediaPath,
                    'media_url' => $mediaType === 'video' ? trim((string) ($section['media_url'] ?? '')) : null,
                    'caption' => trim((string) ($section['caption'] ?? '')),
                ];
            })
            ->filter(fn (array $section) => $section['title'] !== '' || $section['body'] !== '' || $section['media_type'] !== 'none')
            ->values()
            ->all();

        $existingById
            ->pluck('media_path')
            ->filter()
            ->reject(fn ($path) => in_array($path, $keptImagePaths, true))
            ->each(fn ($path) => Storage::disk('public')->delete($path));

        return $normalized;
    }
}
