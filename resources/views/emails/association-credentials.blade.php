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
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6B2C3E 0%, #8B3A4F 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
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
            <h1>🎉 U-Connect</h1>
            <p>Vos identifiants d'accès</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                <p>Bonjour <strong>{{ $associationName }}</strong>,</p>
                <p>L'administrateur de U-Connect vous a attribué des identifiants d'accès pour votre association. Ci-dessous, vous trouverez tous vos identifiants de connexion.</p>
            </div>

            <!-- Credentials Box -->
            <div class="credentials-box">
                <div class="credential-item">
                    <div class="credential-label">📌 Code d'association</div>
                    <div class="credential-value">{{ $code }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">🔐 Mot de passe</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">📧 Email</div>
                    <div class="credential-value">{{ $email }}</div>
                </div>
            </div>

            <!-- Information Section -->
            <div class="info-section">
                <strong>⚠️ Information importante :</strong>
                <ul>
                    <li>Ne partagez jamais vos identifiants avec d'autres personnes</li>
                    <li>Conservez vos identifiants en lieu sûr</li>
                    <li>Vous pouvez modifier votre mot de passe depuis votre tableau de bord</li>
                    <li>Si vous avez oublié vos identifiants, contactez l'administrateur</li>
                </ul>
            </div>

            <!-- Call to Action -->
            <div class="button-container">
                <a href="https://u-connect.app" class="btn">Accéder à U-Connect</a>
            </div>

            <!-- Additional Info -->
            <p style="font-size: 14px; color: #666; margin-top: 30px;">
                Si vous avez des questions ou besoin d'aide, n'hésitez pas à contacter notre équipe de support à <strong>support@u-connect.com</strong>.
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
