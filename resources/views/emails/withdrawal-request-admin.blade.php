<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Retrait</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); overflow: hidden; }
        .header { background: linear-gradient(135deg, #FF9500 0%, #FF8C00 100%); color: white; padding: 50px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 32px; font-weight: 700; }
        .content { padding: 40px 30px; }
        .details-box { background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%); border: 1px solid #e0e7ff; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0; }
        .detail-label { font-size: 12px; font-weight: 700; color: #FF9500; text-transform: uppercase; }
        .detail-value { font-size: 15px; color: #333; margin-top: 4px; }
        .amount-box { background: linear-gradient(135deg, #fff8e1 0%, #ffe8a3 100%); border: 1px solid #ffc107; border-radius: 8px; padding: 25px; margin: 20px 0; text-align: center; }
        .amount-label { font-size: 12px; font-weight: 700; color: #FF9500; text-transform: uppercase; }
        .amount-value { font-size: 32px; font-weight: 700; color: #FF8C00; margin-top: 8px; }
        .warning-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .warning-box strong { color: #856404; }
        .text-muted { color: #999; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 35px; background-color: #FF9500; color: white; text-decoration: none; border-radius: 6px; font-weight: 700; margin-top: 20px; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Demande de Retrait</h1>
            <p>À Traiter</p>
        </div>
        
        <div class="content">
            <p>Une nouvelle demande de retrait nécessite votre attention.</p>
            
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">📋 Cagnote</div>
                    <div class="detail-value">{{ $cagnote->title }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">👥 Association</div>
                    <div class="detail-value">{{ $user->name }} ({{ $user->email }})</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">📍 Catégorie</div>
                    <div class="detail-value">{{ $cagnote->category }}</div>
                </div>
            </div>
            
            <div class="amount-box">
                <div class="amount-label">💰 Montant à Verser (90%)</div>
                <div class="amount-value">{{ number_format($withdrawalAmount, 2, ',', ' ') }} €</div>
                <div class="text-muted" style="margin-top: 10px;">Montant Collecté: {{ number_format($originalAmount, 2, ',', ' ') }} € | Frais: {{ number_format($platformFee, 2, ',', ' ') }} €</div>
            </div>
            
            <div class="details-box">
                <div class="detail-item" style="border-bottom: none;">
                    <div class="detail-label">🏦 Informations Bancaires</div>
                </div>
                <li><strong>Titulaire:</strong> {{ $withdrawalRequest->account_holder_name ?? 'Non spécifié' }}</li>
                <li><strong>IBAN:</strong> {{ $withdrawalRequest->iban ?? 'Non spécifié' }}</li>
                <li><strong>BIC/SWIFT:</strong> {{ $withdrawalRequest->bic ?? 'Non spécifié' }}</li>
                <li><strong>Banque:</strong> {{ $withdrawalRequest->bank_name ?? 'Non spécifié' }}</li>
                <li><strong>Email:</strong> {{ $withdrawalRequest->account_email ?? 'Non spécifié' }}</li>
            </div>
            
            <div class="warning-box">
                <strong>⚠️ Action Requise</strong>
                <p>Veuillez vérifier les informations bancaires et traiter ce retrait dans les meilleurs délais pour respecter l'engagement envers l'association.</p>
            </div>
            
            <a href="{{ env('APP_URL') }}/admin/withdrawal-requests" class="btn">Accéder à l'Administration</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} U-Connect. Tous droits réservés.</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
