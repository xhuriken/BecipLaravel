<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        p.small{
            color: #6c757d;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="https://le-de.cdn-website.com/b7431e42b28841b09fa117548a4c9df2/dms3rep/multi/opt/fc3a2624c1b2473b81b16556bfff7f37-139h.jpg" alt="Becip Logo">
        <h1>Bienvenue chez Becip</h1>
    </div>

    <div class="content">
        <h2>Bonjour {{$userName}},</h2>
        <p>
            Votre adresse email a été modifiée. Voici un lien pour définir votre mot de passe si besoin :
        </p>
        <div class="cta-button">
            <a href="{{$urlPassword}}">Modifier le mot de passe</a>
        </div>
        <p>
            Pour accéder à votre espace personnel et commencer à utiliser nos services, cliquez sur le lien ci-dessous :
        </p>
        <div class="cta-button">
            <a href="{{$urlHome}}">Accéder à mon compte</a>
        </div>
{{--        <p class="small">Ce lien est valable 2 jours après l'envoi de ce mail.</p>--}}
        <p>
            Si vous avez des questions ou besoin d'assistance, n'hésitez pas à nous contacter.
            Nous sommes ravis de vous compter parmi nos utilisateurs.
        </p>
    </div>

    <div class="footer">
        Cet email vous a été envoyé par Becip.<br>
        &copy; {{date('Y')}} Becip. Tous droits réservés.
    </div>
</div>
</body>
</html>

