<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrait Approuvé</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); overflow: hidden; }
        .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 50px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 32px; font-weight: 700; }
        .content { padding: 40px 30px; }
        .details-box { background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%); border: 1px solid #e0e7ff; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0; }
        .detail-label { font-size: 12px; font-weight: 700; color: #28a745; text-transform: uppercase; }
        .detail-value { font-size: 15px; color: #333; margin-top: 4px; }
        .amount-box { background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 1px solid #81c784; border-radius: 8px; padding: 25px; margin: 20px 0; text-align: center; }
        .amount-label { font-size: 12px; font-weight: 700; color: #28a745; text-transform: uppercase; }
        .amount-value { font-size: 32px; font-weight: 700; color: #28a745; margin-top: 8px; }
        .timeline { margin: 20px 0; }
        .timeline-item { margin: 15px 0; padding: 15px; background: #e8f5e9; border-left: 4px solid #28a745; border-radius: 4px; }
        .timeline-item strong { color: #28a745; }
        .info-box { background-color: #f0f8ff; border-left: 4px solid #007B80; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .text-muted { color: #999; font-size: 14px; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Retrait Approuvé!</h1>
            <p>Bonne Nouvelle</p>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $user->name }},</p>
            
            <p style="font-size: 16px; color: #28a745; font-weight: 600; margin: 20px 0;">Votre demande de retrait a été approuvée! 🎉</p>
            
            <div class="amount-box">
                <div class="amount-label">💰 Montant à Recevoir</div>
                <div class="amount-value">{{ number_format($withdrawalRequest->withdrawal_amount, 2, ',', ' ') }} €</div>
                <div class="text-muted" style="margin-top: 10px;">Montant Collecté: {{ number_format($withdrawalRequest->original_amount, 2, ',', ' ') }} € | Frais: {{ number_format($withdrawalRequest->platform_fee, 2, ',', ' ') }} €</div>
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
                <div class="detail-item">
                    <div class="detail-label">📍 IBAN</div>
                    <div class="detail-value">{{ $withdrawalRequest->iban ?? 'Non spécifié' }}</div>
                </div>
                @if($withdrawalRequest->transaction_reference)
                <div class="detail-item" style="border-bottom: none;">
                    <div class="detail-label">🔍 Référence de Transaction</div>
                    <div class="detail-value">{{ $withdrawalRequest->transaction_reference }}</div>
                </div>
                @endif
            </div>
            
            <h3 style="color: #28a745; margin-top: 30px;">📅 Calendrier de Traitement</h3>
            
            <div class="timeline">
                <div class="timeline-item">
                    <strong>✅ Aujourd'hui</strong> - Votre demande a été approuvée
                </div>
                <div class="timeline-item">
                    <strong>🏦 24 à 48h</strong> - Virement sur votre compte bancaire
                </div>
            </div>
            
            <div class="info-box">
                <strong>💡 Conseil</strong>
                <p>Le délai de virement dépend de votre banque. Veuillez vérifier que vos coordonnées bancaires sont correctes.</p>
            </div>
            
            <p class="text-muted">Si vous avez des questions, n'hésitez pas à nous contacter.</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
