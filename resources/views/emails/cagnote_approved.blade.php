<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cagnote Approuvée - U-Connect</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #ffffff;
            padding: 50px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
            line-height: 1.8;
        }
        .success-box {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .success-box strong {
            color: #155724;
            font-size: 16px;
        }
        .details-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%);
            border: 1px solid #e0e7ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-item {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-size: 12px;
            font-weight: 700;
            color: #12546D;
            text-transform: uppercase;
        }
        .detail-value {
            font-size: 15px;
            color: #333;
            margin-top: 4px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 35px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
        }
        .info-section {
            background-color: #f0f8ff;
            border-left: 4px solid #007B80;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.7;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 6px 0;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Cagnote Approuvée!</h1>
            <p>Votre cagnote est maintenant en ligne</p>
        </div>
        
        <div class="content">
            <p class="greeting">Bonjour<strong> {{ $association->name }}</strong>,</p>
            
            <p>Excellente nouvelle! Votre cagnote a été validée par notre équipe et est maintenant <strong>en ligne pour recevoir des donations</strong>.</p>
            
            <div class="success-box">
                <strong>🎉 Cagnote Active et En Ligne</strong>
            </div>
            
            <h3 style="color: #12546D; margin-top: 30px;">Détails de votre cagnote:</h3>
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">📝 Titre</div>
                    <div class="detail-value">{{ $cagnote->title }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">💰 Objectif</div>
                    <div class="detail-value">{{ $cagnote->goal_amount }} EUR</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">📅 Date de clôture</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($cagnote->end_date)->format('d/m/Y') }}</div>
                </div>
            </div>
            
            <div class="info-section">
                <strong>📌 Prochaines étapes:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li style="margin: 8px 0;">Partagez votre cagnote avec votre réseau</li>
                    <li style="margin: 8px 0;">Mettez à jour régulièrement la description et les photos</li>
                    <li style="margin: 8px 0;">Suivez vos donations sur votre tableau de bord</li>
                    <li style="margin: 8px 0;">Remerciez vos donateurs régulièrement</li>
                </ul>
            </div>
            
            <div class="button-container">
                <a href="https://uconnect.vibecro.com/cagnotes/{{ $cagnote->id }}" class="btn">Voir ma Cagnote</a>
            </div>
            
            <p style="margin-top: 30px; line-height: 1.8;">
                Besoin d'aide? Contactez-nous à contact@u-connect.org
            </p>
        </div>
        
        <div class="footer">
            <p><strong>U-Connect</strong> - Ensemble pour un monde meilleur</p>
            <p>&copy; {{ date('Y') }} U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
                <li><strong>Statut:</strong> Approuvée</li>
                <li><strong>Catégorie:</strong> {{ $cagnote->category }}</li>
                <li><strong>Objectif:</strong> {{ $cagnote->objective_amount }}€</li>
                <li><strong>Lieu:</strong> {{ $cagnote->location }}</li>
            </ul>
            
            <p>Les utilisateurs de U-Connect peuvent maintenant découvrir votre cagnote et y contribuer.</p>
            
            <p style="text-align: center;">
                <a href="{{ config('app.url') }}/cagnotes/{{ $cagnote->id }}" class="button">Voir votre cagnote</a>
            </p>
            
            <p>Merci d'utiliser U-Connect pour soutenir votre cause!</p>
            <p>Cordialement,<br><strong>L'équipe U-Connect</strong></p>
        </div>
        <div class="footer">
            <p>&copy; 2026 U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
