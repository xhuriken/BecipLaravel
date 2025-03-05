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
            text-align: center;
            margin: 20px 0;
        }
        .cta-button a {
            background-color: #0c9155;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            display: inline-block;
            font-weight: bold;
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
    </style>
</head>
<body>
<div class="email-container">
    <!-- Header -->
    <div class="header">
        <img src="https://le-de.cdn-website.com/b7431e42b28841b09fa117548a4c9df2/dms3rep/multi/opt/fc3a2624c1b2473b81b16556bfff7f37-139h.jpg" alt="Becip Logo">
        <h1>Réinitialisation de votre mot de passe</h1>
    </div>

    <div class="content">
        <h2>Bonjour {{$user->name}},</h2>
        <p>
            Nous avons reçu une demande de réinitialisation de votre mot de passe.
            Si vous êtes à l'origine de cette demande, cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
        </p>
        <div class="cta-button">
            <a href="{{$url}}">Réinitialiser mon mot de passe</a>
        </div>
        <p>
            Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet e-mail en toute sécurité.
            Votre mot de passe actuel restera inchangé.
        </p>
    </div>

    <div class="footer">
        Cet email vous a été envoyé par Becip.<br>
        &copy; ' . date('Y') . ' Becip. Tous droits réservés.
    </div>
</div>
</body>
</html>
