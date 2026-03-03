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
        .button { display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .success-badge { background: #28a745; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Virement en Cours!</h1>
        </div>
        <div class="content">
            <p>Chère {{ $user->name }},</p>
            
            <p style="font-size: 16px; color: #28a745; font-weight: bold;">
                🎉 Félicitations! Votre cagnote a atteint son objectif!
            </p>

            <p>Une demande de retrait a été automatiquement créée et sera traitée par nos équipes. Vous recevrez votre virement dans les <strong>72 heures</strong></p>

            <div class="info-box">
                <div class="label">📋 Cagnote</div>
                <div class="value">{{ $cagnote->title }}</div>
            </div>

            <div class="info-box">
                <div class="label">📍 Catégorie</div>
                <div class="value">{{ $cagnote->category }}</div>
            </div>

            <div class="info-box">
                <div class="label">ID Demande de Retrait</div>
                <div class="value"><code>#{{ $withdrawalRequest->id }}</code></div>
            </div>

            <h3 style="color: #28a745; margin-top: 30px;">Résumé de Votre Collecte</h3>

            <div class="amount-box">
                <div class="label">Montant Total Collecté</div>
                <div class="amount">{{ number_format($originalAmount, 2, ',', ' ') }} €</div>
            </div>

            <div class="info-box">
                <div class="label">💳 Montant à Recevoir</div>
                <div class="value" style="font-size: 20px; font-weight: bold; color: #28a745;">{{ number_format($withdrawalAmount, 2, ',', ' ') }} €</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">Après déduction de 10% de frais de plateforme</div>
            </div>

            <div class="info-box">
                <div class="label">📊 Frais Plateforme</div>
                <div class="value">{{ number_format($platformFee, 2, ',', ' ') }} €</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">10% des fonds collectés sont versés à la plateforme pour couvrir les frais de traitement</div>
            </div>

            <h3 style="color: #28a745; margin-top: 30px;">📅 Calendrier de Traitement</h3>

            <div class="timeline">
                <div class="timeline-item">
                    <strong>✅ Aujourd'hui</strong> - Demande de retrait créée automatiquement
                </div>
                <div class="timeline-item">
                    <strong>⏳ Demain</strong> - Vérification par nos équipes
                </div>
                <div class="timeline-item">
                    <strong>🏦 Dans 72h (maximum)</strong> - Virement sur votre compte bancaire
                </div>
            </div>

            <h3 style="color: #28a745; margin-top: 30px;">💰 Données Bancaires Enregistrées</h3>

            <div class="info-box">
                <div class="label">Titulaire du Compte</div>
                <div class="value">{{ $withdrawalRequest->account_holder_name ?? 'Non spécifié' }}</div>
            </div>

            <div class="info-box">
                <div class="label">IBAN</div>
                <div class="value">
                    {{ $withdrawalRequest->iban ?? 'Non spécifié' }}
                    <span class="success-badge" style="float: right;">Vérifié</span>
                </div>
            </div>

            <div class="info-box">
                <div class="label">Banque</div>
                <div class="value">{{ $withdrawalRequest->bank_name ?? 'Non spécifié' }}</div>
            </div>

            <p style="margin-top: 30px; padding: 15px; background: #e8f5e9; border-left: 4px solid #28a745; border-radius: 3px;">
                <strong>✓ Vérifiez vos données bancaires:</strong> Assurez-vous que l'IBAN et le titulaire du compte sont corrects. Si vous avez besoin de modifier ces informations, veuillez nous contacter immédiatement.
            </p>

            <h3 style="color: #28a745; margin-top: 30px;">❓ Questions Fréquentes</h3>

            <div style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
                <p><strong>Pourquoi 90% et non 100%?</strong></p>
                <p>La plateforme U-Connect prélève 10% de frais pour couvrir les coûts de transaction, de vérification des données bancaires, et de sécurité.</p>

                <p style="margin-top: 15px;"><strong>Quand recevrai-je mon virement?</strong></p>
                <p>Sous 72 heures ouvrables à compter de la création automatique de cette demande.</p>

                <p style="margin-top: 15px;"><strong>Puis-je suivre l'état de ma demande?</strong></p>
                <p>Oui, vous pouvez consulter le statut de votre retrait dans votre espace personnel.</p>
            </div>

            <center>
                <a href="{{ env('APP_URL') }}/dashboard/withdrawals" class="button">Suivre Ma Demande</a>
            </center>

            <p style="margin-top: 30px; text-align: center; font-size: 12px; color: #999;">
                Si vous avez des questions, n'hésitez pas à nous contacter à support@uconnect.com
            </p>
        </div>
        <div class="footer">
            <p>{{ env('APP_NAME') }} - Plateforme de Cagnotes Solidaires</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
            <p>Merci de votre confiance!</p>
        </div>
    </div>
</body>
</html>
