<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Vos identifiants</title>
</head>
<body>
    <p>Bonjour {{ $client->nom }},</p>
    <p>Votre compte a été créé. Voici vos identifiants :</p>
    <ul>
        <li>Email : {{ $client->email }}</li>
        <li>Mot de passe : {{ $password }}</li>
    </ul>
    <p>Vous devrez entrer le code de sécurité à la première connexion : {{ $client->security_code ?? '—' }}</p>
    <p>Cordialement,<br/>L'équipe</p>
</body>
</html>
