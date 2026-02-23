<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { background-color: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .highlight { background-color: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cagnote Reçue - Analyse en Cours</h2>
        </div>
        <div class="content">
            <p>Bonjour {{ $association->name }},</p>
            <p>Merci d'avoir créé une cagnote sur U-Connect!</p>
            <p>Nous avons bien reçu votre cagnote <strong>{{ $cagnote->title }}</strong> et elle est maintenant en cours d'analyse de conformité.</p>
            
            <h3>Que se passe-t-il maintenant?</h3>
            <p>Notre équipe d'administrateurs va examiner votre cagnote dans les prochaines <strong>24 heures</strong> pour vérifier sa conformité avec nos critères. Vous recevrez un email de confirmation dès qu'elle sera validée ou si des modifications sont nécessaires.</p>
            
            <h3>Détails de votre cagnote:</h3>
            <ul>
                <li><strong>Titre:</strong> {{ $cagnote->title }}</li>
                <li><strong>Catégorie:</strong> {{ $cagnote->category }}</li>
                <li><strong>Objectif:</strong> {{ $cagnote->objective_amount }}€</li>
                <li><strong>Lieu:</strong> {{ $cagnote->location }}</li>
            </ul>
            
            <div class="highlight">
                <p><strong>💡 Astuce:</strong> Vous pouvez éditer votre cagnote à tout moment jusqu'à son approbation.</p>
            </div>
            
            <p>Nous vous remercions de votre patience!</p>
            <p>Cordialement,<br><strong>L'équipe U-Connect</strong></p>
        </div>
        <div class="footer">
            <p>&copy; 2026 U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
