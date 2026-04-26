<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKnowledgeArticleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_published_knowledge_article_with_generated_slug(): void
    {
        $admin = $this->createAdminUser();
        $category = KnowledgeCategory::query()->create([
            'name' => 'Pelatihan Dasar',
            'slug' => 'pelatihan-dasar',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.knowledge-articles.store'), [
                'knowledge_category_id' => $category->id,
                'title' => 'Panduan Inspeksi Harian',
                'summary' => 'Ringkasan panduan inspeksi.',
                'content' => 'Isi lengkap panduan inspeksi harian.',
                'reading_time' => '6 menit',
                'status' => 'published',
                'sections' => [
                    [
                        'id' => 'section-1',
                        'title' => 'Pemeriksaan awal',
                        'body' => 'Isi lengkap panduan inspeksi harian.',
                        'list_style' => 'paragraph',
                        'media_type' => 'none',
                        'media_url' => null,
                        'caption' => null,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.knowledge-articles.index'))
            ->assertSessionHas('status', 'Materi knowledge berhasil ditambahkan.');

        $article = KnowledgeArticle::query()->firstOrFail();

        $this->assertSame('panduan-inspeksi-harian', $article->slug);
        $this->assertSame($admin->id, $article->created_by);
        $this->assertSame($admin->id, $article->approved_by);
        $this->assertNotNull($article->published_at);
    }

    public function test_admin_can_update_knowledge_article_and_publish_it(): void
    {
        $admin = $this->createAdminUser();
        $category = KnowledgeCategory::query()->create([
            'name' => 'Pelaporan',
            'slug' => 'pelaporan',
        ]);

        $article = KnowledgeArticle::query()->create([
            'knowledge_category_id' => $category->id,
            'title' => 'Draft Pelaporan',
            'slug' => 'draft-pelaporan',
            'content' => 'Draft isi materi.',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.knowledge-articles.update', $article), [
                'knowledge_category_id' => $category->id,
                'title' => 'Panduan Pelaporan Final',
                'slug' => 'panduan-pelaporan-final',
                'summary' => 'Versi final materi pelaporan.',
                'content' => 'Isi final materi pelaporan.',
                'reading_time' => '4 menit',
                'status' => 'published',
                'sections' => [
                    [
                        'id' => 'section-1',
                        'title' => 'Alur pelaporan final',
                        'body' => 'Isi final materi pelaporan.',
                        'list_style' => 'paragraph',
                        'media_type' => 'none',
                        'media_url' => null,
                        'caption' => null,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.knowledge-articles.index'))
            ->assertSessionHas('status', 'Materi knowledge berhasil diperbarui.');

        $article->refresh();

        $this->assertSame('Panduan Pelaporan Final', $article->title);
        $this->assertSame('panduan-pelaporan-final', $article->slug);
        $this->assertSame('published', $article->status);
        $this->assertSame($admin->id, $article->approved_by);
        $this->assertNotNull($article->published_at);
    }

    public function test_admin_can_soft_delete_knowledge_article(): void
    {
        $admin = $this->createAdminUser();

        $article = KnowledgeArticle::query()->create([
            'title' => 'Materi Lama',
            'slug' => 'materi-lama',
            'content' => 'Konten lama.',
            'status' => 'archived',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.knowledge-articles.destroy', $article))
            ->assertRedirect(route('admin.knowledge-articles.index'))
            ->assertSessionHas('status', 'Materi knowledge berhasil dihapus.');

        $this->assertSoftDeleted('knowledge_articles', [
            'id' => $article->id,
        ]);
    }

    protected function createAdminUser(): User
    {
        $adminRole = Role::query()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);

        return User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }
}
