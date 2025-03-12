{{-- resources/views/download.blade.php --}}
@php use Illuminate\Support\Facades\Storage; @endphp
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Téléchargement des fichiers</title>
    <style>
        /* Ton CSS ici... */
    </style>
</head>
<body>
<div class="container">
    <h1>📁 Téléchargement en cours...</h1>
    <p>Si rien ne se passe, cliquez sur les liens ci-dessous :</p>

    <div class="loader"></div> <!-- Indicateur de chargement -->

    <ul>
        @foreach($files as $file)
            @php
                // Ex: storage_url = storage/8539/wav/funky.wav
                $filePath = asset("storage/{$file->project_id}/{$file->extension}/{$file->name}");
            @endphp
            <li>
                <a href="{{ $filePath }}" download>
                    <span class="file-icon">📃</span> {{ $file->name }}
                </a>
            </li>
        @endforeach
    </ul>

    <button class="close-button" onclick="closePage()">❌ Fermer la page</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.querySelector('.loader');
        const files = @json($files);

        if (files.length > 0) {
            loader.style.display = 'block';

            files.forEach((file, index) => {
                setTimeout(() => {
                    const cleanName = file.name.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    const link = document.createElement('a');
                    link.href = encodeURI("{{ asset('storage') }}/" + file.project_id + "/" + file.extension + "/" + encodeURIComponent(file.name));
                    link.download = cleanName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Cacher le loader une fois le dernier fichier téléchargé
                    if (index === files.length - 1) {
                        loader.style.display = 'none';
                    }
                }, index * 1000); // 1 seconde entre chaque téléchargement
            });
        } else {
            alert("Aucun fichier disponible pour le téléchargement.");
        }
    });

    function closePage() {
        if (window.opener) {
            window.close();
        } else {
            alert("Vous pouvez fermer cette page manuellement.");
        }
    }
</script>
</body>
</html>
