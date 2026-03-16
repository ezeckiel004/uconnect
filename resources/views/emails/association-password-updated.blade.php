<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe mis à jour - U-Connect</title>
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
            background: linear-gradient(135deg, #12546D 0%, #007B80 100%);
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
        .info-section {
            background-color: #f0f8ff;
            border-left: 4px solid #007B80;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
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
            <h1>U-Connect</h1>
            <p>Mot de Passe Mis à Jour</p>
        </div>
        
        <div class="content">
            <p class="greeting">Bonjour <strong>{{ $associationName }}</strong>,</p>
            
            <p>Le mot de passe de votre compte U-Connect a été mis à jour avec succès.</p>
            
            <div class="success-box">
                <strong>✅ Modification confirmée</strong>
            </div>
            
            <div class="info-section">
                <strong>📌 Informations:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li style="margin: 8px 0;">Votre nouveau mot de passe est maintenant actif</li>
                    <li style="margin: 8px 0;">Vous pouvez vous connecter immédiatement avec votre nouveau mot de passe</li>
                    <li style="margin: 8px 0;">Conservez votre mot de passe en lieu sûr</li>
                </ul>
            </div>
            
            <p style="margin-top: 30px; line-height: 1.8;">
                Si vous n'avez pas effectué ce changement, <strong>contactez immédiatement</strong> notre support à contact@u-connect.org
            </p>
        </div>
        
        <div class="footer">
            <p><strong>U-Connect</strong> - Ensemble pour un monde meilleur</p>
            <p>&copy; {{ date('Y') }} U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .alert-box strong {
            color: #856404;
        }
        .credentials-box {
            background-color: #f9f9f9;
            border: 2px solid #6B2C3E;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin-bottom: 15px;
        }
        .credential-label {
            font-size: 12px;
            font-weight: 600;
            color: #6B2C3E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .credential-value {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 10px 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            word-break: break-all;
            color: #333;
        }
        .info-section {
            background-color: #eff6ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
        }
        .info-section strong {
            color: #0066cc;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #6B2C3E;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #8B3A4F;
        }
        .footer {
            background-color: #f9f9f9;
            border-top: 1px solid #eee;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 U-Connect</h1>
            <p>Mise à jour de sécurité</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                <p>Bonjour <strong>{{ $associationName }}</strong>,</p>
                <p>Votre mot de passe a été mis à jour par l'administrateur de U-Connect. Voici vos nouveaux identifiants de connexion.</p>
            </div>

            <!-- Alert Box -->
            <div class="alert-box">
                <strong>⚠️ Attention :</strong> Votre mot de passe a été modifié. Assurez-vous de le mémoriser ou de le conserver en lieu sûr.
            </div>

            <!-- Credentials Box -->
            <div class="credentials-box">
                <div class="credential-item">
                    <div class="credential-label">📌 Code d'association</div>
                    <div class="credential-value">{{ $code }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">🔐 Nouveau mot de passe</div>
                    <div class="credential-value">{{ $newPassword }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">📧 Email</div>
                    <div class="credential-value">{{ $email }}</div>
                </div>
            </div>

            <!-- Information Section -->
            <div class="info-section">
                <strong>💡 Recommandations de sécurité :</strong>
                <ul>
                    <li>Ne partagez jamais vos identifiants avec d'autres personnes</li>
                    <li>Utilisez un mot de passe unique et sécurisé</li>
                    <li>Vous pouvez modifier votre mot de passe depuis votre tableau de bord</li>
                    <li>Connectez-vous dès que possible pour mettre à jour vos préférences</li>
                </ul>
            </div>

            <!-- Call to Action -->
            <div class="button-container">
                <a href="https://u-connect.app" class="btn">Accéder à U-Connect</a>
            </div>

            <!-- Additional Info -->
            <p style="font-size: 14px; color: #666; margin-top: 30px;">
                Si vous n'avez pas demandé cette modification ou si vous avez des questions, contactez immédiatement notre équipe de support à <strong>support@u-connect.com</strong>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2026 U-Connect. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement. Veuillez ne pas y répondre directement.</p>
        </div>
    </div>
</body>
</html>
