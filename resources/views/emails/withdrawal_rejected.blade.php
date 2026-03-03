<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .header h1 { margin: 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info-box { background: white; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0; }
        .info-box .label { font-weight: bold; color: #dc3545; }
        .info-box .value { color: #333; margin-top: 5px; }
        .amount-box { background: #ffe8e8; padding: 20px; border-radius: 5px; margin: 15px 0; text-align: center; }
        .amount-box .label { font-size: 12px; color: #666; }
        .amount-box .amount { font-size: 32px; font-weight: bold; color: #dc3545; }
        .reason-box { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; border-radius: 3px; }
        .timeline { margin: 20px 0; padding: 15px; background: #e3f2fd; border-radius: 5px; }
        .timeline-item { margin: 10px 0; padding-left: 20px; border-left: 2px solid #dc3545; }
        .timeline-item strong { color: #dc3545; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Demande de Retrait Rejetée</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $user->name }},</p>
            
            <p style="font-size: 16px; color: #dc3545; font-weight: bold;">
                ⚠️ Votre demande de retrait a été rejetée par notre équipe.
            </p>

            <div class="info-box">
                <div class="label">📋 ID Demande</div>
                <div class="value">#{{ $withdrawalRequest->id }}</div>
            </div>

            <div class="amount-box">
                <div class="label">Montant Demandé</div>
                <div class="amount">{{ number_format($withdrawalRequest->withdrawal_amount, 2, ',', ' ') }} €</div>
            </div>

            <h3 style="color: #dc3545; margin-top: 30px;">📌 Raison du Rejet</h3>

            @if($withdrawalRequest->rejection_reason)
            <div class="reason-box">
                <p style="margin: 0; font-size: 15px;">
                    {{ $withdrawalRequest->rejection_reason }}
                </p>
            </div>
            @else
            <div class="reason-box">
                <p style="margin: 0; font-size: 15px;">
                    Aucune raison spécifiée. Veuillez contacter notre support pour plus de détails.
                </p>
            </div>
            @endif

            <div class="info-box">
                <div class="label">💰 Montant Original Collecté</div>
                <div class="value">{{ number_format($withdrawalRequest->original_amount, 2, ',', ' ') }} €</div>
            </div>

            <div class="info-box">
                <div class="label">💳 Compte Bancaire</div>
                <div class="value">{{ $withdrawalRequest->account_holder_name ?? 'Non spécifié' }}</div>
            </div>

            <h3 style="color: #dc3545; margin-top: 30px;">📞 Prochaines Étapes</h3>

            <p>
                Si vous pensez qu'il y a une erreur, vous pouvez:
            </p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Vérifier les informations de votre compte bancaire</li>
                <li>Nous contacter via notre formulaire de support</li>
                <li>Créer une nouvelle demande de retrait après correction</li>
            </ul>

            <p style="margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px; font-size: 12px;">
                <strong>Besoin d'aide?</strong> Répondez à cet email ou contactez notre équipe de support.
            </p>

            <div class="footer">
                <p>U-Connect • Plateforme de Financement Participatif</p>
                <p>© {{ date('Y') }} Tous droits réservés</p>
            </div>
        </div>
    </div>
</body>
</html>
