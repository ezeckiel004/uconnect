<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virement En Cours</title>
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
        .timeline-item { margin: 15px 0; padding: 15px; background: #f0f8ff; border-left: 4px solid #007B80; border-radius: 4px; }
        .timeline-item strong { color: #28a745; }
        .info-box { background-color: #e8f5e9; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .faq-section { background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .text-muted { color: #999; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 35px; background-color: #28a745; color: white; text-decoration: none; border-radius: 6px; font-weight: 700; margin-top: 20px; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Virement En Cours!</h1>
            <p>Félicitations!</p>
        </div>
        
        <div class="content">
            <p>Chère {{ $user->name }},</p>
            
            <p style="font-size: 16px; color: #28a745; font-weight: 600; margin: 20px 0;">Votre cagnote a atteint son objectif! 🚀</p>
            
            <p>Une demande de retrait a été automatiquement créée et sera traitée par nos équipes. Vous recevrez votre virement dans les <strong>72 heures</strong>.</p>
            
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">📋 Cagnote</div>
                    <div class="detail-value">{{ $cagnote->title }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">📍 Catégorie</div>
                    <div class="detail-value">{{ $cagnote->category }}</div>
                </div>
                <div class="detail-item" style="border-bottom: none;">
                    <div class="detail-label">🔑 ID Demande</div>
                    <div class="detail-value">#{{ $withdrawalRequest->id }}</div>
                </div>
            </div>
            
            <div class="amount-box">
                <div class="amount-label">💰 Montant Total Collecté</div>
                <div class="amount-value">{{ number_format($originalAmount, 2, ',', ' ') }} €</div>
                <div class="text-muted" style="margin-top: 10px;">À Recevoir: {{ number_format($withdrawalAmount, 2, ',', ' ') }} € | Frais: {{ number_format($platformFee, 2, ',', ' ') }} €</div>
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
                    <strong>🏦 Dans 72h (maximum)</strong> - Virement sur votre compte
                </div>
            </div>
            
            <h3 style="color: #28a745; margin-top: 30px;">🏦 Données Bancaires</h3>
            
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">Titulaire du Compte</div>
                    <div class="detail-value">{{ $withdrawalRequest->account_holder_name ?? 'Non spécifié' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">IBAN</div>
                    <div class="detail-value">{{ $withdrawalRequest->iban ?? 'Non spécifié' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Banque</div>
                    <div class="detail-value">{{ $withdrawalRequest->bank_name ?? 'Non spécifié' }}</div>
                </div>
            </div>
            
            <div class="info-box">
                <strong>✓ Vérifiez vos données</strong>
                <p>Assurez-vous que l'IBAN et le titulaire du compte sont corrects. Contactez-nous immédiatement si des modifications sont nécessaires.</p>
            </div>
            
            <h3 style="color: #28a745; margin-top: 30px;">❓ Questions Fréquentes</h3>
            
            <div class="faq-section">
                <p><strong>Pourquoi 90% et non 100%?</strong></p>
                <p class="text-muted">La plateforme prélève 10% de frais pour les coûts de transaction, la vérification des données, et la sécurité.</p>

                <p style="margin-top: 15px;"><strong>Quand recevrai-je mon virement?</strong></p>
                <p class="text-muted">Sous 72 heures ouvrables à compter de la création de cette demande.</p>

                <p style="margin-top: 15px;"><strong>Puis-je suivre ma demande?</strong></p>
                <p class="text-muted">Oui, vous pouvez consulter le statut dans votre espace personnel.</p>
            </div>
            
            <a href="{{ env('APP_URL') }}/dashboard/withdrawals" class="btn">Suivre Ma Demande</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} U-Connect. Tous droits réservés.</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
