<x-default-layout>
    <x-slot:title>
        @if ($post->title)
            {{ __('ui.posts.show.title', [
                'post_title' => $post->title,
                'first_name' => $post->user->first_name,
                'last_name' => $post->user->last_name,
            ]) }}
        @else
            {{ __('ui.posts.show.title_without_post_title', [
                'first_name' => $post->user->first_name,
                'last_name' => $post->user->last_name,
            ]) }}
        @endif
    </x-slot>

    <x-slot:description>
        @if ($post->title)
            {{ __('ui.posts.show.description', [
                'post_title' => $post->title,
                'first_name' => $post->user->first_name,
                'last_name' => $post->user->last_name,
            ]) }}
        @else
            {{ __('ui.posts.show.description_without_post_title', [
                'first_name' => $post->user->first_name,
                'last_name' => $post->user->last_name,
            ]) }}
        @endif
    </x-slot>

    <article class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
        <header class="mb-6">
            @if ($post->title)
                <h1 class="text-3xl font-bold dark:text-white mb-2">
                    {{ $post->title }}
                </h1>
            @endif

            <p class="text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ url('@' . $post->user->username) }}">
                    {{ __('ui.posts.show.author', [
                        'first_name' => $post->user->first_name,
                        'last_name' => $post->user->last_name,
                    ]) }}
                </a>
                ·
                <span title="{{ $post->created_at->isoFormat('LLLL') }}">
                    {{ $post->created_at->diffForHumans() }}
                </span>
                @can('update', $post)
                    ·
                    <a href="{{ url('/posts/' . $post->id . '/edit') }}">
                        {{ __('ui.posts.edit.title_without_post_title') }}
                    </a>
                @endcan
                ·
                <span class="font-semibold">
                    ⭐ {{ $post->averageRating() }}/5 ({{ $post->ratings()->count() }} votes)
                </span>
            </p>
        </header>

        <div class="mb-4">
            {{-- Affichage de la photo --}}
            <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title ?? 'Photo du post' }}"
                class="w-full rounded-md object-cover max-h-[600px]">

            {{-- Menu déroulant EXIF, affiché seulement s'il y a des données --}}
            @if ($post->exif_data && count(array_filter($post->exif_data)) > 0)
                <details class="mt-1 mb-4 border border-gray-200 dark:border-gray-600 rounded-b-md overflow-hidden">
                    <summary
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 select-none">
                        Détails de la photo
                    </summary>

                    <div class="px-4 py-3 grid grid-cols-2 gap-x-6 gap-y-2 text-sm bg-gray-50 dark:bg-slate-700">

                        @if (isset($post->exif_data['width']) && isset($post->exif_data['height']))
                            <div class="text-gray-500 dark:text-gray-400">Dimensions</div>
                            <div class="dark:text-white font-medium">
                                {{ $post->exif_data['width'] }}x{{ $post->exif_data['height'] }}px
                            </div>
                        @endif

                        @if (isset($post->exif_data['file_size']))
                            <div class="text-gray-500 dark:text-gray-400">Taille</div>
                            <div class="dark:text-white font-medium">{{ $post->exif_data['file_size'] }} Mo</div>
                        @endif

                        @if (isset($post->exif_data['camera_make']) || isset($post->exif_data['camera_model']))
                            <div class="text-gray-500 dark:text-gray-400">Appareil</div>
                            <div class="dark:text-white font-medium">
                                {{ trim(($post->exif_data['camera_make'] ?? '') . ' ' . ($post->exif_data['camera_model'] ?? '')) }}
                            </div>
                        @endif

                        @if (isset($post->exif_data['lens']))
                            <div class="text-gray-500 dark:text-gray-400">Objectif</div>
                            <div class="dark:text-white font-medium">{{ $post->exif_data['lens'] }}</div>
                        @endif

                        @if (isset($post->exif_data['focal_length']))
                            <div class="text-gray-500 dark:text-gray-400">Focale</div>
                            <div class="dark:text-white font-medium">{{ $post->exif_data['focal_length'] }}mm</div>
                        @endif

                        @if (isset($post->exif_data['iso']))
                            <div class="text-gray-500 dark:text-gray-400">ISO</div>
                            <div class="dark:text-white font-medium">{{ $post->exif_data['iso'] }}</div>
                        @endif

                        @if (isset($post->exif_data['shutter_speed']))
                            <div class="text-gray-500 dark:text-gray-400">Vitesse</div>
                            <div class="dark:text-white font-medium">{{ $post->exif_data['shutter_speed'] }}s</div>
                        @endif

                        @if (isset($post->exif_data['aperture']))
                            <div class="text-gray-500 dark:text-gray-400">Ouverture</div>
                            <div class="dark:text-white font-medium">f/{{ $post->exif_data['aperture'] }}</div>
                        @endif

                    </div>
                </details>
            @endif

            <p class="mt-4 dark:text-gray-300">{{ $post->content }}</p>
        </div>

        <footer class="pt-4 border-t border-gray-200 dark:border-gray-700">

            {{-- Affichage de la note moyenne --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="flex gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <span
                            class="text-2xl {{ $i <= $post->averageRating() ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">
                            ★
                        </span>
                    @endfor
                </div>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $post->averageRating() }}/5
                    ({{ $post->ratings()->count() }}
                    {{ $post->ratings()->count() === 1 ? 'vote' : 'votes' }})
                </span>
            </div>

            @auth
                @can('create', [App\Models\Rating::class, $post])
                    {{-- Formulaire pour soumettre ou changer sa note --}}
                    <form method="POST" action="{{ url('/ratings/' . $post->id) }}" class="mb-4">
                        @csrf
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $userRating ? 'Votre note : ' . $userRating->stars . '/5 — Modifier :' : 'Noter cette photo :' }}
                        </p>
                        <div class="flex gap-2 items-center">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="submit" name="stars" value="{{ $i }}"
                                    class="text-3xl cursor-pointer hover:scale-110 transition-transform
                            {{ $userRating && $userRating->stars >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">
                                    ★
                                </button>
                            @endfor
                        </div>
                    </form>

                    {{-- Bouton pour supprimer sa note si elle existe --}}
                    @if ($userRating)
                        <form method="POST" action="{{ url('/ratings/' . $post->id) }}" class="mb-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline cursor-pointer">
                                Supprimer ma note
                            </button>
                        </form>
                    @endif
                @else
                    {{-- Message si c'est son propre post --}}
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 italic">
                        Vous ne pouvez pas noter votre propre photo.
                    </p>
                @endcan
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <a href="{{ url('/auth/login') }}" class="underline">Connectez-vous</a>
                    pour noter cette photo.
                </p>
            @endauth

        </footer>
    </article>
</x-default-layout>
