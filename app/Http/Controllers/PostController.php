<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage; //import de storage

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->with('user')->with('ratings')->get();

        return view('posts.index', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Code généré par IA
     * Méthode privée : lit les EXIF du fichier et retourne un tableau propre.
     * "Privée" = elle n'est utilisable que dans ce controller, pas depuis l'extérieur.
     */
    private function extractExifData($file): array
    {
        $exif = [];

        try {
            // Dimensions — on les lit directement depuis getimagesize() car
            // EXIF ne donne pas toujours les dimensions correctes
            // getimagesize() est toujours disponible, sans extension particulière
            $size = getimagesize($file->getPathname());
            if ($size) {
                $exif['width']  = $size[0]; // largeur en pixels
                $exif['height'] = $size[1]; // hauteur en pixels
            }

            // Taille du fichier en octets, convertie en Mo
            // getSize() est toujours disponible, sans extension particulière
            $exif['file_size'] = round($file->getSize() / 1048576, 2);

            // function_exists() vérifie que l'extension EXIF est activée dans PHP
            // avant d'appeler exif_read_data(), sinon on obtient une erreur fatale
            if (function_exists('exif_read_data')) {
                // exif_read_data() lit les métadonnées du fichier
                // Elle peut échouer (image sans EXIF, format non supporté)
                $raw = @exif_read_data($file->getPathname());

                if ($raw) {
                    // Marque et modèle de l'appareil
                    // isset() vérifie que la clé existe avant d'y accéder
                    if (isset($raw['Make']))  $exif['camera_make']  = $raw['Make'];
                    if (isset($raw['Model'])) $exif['camera_model'] = trim($raw['Model']);

                    // Focale — stockée comme fraction "35/1", on la convertit en nombre
                    if (isset($raw['FocalLength'])) {
                        $exif['focal_length'] = $this->convertFraction($raw['FocalLength']);
                    }

                    // ISO
                    if (isset($raw['ISOSpeedRatings'])) {
                        $exif['iso'] = $raw['ISOSpeedRatings'];
                    }

                    // Vitesse d'obturation — stockée comme "1/150"
                    if (isset($raw['ExposureTime'])) {
                        $exif['shutter_speed'] = $raw['ExposureTime'];
                    }

                    // Ouverture — stockée comme fraction, on la convertit
                    if (isset($raw['FNumber'])) {
                        $exif['aperture'] = $this->convertFraction($raw['FNumber']);
                    }

                    // Objectif
                    if (isset($raw['UndefinedTag:0xA434'])) {
                        $exif['lens'] = $raw['UndefinedTag:0xA434'];
                    } elseif (isset($raw['LensModel'])) {
                        $exif['lens'] = $raw['LensModel'];
                    }
                }
            }
            // Si l'extension EXIF n'est pas activée : les dimensions et la taille
            // sont quand même récupérées, l'utilisateur remplit le reste manuellement

        } catch (\Throwable $e) {
            // \Throwable capture à la fois les Exception ET les Error PHP
            // (contrairement à \Exception qui ne capture pas les erreurs fatales
            // comme "fonction introuvable")
            // Si la lecture échoue, on retourne ce qu'on a déjà (dimensions/taille)
            // L'utilisateur pourra compléter manuellement
        }

        return $exif;
    }

    /**
     * Convertit une fraction "35/1" ou "28/5" en nombre décimal.
     * Les valeurs EXIF comme la focale et l'ouverture sont stockées comme fractions.
     */
    private function convertFraction(string $fraction): float|string
    {
        if (str_contains($fraction, '/')) {
            [$num, $den] = explode('/', $fraction);
            return $den != 0 ? round($num / $den, 1) : $fraction;
        }
        return $fraction;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
            'image'   => 'required|image|max:8192', // obligatoire, fichier image, 8 Mo maximum

            // Champs EXIF manuels — tous optionnels
            'exif_width'        => 'nullable|string|max:50',
            'exif_height'       => 'nullable|string|max:50',
            'exif_file_size'    => 'nullable|string|max:50',
            'exif_camera_make'  => 'nullable|string|max:100',
            'exif_camera_model' => 'nullable|string|max:100',
            'exif_lens'         => 'nullable|string|max:100',
            'exif_focal_length' => 'nullable|string|max:50',
            'exif_iso'          => 'nullable|string|max:50',
            'exif_shutter_speed' => 'nullable|string|max:50',
            'exif_aperture'     => 'nullable|string|max:50',
        ]);

        // récupère le fichier uploadé
        $file = $request->file('image');

        // enregistre le fichier dans storage/app/public/post-images/ et retourne son chemin relatif
        $path = Storage::disk('public')->put('post-images', $file);

        // 1. On lit les EXIF automatiquement depuis le fichier
        $exif = $this->extractExifData($file);

        // 2. Les valeurs saisies manuellement écrasent les valeurs auto
        //    array_filter() supprime les valeurs nulles/vides
        $manual = array_filter([
            'width'        => $validated['exif_width']         ?? null,
            'height'       => $validated['exif_height']        ?? null,
            'file_size'    => $validated['exif_file_size']     ?? null,
            'camera_make'  => $validated['exif_camera_make']   ?? null,
            'camera_model' => $validated['exif_camera_model']  ?? null,
            'lens'         => $validated['exif_lens']          ?? null,
            'focal_length' => $validated['exif_focal_length']  ?? null,
            'iso'          => $validated['exif_iso']           ?? null,
            'shutter_speed' => $validated['exif_shutter_speed'] ?? null,
            'aperture'     => $validated['exif_aperture']      ?? null,
        ]);

        // array_merge : les clés de $manual écrasent celles de $exif
        $exif = array_merge($exif, $manual);

        $user = $request->user();
        $post = new Post();

        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->image   = $path; // stocke le chemin vers le fichier
        $post->exif_data = !empty($exif) ? $exif : null;

        $post->user()->associate($user);

        $post->save();

        return redirect("/posts/$post->id");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::with('user')->with('ratings')->findOrFail($id);

        $user = Auth::user();
        $userRating = null;

        if ($user) {
            // Récupère la note de l'utilisateur connecté pour ce post
            $userRating = $post->ratings()->where('user_id', $user->id)->first();
        }

        return view('posts.show', [
            'post'       => $post,
            'userRating' => $userRating,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::findOrFail($id);

        Gate::authorize('update', $post);

        return view('posts.edit', ['post' => $post]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
            'image'   => 'nullable|image|max:8192', // nullable = pas obligatoire de changer la photo si on modifie
            'exif_width'        => 'nullable|string|max:50',
            'exif_height'       => 'nullable|string|max:50',
            'exif_file_size'    => 'nullable|string|max:50',
            'exif_camera_make'  => 'nullable|string|max:100',
            'exif_camera_model' => 'nullable|string|max:100',
            'exif_lens'         => 'nullable|string|max:100',
            'exif_focal_length' => 'nullable|string|max:50',
            'exif_iso'          => 'nullable|string|max:50',
            'exif_shutter_speed' => 'nullable|string|max:50',
            'exif_aperture'     => 'nullable|string|max:50',
        ]);

        $post = Post::findOrFail($id);

        Gate::authorize('update', $post);

        $file = $request->file('image');
        $exif = $post->exif_data ?? []; // On repart des EXIF existantes

        if ($file) {
            // Supprime l'ancienne image du disque
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $post->image = Storage::disk('public')->put('post-images', $file);

            // Nouvelle image = on repart de zéro, on ne conserve PAS les anciennes EXIF
            $exif = $this->extractExifData($file);
        } else {
            // Pas de nouvelle image = on repart des EXIF existantes
            $exif = $post->exif_data ?? [];
        }

        // Sur l'édition on prend TOUTES les valeurs du formulaire,
        // y compris les vides — pour pouvoir effacer une ancienne valeur
        $manual = [
            'width'        => $validated['exif_width']        ?? null,
            'height'       => $validated['exif_height']       ?? null,
            'file_size'    => $validated['exif_file_size']    ?? null,
            'camera_make'  => $validated['exif_camera_make']  ?? null,
            'camera_model' => $validated['exif_camera_model'] ?? null,
            'lens'         => $validated['exif_lens']         ?? null,
            'focal_length' => $validated['exif_focal_length'] ?? null,
            'iso'          => $validated['exif_iso']          ?? null,
            'shutter_speed' => $validated['exif_shutter_speed'] ?? null,
            'aperture'     => $validated['exif_aperture']     ?? null,
        ];

        // array_merge : les valeurs de $manual écrasent celles de $exif
        // array_filter à la fin : supprime les clés null du résultat final
        $exif = array_filter(array_merge($exif, $manual));

        $post->title    = $validated['title'];
        $post->content  = $validated['content'];
        $post->exif_data = !empty($exif) ? $exif : null;

        $post->save();

        return redirect("/posts/$post->id");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);

        Gate::authorize('delete', $post);

        // Supprime le fichier image avant de supprimer le post
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect("/posts");
    }
}
