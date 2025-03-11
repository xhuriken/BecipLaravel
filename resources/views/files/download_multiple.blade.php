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
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #0c9155;
            font-size: 22px;
        }

        p {
            font-size: 16px;
            margin-bottom: 15px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
        }

        a {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #0c9155;
            font-weight: bold;
            font-size: 16px;
            border: 1px solid #0c9155;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        a:hover {
            background-color: #0c9155;
            color: white;
        }

        .file-icon {
            margin-right: 8px;
            font-size: 18px;
        }

        .loader {
            display: none;
            margin: 10px auto;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0c9155;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

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
                $filePath = Storage::url("{$file->project_id}/{$file->extension}/{$file->name}");
            @endphp
            <li>
                <a href="{{ $filePath }}" download>
                    <span class="file-icon">📂</span> {{ $file->name }}
                </a>
            </li>
        @endforeach
    </ul>
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
                    const a = document.createElement('a');
                    a.href = encodeURI("{{ asset('storage') }}/" + file.project_id + "/" + file.extension + "/" + encodeURIComponent(file.name));
                    a.download = cleanName;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);

                    // Cacher le loader une fois le dernier fichier téléchargé
                    if (index === files.length - 1) {
                        loader.style.display = 'none';
                    }
                }, index * 1000); // Délai entre les téléchargements (1s par fichier)
            });
        } else {
            alert("Aucun fichier disponible pour le téléchargement.");
        }
    });
</script>

</body>
</html>
