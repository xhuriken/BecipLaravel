<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveaux Plans Validés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            font-size: 17px;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0c9155;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 100px;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 10px 0 0;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
        }
        .content h2 {
            color: #0c9155;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .project-title {
            font-size: 18px;
            font-weight: bold;
            color: #0c9155;
            margin-top: 15px;
        }
        .project-title a {
            text-decoration: none;
            color: #0c9155;
        }
        .file-item {
            margin: 5px 0;
        }
        .file-item a {
            color: #333;
            text-decoration: none;
            font-weight: normal;
        }
        .file-info {
            font-size: 14px;
            color: #808080;
            margin-left: 5px;
        }
        .footer {
            background-color: #f5f5f5;
            color: #666666;
            text-align: center;
            font-size: 12px;
            padding: 10px;
            border-top: 1px solid #dddddd;
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- HEADER -->
    <div class="header">
        <img src="https://your-logo-url.png" alt="Becip Logo">
        <h1>🚀 Nouveaux Plans Validés</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $client->name }},</p>
        <p>Voici les plans validés au cours des dernières 24 heures :</p>

        @foreach($projects as $project)
            @if($project->files->isNotEmpty())
                <p class="project-title">
                    📂 <a href="{{ $project->passwordless_url }}">{{ $project->name }} - Voir</a>
                </p>
                <ul>
                    @foreach($project->files as $file)
                        <li class="file-item">
                            📝 {{ $file->name }}
                            @if($file->uploadedBy)
                                <span class="file-info">{{ $file->uploadedBy->name }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Cet email vous a été envoyé par Becip.<br>
        &copy; {{ date('Y') }} Becip. Tous droits réservés.
    </div>
</div>
</body>
</html>
