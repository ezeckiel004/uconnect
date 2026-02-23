<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { background-color: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; }
        .button { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .success-box { background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cagnote Validée - Maintenant en Ligne! 🎉</h2>
        </div>
        <div class="content">
            <p>Bonjour {{ $association->name }},</p>
            
            <div class="success-box">
                <p><strong>Bonnes nouvelles!</strong> Votre cagnote a été <strong>validée avec succès</strong> par notre équipe et est maintenant <strong>en ligne</strong>!</p>
            </div>
            
            <h3>Votre cagnote est maintenant visible:</h3>
            <ul>
                <li><strong>Titre:</strong> {{ $cagnote->title }}</li>
                <li><strong>Statut:</strong> Approuvée</li>
                <li><strong>Catégorie:</strong> {{ $cagnote->category }}</li>
                <li><strong>Objectif:</strong> {{ $cagnote->objective_amount }}€</li>
                <li><strong>Lieu:</strong> {{ $cagnote->location }}</li>
            </ul>
            
            <p>Les utilisateurs de U-Connect peuvent maintenant découvrir votre cagnote et y contribuer.</p>
            
            <p style="text-align: center;">
                <a href="{{ config('app.url') }}/cagnotes/{{ $cagnote->id }}" class="button">Voir votre cagnote</a>
            </p>
            
            <p>Merci d'utiliser U-Connect pour soutenir votre cause!</p>
            <p>Cordialement,<br><strong>L'équipe U-Connect</strong></p>
        </div>
        <div class="footer">
            <p>&copy; 2026 U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
