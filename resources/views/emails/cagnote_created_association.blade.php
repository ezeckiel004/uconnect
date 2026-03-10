<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cagnote Créée</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); overflow: hidden; }
        .header { background: linear-gradient(135deg, #12546D 0%, #007B80 100%); color: white; padding: 50px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 32px; font-weight: 700; }
        .content { padding: 40px 30px; }
        .details-box { background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%); border: 1px solid #e0e7ff; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0; }
        .detail-label { font-size: 12px; font-weight: 700; color: #12546D; text-transform: uppercase; }
        .detail-value { font-size: 15px; color: #333; margin-top: 4px; }
        .info-box { background-color: #f0f8ff; border-left: 4px solid #007B80; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .text-muted { color: #999; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 35px; background-color: #007B80; color: white; text-decoration: none; border-radius: 6px; font-weight: 700; margin-top: 20px; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Cagnote Créée</h1>
            <p>En Attente de Validation</p>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $association->name }},</p>
            
            <p>Votre nouvelle cagnote a bien été créée et est maintenant en attente de validation par notre équipe d'administration.</p>
            
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">📝 Titre</div>
                    <div class="detail-value">{{ $cagnote->title }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">📝 Description</div>
                    <div class="detail-value">{{ $cagnote->description }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">💰 Objectif</div>
                    <div class="detail-value">{{ $cagnote->goal_amount }} EUR</div>
                </div>
            </div>
            
            <div class="info-box">
                <strong>⏳ Délai de Validation</strong>
                <p>Votre cagnote sera examinée par notre équipe sous 24 à 48 heures. Vous recevrez une notification dès qu'elle sera validée ou si nous avons besoin de modifications.</p>
            </div>
            
            <p class="text-muted">En attente de validation, votre cagnote n'est pas encore visible publiquement.</p>
            
            <a href="https://uconnect.vibecro.com" class="btn">Consulter Votre Compte</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
