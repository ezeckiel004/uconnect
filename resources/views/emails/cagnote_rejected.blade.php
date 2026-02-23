<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { background-color: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; }
        .button { background-color: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .reason-box { background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cagnote Refusée - Modifications Demandées</h2>
        </div>
        <div class="content">
            <p>Bonjour {{ $association->name }},</p>
            <p>Après analyse de votre cagnote, nous ne pouvons pas la valider pour le moment.</p>
            
            <div class="reason-box">
                <h4>Raison du rejet:</h4>
                <p>{{ $reason }}</p>
            </div>
            
            <h3>Votre cagnote:</h3>
            <ul>
                <li><strong>Titre:</strong> {{ $cagnote->title }}</li>
                <li><strong>Catégorie:</strong> {{ $cagnote->category }}</li>
                <li><strong>Objectif:</strong> {{ $cagnote->objective_amount }}€</li>
            </ul>
            
            <h3>Que faire maintenant?</h3>
            <p>Vous pouvez modifier votre cagnote en fonction du feedback ci-dessus et la soumettre à nouveau. Notre équipe réexaminera votre demande dès réception de la version mise à jour.</p>
            
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            
            <p>Cordialement,<br><strong>L'équipe U-Connect</strong></p>
        </div>
        <div class="footer">
            <p>&copy; 2026 U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
