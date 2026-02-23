<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { background-color: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; }
        .button { background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nouvelle Cagnote à Valider</h2>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Une nouvelle cagnote a été créée et attend votre validation.</p>
            
            <h3>Détails de la cagnote:</h3>
            <ul>
                <li><strong>Titre:</strong> {{ $cagnote->title }}</li>
                <li><strong>Association:</strong> {{ $association->name }}</li>
                <li><strong>Catégorie:</strong> {{ $cagnote->category }}</li>
                <li><strong>Objectif:</strong> {{ $cagnote->objective_amount }}€</li>
                <li><strong>Localisation:</strong> {{ $cagnote->location }}</li>
                <li><strong>Description:</strong> {{ $cagnote->description }}</li>
            </ul>
            
            <p>Veuillez analyser cette cagnote et la valider ou la rejeter.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('admin.cagnotes.review', $cagnote->id) }}" class="button">Consulter la cagnote</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2026 U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
