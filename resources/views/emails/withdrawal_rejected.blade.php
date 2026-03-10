<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrait Rejeté</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); overflow: hidden; }
        .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 50px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 32px; font-weight: 700; }
        .content { padding: 40px 30px; }
        .details-box { background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%); border: 1px solid #e0e7ff; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0; }
        .detail-label { font-size: 12px; font-weight: 700; color: #dc3545; text-transform: uppercase; }
        .detail-value { font-size: 15px; color: #333; margin-top: 4px; }
        .amount-box { background: linear-gradient(135deg, #fcc5c5 0%, #ffe8e8 100%); border: 1px solid #f69a9a; border-radius: 8px; padding: 25px; margin: 20px 0; text-align: center; }
        .amount-label { font-size: 12px; font-weight: 700; color: #dc3545; text-transform: uppercase; }
        .amount-value { font-size: 32px; font-weight: 700; color: #dc3545; margin-top: 8px; }
        .reason-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .reason-box p { margin: 0; color: #856404; }
        .next-steps { background-color: #f0f8ff; border-left: 4px solid #007B80; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .text-muted { color: #999; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 35px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 6px; font-weight: 700; margin-top: 20px; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; margin: 8px 0; }
        li:before { content: "✓ "; color: #dc3545; font-weight: 700; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Retrait Rejeté</h1>
            <p>À Réviser</p>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $user->name }},</p>
            
            <p style="font-size: 16px; color: #dc3545; font-weight: 600; margin: 20px 0;">Votre demande de retrait a été rejetée. ⚠️</p>
            
            <div class="amount-box">
                <div class="amount-label">💰 Montant Demandé</div>
                <div class="amount-value">{{ number_format($withdrawalRequest->withdrawal_amount, 2, ',', ' ') }} €</div>
                <div class="text-muted" style="margin-top: 10px;">Montant Collecté: {{ number_format($withdrawalRequest->original_amount, 2, ',', ' ') }} €</div>
            </div>
            
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">🔑 ID Demande</div>
                    <div class="detail-value">#{{ $withdrawalRequest->id }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">💳 Compte Bancaire</div>
                    <div class="detail-value">{{ $withdrawalRequest->account_holder_name ?? 'Non spécifié' }}</div>
                </div>
            </div>
            
            <h3 style="color: #dc3545; margin-top: 30px;">📌 Raison du Rejet</h3>
            
            @if($withdrawalRequest->rejection_reason)
            <div class="reason-box">
                <p>{{ $withdrawalRequest->rejection_reason }}</p>
            </div>
            @else
            <div class="reason-box">
                <p>Aucune raison spécifiée. Veuillez contacter notre support pour plus de détails.</p>
            </div>
            @endif
            
            <h3 style="color: #dc3545; margin-top: 30px;">✨ Prochaines Étapes</h3>
            
            <div class="next-steps">
                <ul>
                    <li>Vérifiez vos informations bancaires (IBAN, titulaire)</li>
                    <li>Assurez-vous que les données sont correctes et valides</li>
                    <li>Créez une nouvelle demande de retrait après correction</li>
                </ul>
            </div>
            
            <p class="text-muted">Si vous pensez qu'il y a une erreur ou avez besoin de clarifications, contactez notre équipe de support.</p>
            
            <a href="https://uconnect.vibecro.com/support" class="btn">Contacter le Support</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
