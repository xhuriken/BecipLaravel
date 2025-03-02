@php use Illuminate\Support\Facades\Storage; @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Téléchargement des fichiers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
    </style>
</head>
<body>
    <h1>Vos téléchargements vont commencer...</h1>
    <p>Si rien ne se passe, cliquez sur les liens ci-dessous :</p>

    <ul>
        @foreach($files as $file)
            @php
                $filePath = Storage::url("{$file->project_id}/{$file->extension}/{$file->name}");
            @endphp
            <li><a href="{{ $filePath }}" download>{{ $file->name }}</a></li>
        @endforeach
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const files = @json($files);
            if (files.length > 0) {
                files.forEach(file => {
                    const cleanName = file.name.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    //Au cas ou :
                    //.normalize("NFD") décompose les lettres accentuées en lettres normales + accents séparés
                    //.replace(/[\u0300-\u036f]/g, "") supprime ces accents
                    const a = document.createElement('a');
                    a.href = encodeURI("{{ asset('storage') }}/" + file.project_id + "/" + file.extension + "/" + encodeURIComponent(file.name));
                    a.download = cleanName;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                });
            } else {
                alert("Aucun fichier disponible pour le téléchargement.");
            }
        });
    </script>
</body>
</html>
