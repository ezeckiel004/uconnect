<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .header h1 { margin: 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info-box { background: white; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
        .info-box .label { font-weight: bold; color: #28a745; }
        .info-box .value { color: #333; margin-top: 5px; }
        .amount-box { background: #e8f5e9; padding: 20px; border-radius: 5px; margin: 15px 0; text-align: center; }
        .amount-box .label { font-size: 12px; color: #666; }
        .amount-box .amount { font-size: 32px; font-weight: bold; color: #28a745; }
        .timeline { margin: 20px 0; padding: 15px; background: #e3f2fd; border-radius: 5px; }
        .timeline-item { margin: 10px 0; padding-left: 20px; border-left: 2px solid #007bff; }
        .timeline-item strong { color: #007bff; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; border-top: 1px solid #ddd; }
        .success-badge { background: #28a745; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Demande de Retrait Approuvée!</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $user->name }},</p>
            
            <p style="font-size: 16px; color: #28a745; font-weight: bold;">
                🎉 Bonne nouvelle! Votre demande de retrait a été approuvée par notre équipe!
            </p>

            <div class="info-box">
                <div class="label">📋 ID Demande</div>
                <div class="value">#{{ $withdrawalRequest->id }}</div>
            </div>

            <div class="amount-box">
                <div class="label">Montant à Recevoir</div>
                <div class="amount">{{ number_format($withdrawalRequest->withdrawal_amount, 2, ',', ' ') }} €</div>
            </div>

            <div class="info-box">
                <div class="label">💳 Compte Bancaire</div>
                <div class="value">{{ $withdrawalRequest->account_holder_name ?? 'Non spécifié' }}</div>
            </div>

            <div class="info-box">
                <div class="label">📍 IBAN</div>
                <div class="value">{{ $withdrawalRequest->iban ?? 'Non spécifié' }}</div>
            </div>

            @if($withdrawalRequest->transaction_reference)
            <div class="info-box">
                <div class="label">🔍 Référence de Transaction</div>
                <div class="value">{{ $withdrawalRequest->transaction_reference }}</div>
            </div>
            @endif

            <div class="info-box">
                <div class="label">💰 Montant Original Collecté</div>
                <div class="value">{{ number_format($withdrawalRequest->original_amount, 2, ',', ' ') }} €</div>
            </div>

            <div class="info-box">
                <div class="label">📊 Frais Plateforme (10%)</div>
                <div class="value">{{ number_format($withdrawalRequest->platform_fee, 2, ',', ' ') }} €</div>
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

            <p style="margin-top: 30px; color: #666;">
                <strong>Note:</strong> Le délai de virement dépend de votre banque. Veuillez vérifier que vos coordonnées bancaires sont correctes.
            </p>

            <p style="margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px; font-size: 12px;">
                Si vous avez des questions, vous pouvez nous contacter en répondant à cet email.
            </p>

            <div class="footer">
                <p>U-Connect • Plateforme de Financement Participatif</p>
                <p>© {{ date('Y') }} Tous droits réservés</p>
            </div>
        </div>
    </div>
</body>
</html>
