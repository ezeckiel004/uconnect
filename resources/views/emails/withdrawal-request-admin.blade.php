<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .header h1 { margin: 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info-box { background: white; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; }
        .info-box .label { font-weight: bold; color: #007bff; }
        .info-box .value { color: #333; margin-top: 5px; }
        .amount-box { background: #e7f3ff; padding: 20px; border-radius: 5px; margin: 15px 0; text-align: center; }
        .amount-box .label { font-size: 12px; color: #666; }
        .amount-box .amount { font-size: 28px; font-weight: bold; color: #007bff; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; border-top: 1px solid #ddd; }
        .button { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Nouvelle Demande de Retrait</h1>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Une nouvelle demande de retrait a été créée automatiquement car une cagnote a atteint son objectif. Veuillez vérifier et traiter cette demande dès que possible.</p>

            <div class="info-box">
                <div class="label">📋 Cagnote</div>
                <div class="value">{{ $cagnote->title }}</div>
            </div>

            <div class="info-box">
                <div class="label">👥 Association</div>
                <div class="value">{{ $user->name }} ({{ $user->email }})</div>
            </div>

            <div class="info-box">
                <div class="label">📍 Catégorie</div>
                <div class="value">{{ $cagnote->category }}</div>
            </div>

            <div class="info-box">
                <div class="label">📝 Statut</div>
                <div class="value"><strong style="color: #ff9800;">EN ATTENTE DE TRAITEMENT</strong></div>
            </div>

            <h3 style="color: #007bff; margin-top: 30px;">Détails Financiers</h3>

            <div class="amount-box">
                <div class="label">Montant Collecté</div>
                <div class="amount">{{ number_format($originalAmount, 2, ',', ' ') }} €</div>
            </div>

            <div class="info-box">
                <div class="label">Montant à Verser (90%)</div>
                <div class="value" style="font-size: 18px; font-weight: bold; color: #28a745;">{{ number_format($withdrawalAmount, 2, ',', ' ') }} €</div>
            </div>

            <div class="info-box">
                <div class="label">Frais Plateforme (10%)</div>
                <div class="value">{{ number_format($platformFee, 2, ',', ' ') }} €</div>
            </div>

            <h3 style="color: #007bff; margin-top: 30px;">Données Bancaires Sauvegardées</h3>

            <div class="info-box">
                <div class="label">Titulaire du Compte</div>
                <div class="value">{{ $withdrawalRequest->account_holder_name ?? 'Non spécifié' }}</div>
            </div>

            <div class="info-box">
                <div class="label">IBAN</div>
                <div class="value">{{ $withdrawalRequest->iban ?? 'Non spécifié' }}</div>
            </div>

            <div class="info-box">
                <div class="label">BIC/SWIFT</div>
                <div class="value">{{ $withdrawalRequest->bic ?? 'Non spécifié' }}</div>
            </div>

            <div class="info-box">
                <div class="label">Banque</div>
                <div class="value">{{ $withdrawalRequest->bank_name ?? 'Non spécifié' }}</div>
            </div>

            <div class="info-box">
                <div class="label">Email de Contact</div>
                <div class="value">{{ $withdrawalRequest->account_email ?? 'Non spécifié' }}</div>
            </div>

            <p style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 3px;">
                <strong>⚠️ Important:</strong> Veuillez vérifier les données bancaires et traiter ce retrait dans les meilleurs délais pour respecter l'engagement envers l'association.
            </p>

            <center>
                <a href="{{ env('APP_URL') }}/admin/withdrawal-requests" class="button">Accéder au Tableau de Bord</a>
            </center>
        </div>
        <div class="footer">
            <p>{{ env('APP_NAME') }} - Plateforme de Cagnotes Solidaires</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
