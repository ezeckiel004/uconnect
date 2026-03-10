<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos identifiants d'accès U-Connect</title>
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
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 12px 0 0 0;
            font-size: 14px;
            opacity: 0.95;
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
        .credentials-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%);
            border: 1px solid #e0e7ff;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .credential-item {
            margin-bottom: 18px;
        }
        .credential-label {
            font-size: 12px;
            font-weight: 700;
            color: #12546D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .credential-value {
            background-color: #ffffff;
            border: 2px solid #007B80;
            padding: 12px 14px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 15px;
            word-break: break-all;
            color: #333;
            font-weight: 600;
        }
        .info-section {
            background-color: #f0f8ff;
            border-left: 4px solid #007B80;
            padding: 15px;
            margin: 25px 0;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            line-height: 1.7;
        }
        .info-section strong {
            color: #12546D;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 35px;
            background-color: #007B80;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #005f63;
        }
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            padding: 25px 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .footer p {
            margin: 6px 0;
        }
        .security-badge {
            display: inline-block;
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 12px 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 13px;
            color: #856404;
        }
        ul {
            margin: 12px 0;
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>U-Connect</h1>
            <p>Vos identifiants d'accès</p>
        </div>

        <div class="content">
            <p class="greeting"><strong>Bienvenue {{ $associationName }}!</strong></p>
            
            <p>L'administrateur de U-Connect a activé votre compte. Vos identifiants de connexion sont présentés ci-dessous pour accéder à votre tableau de bord.</p>

            <div class="credentials-box">
                <div class="credential-item">
                    <div class="credential-label">📌 Code Association</div>
                    <div class="credential-value">{{ $code }}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">🔐 Mot de Passe</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">📧 Adresse Email</div>
                    <div class="credential-value">{{ $email }}</div>
                </div>
            </div>

            <div class="security-badge">
                <strong>🔒 Conseil sécurité:</strong> Gardez vos identifiants confidentiels et modifiez votre mot de passe après la première connexion.
            </div>

            <div class="info-section">
                <strong>ℹ️ Prochaines étapes:</strong>
                <ul>
                    <li>Connectez-vous à U-Connect avec vos identifiants</li>
                    <li>Complétez votre profil d'association</li>
                    <li>Modifiez votre mot de passe pour plus de sécurité</li>
                    <li>Explorez les fonctionnalités disponibles pour votre organisation</li>
                </ul>
            </div>

            <div class="button-container">
                <a href="https://uconnect.vibecro.com" class="btn" style="display: inline-block;">Se Connecter à U-Connect</a>
            </div>

            <p style="font-size: 14px; color: #666; margin-top: 30px; line-height: 1.8;">
                Des questions? Notre équipe support est là pour vous aider à <strong>support@u-connect.org</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>U-Connect</strong> - Ensemble pour un monde meilleur</p>
            <p>&copy; {{ date('Y') }} U-Connect. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement. Veuillez ne pas y répondre directement.</p>
        </div>
    </div>
</body>
</html>
