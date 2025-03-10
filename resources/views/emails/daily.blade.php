<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Quotidien des Plans</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333333;
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
            max-width: 150px;
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
        .content p {
            margin: 10px 0;
        }
        .cta-button {
            display: block;
            text-align: center;
            margin: 20px 0;
        }
        .cta-button a {
            background-color: #0c9155;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
        }
        .cta-button a:hover {
            background-color: #0a7a48;
        }
        .footer {
            background-color: #f5f5f5;
            color: #666666;
            text-align: center;
            font-size: 12px;
            padding: 10px;
            border-top: 1px solid #dddddd;
        }
        .file-list {
            margin-top: 20px;
        }
        .file-item {
            margin: 10px 0;
        }
        .file-item a {
            color: #0c9155;
            text-decoration: none;
        }
        .new-file {
            color: #0c9155;
            font-weight: bold;
        }
        .old-file {
            color: #333333;
        }
        .file-info {
            font-size: 14px;
            color: #999999;
        }
        ul {
            margin: 0;
        }
        .name {
            font-size: 14px;
            color: #808080;
        }
        .filename {
            font-weight: bold;
        }
        a {
            color: #333333;
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- HEADER -->
    <div class="header">
        <img src="https://le-de.cdn-website.com/b7431e42b28841b09fa117548a4c9df2/dms3rep/multi/opt/fc3a2624c1b2473b81b16556bfff7f37-139h.jpg" alt="Becip Logo">
        <h1>Rapport Quotidien des Plans</h1>
    </div>
    <div class="content">
        <p>Bonjour {{ $user->name }},</p>

        @if($ownProjects->isNotEmpty())
            <h3>Vos projets :</h3>
            <ul>
                @foreach($ownProjects as $project)
                    <li>
                        <a href="{{ $project->passwordless_url }}" class="project-link">{{ $project->name }}</a>
                        <ul>
                            @foreach($project->files as $file)
                                <li>
                                    {{ $file->name }}
                                    @if($file->uploaded_recently)
                                        <span class="new-file">Nouveau</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        @else
            <p>Vous n'êtes référent d'aucun projet.</p>
        @endif

        @if($otherProjects->isNotEmpty())
            <h3>Projets d'autres ingénieurs :</h3>
            <ul>
                @foreach($otherProjects as $project)
                    <li class="file-item">
                        <a href="{{ $project->passwordless_url }}">{{ $project->name }}</a>
                        <ul>
                            @foreach($project->files as $file)
                                <li>
                                    {{ $file->name }}
                                    @if($file->uploaded_recently)
                                        <span class="new-file">Nouveau</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <!-- FOOTER -->
    <div class="footer">
        Cet email vous a été envoyé par Becip.<br>
        &copy; {{ date('Y') }} Becip. Tous droits réservés.
    </div>
</div>
</body>
</html>
