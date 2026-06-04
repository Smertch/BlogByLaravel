<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\SiteLanguage;
use App\Models\SiteTranslation;
use Illuminate\Http\Request;

final class SiteUi
{
    /**
     * @return list<SiteLanguage>
     */
    public function languages(): array
    {
        return SiteLanguage::cases();
    }

    public function language(?Request $request): SiteLanguage
    {
        $candidate = $request?->user()?->site_language
            ?? $request?->session()->get('site_language')
            ?? SiteLanguage::EN->value;

        return SiteLanguage::tryFrom((string) $candidate) ?? SiteLanguage::EN;
    }

    public function setLanguage(Request $request, SiteLanguage $language): void
    {
        $request->session()->put('site_language', $language->value);

        $user = $request->user();
        if ($user !== null) {
            $user->forceFill(['site_language' => $language->value])->save();
        }
    }

    /**
     * @param array<string, string> $replace
     */
    public function trans(string $alias, array $replace = [], ?SiteLanguage $language = null): string
    {
        $language ??= SiteLanguage::EN;

        $text = SiteTranslation::query()
            ->where('alias', $alias)
            ->where('language_type', $language->value)
            ->value('translate');

        $text = is_string($text) && $text !== ''
            ? $text
            : ($this->defaults()[$language->value][$alias] ?? $this->defaults()[SiteLanguage::EN->value][$alias] ?? $alias);

        foreach ($replace as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function defaults(): array
    {
        return [
            'EN' => [
                'nav.brand' => 'Blog',
                'nav.admin' => 'Admin',
                'nav.account' => 'Account',
                'nav.new_post' => 'New post',
                'nav.logout' => 'Log out',
                'feed.title' => 'Posts',
                'feed.subtitle' => 'Share updates, discuss ideas, and keep the team in sync.',
                'feed.search_placeholder' => 'Search posts',
                'feed.empty' => 'No posts yet.',
                'feed.empty_search' => 'No posts match your search.',
                'post.title' => 'Title',
                'post.content' => 'Content',
                'post.publish' => 'Publish',
                'post.update' => 'Update',
                'post.edit' => 'Edit',
                'post.delete' => 'Delete',
                'post.likes' => 'Likes',
                'post.comments' => 'Comments',
                'comment.placeholder' => 'Write a comment',
                'comment.add' => 'Comment',
                'comment.delete' => 'Delete',
                'flash.invalid_csrf' => 'The form expired. Try again.',
                'flash.title_empty' => 'Title cannot be empty.',
                'flash.comment_empty' => 'Comment cannot be empty.',
                'flash.post_published' => 'Post published.',
                'flash.post_updated' => 'Post updated.',
                'flash.post_removed' => 'Post removed.',
                'flash.comment_removed' => 'Comment removed.',
            ],
            'ES' => [
                'nav.account' => 'Cuenta',
                'nav.new_post' => 'Nueva entrada',
                'nav.logout' => 'Salir',
                'feed.title' => 'Entradas',
                'feed.subtitle' => 'Comparte novedades, ideas y conversaciones.',
                'feed.search_placeholder' => 'Buscar entradas',
                'feed.empty' => 'Todavia no hay entradas.',
                'feed.empty_search' => 'No hay entradas para esta busqueda.',
                'post.publish' => 'Publicar',
                'post.update' => 'Actualizar',
                'post.edit' => 'Editar',
                'post.delete' => 'Eliminar',
                'post.likes' => 'Me gusta',
                'post.comments' => 'Comentarios',
                'comment.placeholder' => 'Escribe un comentario',
                'comment.add' => 'Comentar',
                'comment.delete' => 'Eliminar',
            ],
            'DE' => [
                'nav.account' => 'Konto',
                'nav.new_post' => 'Neuer Beitrag',
                'nav.logout' => 'Abmelden',
                'feed.title' => 'Beitrage',
                'feed.subtitle' => 'Teile Updates, Ideen und Diskussionen.',
                'feed.search_placeholder' => 'Beitrage suchen',
                'feed.empty' => 'Noch keine Beitrage.',
                'feed.empty_search' => 'Keine passenden Beitrage gefunden.',
                'post.publish' => 'Veroffentlichen',
                'post.update' => 'Aktualisieren',
                'post.edit' => 'Bearbeiten',
                'post.delete' => 'Loschen',
                'post.likes' => 'Likes',
                'post.comments' => 'Kommentare',
                'comment.placeholder' => 'Kommentar schreiben',
                'comment.add' => 'Kommentieren',
                'comment.delete' => 'Loschen',
            ],
            'UA' => [
                'nav.brand' => 'Blog',
                'nav.admin' => 'Admin',
                'nav.account' => 'Account',
                'nav.new_post' => 'New post',
                'nav.logout' => 'Log out',
                'feed.title' => 'Posts',
                'feed.subtitle' => 'Share updates, discuss ideas, and keep the team in sync.',
                'feed.search_placeholder' => 'Search posts',
                'feed.empty' => 'No posts yet.',
                'feed.empty_search' => 'No posts match your search.',
                'post.publish' => 'Publish',
                'post.update' => 'Update',
                'post.edit' => 'Edit',
                'post.delete' => 'Delete',
                'post.likes' => 'Likes',
                'post.comments' => 'Comments',
                'comment.placeholder' => 'Write a comment',
                'comment.add' => 'Comment',
                'comment.delete' => 'Delete',
            ],
        ];
    }
}
