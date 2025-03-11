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
        .new-file {
            color: #0c9155;
            font-weight: bold;
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
        <img src="https://le-de.cdn-website.com/b7431e42b28841b09fa117548a4c9df2/dms3rep/multi/opt/fc3a2624c1b2473b81b16556bfff7f37-139h.jpg" alt="Becip Logo">
        <h1>Rapport Quotidien des Plans</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $user->name }},</p>

        @if($ownProjects->isEmpty() && $otherProjects->isEmpty())
            <p style="text-align: center; font-size: 18px; font-weight: bold; color: #0c9155;">
                ✅ Tout va bien, aucun plan non validé. Reposez-vous bien ! 😊
            </p>
        @else
            @if($ownProjects->isNotEmpty())
                <h3>📌 Plans où vous êtes l'ingénieur référent :</h3>
                @php $hasOwnFiles = false; @endphp
                @foreach($ownProjects as $project)
                    @if($project->files->isNotEmpty())
                        @php $hasOwnFiles = true; @endphp
                        <p class="project-title">
                            📁 <a href="{{ $project->passwordless_url }}">{{ $project->name }} - voir</a>
                        </p>
                        <ul>
                            @foreach($project->files as $file)
                                @php
                                    $shortName = strlen($file->name) > 30 ? substr($file->name, 0, 28) . '...' : $file->name;
                                @endphp
                                <li class="file-item">
                                    @if($file->uploaded_recently)
                                        <span class="new-file">Nouveau,</span>
                                    @endif
                                    <a href="#">{{ $shortName }}</a>
                                    @if(isset($file->uploader_name))
                                        <span class="file-info" style="color: #808080; font-size: 12px;">| {{ $file->uploader_name }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach
                @if(!$hasOwnFiles)
                    <p>Aucun plan non validé dans vos projets.</p>
                @endif
            @endif

            @if($otherProjects->isNotEmpty())
                <h3>📌 Plans non validés des autres affaires :</h3>
                @foreach($otherProjects as $project)
                    @if($project->files->isNotEmpty())
                        <p class="project-title">
                            📁 <a href="{{ $project->passwordless_url }}">{{ $project->name }} - voir</a>
                        </p>
                        <ul>
                            @foreach($project->files as $file)
                                @php
                                    $shortName = strlen($file->name) > 30 ? substr($file->name, 0, 28) . '...' : $file->name;
                                @endphp
                                <li class="file-item">
                                    @if($file->uploaded_recently)
                                        <span class="new-file">Nouveau,</span>
                                    @endif
                                    <a href="#">{{ $shortName }}</a>
                                    @if(isset($file->uploader_name))
                                        <span class="file-info" style="color: #808080; font-size: 12px;">| {{ $file->uploader_name }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach
            @endif
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
