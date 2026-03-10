<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de réinitialisation - U-Connect</title>
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
        .code-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%);
            border: 2px solid #007B80;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }
        .code-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .code {
            font-size: 42px;
            font-weight: 700;
            color: #007B80;
            letter-spacing: 10px;
            margin: 0;
            font-family: 'Courier New', monospace;
        }
        .expiry {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 15px;
            font-weight: 600;
        }
        .security-notice {
            background-color: #fef3e6;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .security-notice strong {
            color: #d68910;
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
            <p>Réinitialisation de Mot de Passe</p>
        </div>
        
        <div class="content">
            <p class="greeting">Bonjour <strong>{{ $userName }}</strong>,</p>
            
            <p>Vous avez demandé une réinitialisation de mot de passe. Utilisez le code ci-dessous pour réinitialiser votre accès U-Connect.</p>
            
            <div class="code-box">
                <div class="code-label">Votre code de réinitialisation</div>
                <p class="code">{{ $code }}</p>
                <div class="expiry">⏱️ Valide pendant 15 minutes</div>
            </div>
            
            <div class="security-notice">
                <strong>🔒 Sécurité:</strong> Ne partagez jamais ce code. L'équipe U-Connect ne vous le demandera jamais par email.
            </div>
            
            <p style="margin-top: 30px; line-height: 1.8;">
                Si vous n'avez pas demandé cette réinitialisation, ignorez cet email. Votre compte reste sécurisé.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>U-Connect</strong> - Ensemble pour un monde meilleur</p>
            <p>&copy; {{ date('Y') }} U-Connect. Tous droits réservés.</p>
            <p><a href="https://uconnect.vibecro.com" style="color: #007B80; text-decoration: none;">Visiter notre site</a></p>
        </div>
    </div>
</body>
</html>
