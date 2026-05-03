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
                    {{ trans_choice('ui.posts.likes_count', count($post->likes)) }}
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
            @auth
                <form method="POST" action="{{ url('/likes/' . $post->id) }}" class="mb-4">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-wrap justify-between gap-2">
                        <button type="submit" name="reaction" value="like"
                            class="w-12 h-12 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer {{ $reaction === 'like' ? 'ring-2 ring-teal-600 dark:ring-purple-900' : '' }}">
                            👍
                        </button>
                        <button type="submit" name="reaction" value="love"
                            class="w-12 h-12 rounded-full cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 {{ $reaction === 'love' ? 'ring-2 ring-teal-600 dark:ring-purple-900' : '' }}">
                            ❤️
                        </button>
                        <button type="submit" name="reaction" value="haha"
                            class="w-12 h-12 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer {{ $reaction === 'haha' ? 'ring-2 ring-teal-600 dark:ring-purple-900' : '' }}">
                            😂
                        </button>
                        <button type="submit" name="reaction" value="wow"
                            class="w-12 h-12 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer {{ $reaction === 'wow' ? 'ring-2 ring-teal-600 dark:ring-purple-900' : '' }}">
                            😮
                        </button>
                        <button type="submit" name="reaction" value="sad"
                            class="w-12 h-12 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer {{ $reaction === 'sad' ? 'ring-2 ring-teal-600 dark:ring-purple-900' : '' }}">
                            😢
                        </button>
                        <button type="submit" name="reaction" value="angry"
                            class="w-12 h-12 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer {{ $reaction === 'angry' ? 'ring-2 ring-teal-600 dark:ring-purple-900' : '' }}">
                            😡
                        </button>
                    </div>
                </form>
            @endauth
            <ul class="flex flex-wrap gap-2">
                @forelse ($post->likes as $user)
                    <li class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                        <a href="{{ url('@' . $user->username) }}" class="font-semibold hover:underline">
                            {{ '@' . $user->username }}
                        </a>
                        <span>
                            @if ($user->pivot->reaction === 'like')
                                👍
                            @elseif($user->pivot->reaction === 'love')
                                ❤️
                            @elseif($user->pivot->reaction === 'haha')
                                😂
                            @elseif($user->pivot->reaction === 'wow')
                                😮
                            @elseif($user->pivot->reaction === 'sad')
                                😢
                            @elseif($user->pivot->reaction === 'angry')
                                😡
                            @endif
                        </span>
                    </li>
                @empty
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ trans_choice('ui.posts.likes_count', 0) }}
                    </span>
                @endforelse
            </ul>
        </footer>
    </article>
</x-default-layout>
