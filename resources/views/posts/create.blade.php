<x-default-layout>
    <x-slot:title>
        {{ __('ui.posts.create.title') }}
    </x-slot>

    <x-slot:description>
        {{ __('ui.posts.create.description', ['app_name' => config('app.name')]) }}
    </x-slot>

    <article class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
        <header class="mb-6">
            <h1 class="text-3xl font-bold dark:text-white mb-2">
                {{ __('ui.posts.create.title') }}
            </h1>

            <p class="mt-4 dark:text-gray-300">
                {{ __('ui.posts.create.description', ['app_name' => config('app.name')]) }}
            </p>
        </header>
        <!-- ajout de enctype pour permettre -->
        <form method="POST" action="{{ url('/posts') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('ui.posts.form.fields.title.label') }}
                </label>
                <input id="title" type="text" name="title" value="{{ old('title') }}"
                    placeholder="{{ __('ui.posts.form.fields.title.placeholder') }}"
                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent @error('title') border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('ui.posts.form.fields.content.label') }}
                </label>
                <textarea id="content" name="content" rows="5"
                    placeholder="{{ __('ui.posts.form.fields.content.placeholder') }}"
                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent @error('content') border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500 @enderror">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>


            <!-- Nouveau champ pour afficher la photo actuelle -->
            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo
                    *</label>
                <input id="image" type="file" name="image" accept="image/*"
                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                @error('image') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                @error('image')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section EXIF : détails techniques de la photo --}}
            <div class="mb-6">
                <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <span>▶</span> Détails techniques (optionnel)
                </button>

                {{-- Ce div est caché par défaut grâce à "hidden", le bouton le révèle --}}
                <div id="exif-panel"
                    class="hidden mt-4 grid grid-cols-2 gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-md">

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Largeur (px)</label>
                        <input type="text" name="exif_width" id="exif_width" value="{{ old('exif_width') }}"
                            placeholder="ex: 6000"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hauteur (px)</label>
                        <input type="text" name="exif_height" id="exif_height" value="{{ old('exif_height') }}"
                            placeholder="ex: 4000"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Taille (Mo)</label>
                        <input type="text" name="exif_file_size" id="exif_file_size"
                            value="{{ old('exif_file_size') }}" placeholder="ex: 6"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Marque appareil</label>
                        <input type="text" name="exif_camera_make" id="exif_camera_make"
                            value="{{ old('exif_camera_make') }}" placeholder="ex: Sony"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Modèle appareil</label>
                        <input type="text" name="exif_camera_model" id="exif_camera_model"
                            value="{{ old('exif_camera_model') }}" placeholder="ex: Alpha 7 III"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Objectif</label>
                        <input type="text" name="exif_lens" id="exif_lens" value="{{ old('exif_lens') }}"
                            placeholder="ex: Sony FE 35mm f/1.8"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Focale (mm)</label>
                        <input type="text" name="exif_focal_length" id="exif_focal_length"
                            value="{{ old('exif_focal_length') }}" placeholder="ex: 35"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">ISO</label>
                        <input type="text" name="exif_iso" id="exif_iso" value="{{ old('exif_iso') }}"
                            placeholder="ex: 400"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Vitesse</label>
                        <input type="text" name="exif_shutter_speed" id="exif_shutter_speed"
                            value="{{ old('exif_shutter_speed') }}" placeholder="ex: 1/150"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Ouverture</label>
                        <input type="text" name="exif_aperture" id="exif_aperture"
                            value="{{ old('exif_aperture') }}" placeholder="ex: f6.0"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    </div>

                </div>
            </div>

            <footer class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <a href="{{ url('/posts') }}"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                        {{ __('ui.posts.form.actions.cancel') }}
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-teal-600 dark:bg-purple-900 text-white rounded-md hover:bg-teal-700 dark:hover:bg-purple-800 cursor-pointer">
                        {{ __('ui.posts.form.actions.submit') }}
                    </button>
                </div>
            </footer>
        </form>
    </article>
</x-default-layout>

<script type="module">
    // Import de la librairie exifr depuis un CDN
    // "module" permet d'utiliser import/export ES6
    import exifr from 'https://cdn.jsdelivr.net/npm/exifr/dist/full.esm.js'

    document.getElementById('image').addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Ouvre automatiquement le panneau des détails techniques
        document.getElementById('exif-panel').classList.remove('hidden');

        // Lit les dimensions et la taille — toujours disponibles sans EXIF
        const img = new Image();
        img.onload = function() {
            document.getElementById('exif_width').value = img.naturalWidth;
            document.getElementById('exif_height').value = img.naturalHeight;
            URL.revokeObjectURL(img.src); // libère la mémoire
        };
        img.src = URL.createObjectURL(file);

        // Taille en Mo, arrondie à 2 décimales
        document.getElementById('exif_file_size').value = (file.size / 1048576).toFixed(2);

        try {
            // exifr.parse() lit toutes les métadonnées EXIF du fichier
            const exif = await exifr.parse(file, {
                pick: ['Make', 'Model', 'LensModel', 'FocalLength', 'ISO', 'ExposureTime',
                    'FNumber'
                ]
            });

            if (!exif) return; // Pas d'EXIF dans ce fichier

            if (exif.Make) document.getElementById('exif_camera_make').value = exif.Make;
            if (exif.Model) document.getElementById('exif_camera_model').value = exif.Model;
            if (exif.LensModel) document.getElementById('exif_lens').value = exif.LensModel;
            if (exif.FocalLength) document.getElementById('exif_focal_length').value = exif.FocalLength;
            if (exif.ISO) document.getElementById('exif_iso').value = exif.ISO;

            // ExposureTime est un nombre décimal (ex: 0.00667), on le convertit en fraction lisible
            if (exif.ExposureTime) {
                const t = exif.ExposureTime;
                // Si moins d'une seconde, on affiche "1/X", sinon la valeur directe
                document.getElementById('exif_shutter_speed').value = t < 1 ?
                    '1/' + Math.round(1 / t) :
                    t + 's';
            }

            // FNumber est déjà un nombre décimal (ex: 6.0)
            if (exif.FNumber) document.getElementById('exif_aperture').value = exif.FNumber;

        } catch (err) {
            // Si la lecture EXIF échoue (image sans EXIF, format non supporté)
            // les champs restent vides, l'utilisateur remplit manuellement
            console.log('Pas de données EXIF disponibles');
        }
    });
</script>
