<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px; border-radius: 8px;">
    <div style="background-color: #fff; padding: 30px; border-radius: 8px; text-align: center;">
        <h1 style="color: #333; margin-bottom: 10px;">U-Connect</h1>
        <h2 style="color: #666; font-size: 20px; margin-bottom: 30px;">Réinitialisation de mot de passe</h2>
        
        <p style="color: #555; font-size: 16px; margin-bottom: 20px;">
            Bonjour {{ $userName }},
        </p>
        
        <p style="color: #555; font-size: 16px; margin-bottom: 30px;">
            Vous avez demandé une réinitialisation de mot de passe. Veuillez utiliser le code ci-dessous dans l'application pour réinitialiser votre mot de passe.
        </p>
        
        <div style="background-color: #f0f0f0; padding: 20px; border-radius: 6px; margin-bottom: 30px;">
            <p style="color: #666; font-size: 14px; margin-bottom: 10px;">Votre code de réinitialisation:</p>
            <p style="font-size: 36px; font-weight: bold; color: #007bff; letter-spacing: 8px; margin: 0;">
                {{ $code }}
            </p>
            <p style="color: #999; font-size: 12px; margin-top: 10px;">
                Ce code expire dans 15 minutes.
            </p>
        </div>
        
        <p style="color: #999; font-size: 14px; margin-bottom: 20px;">
            <strong>Sécurité:</strong> Ne partagez jamais ce code avec quiconque. L'équipe U-Connect ne vous demandera jamais votre code par email.
        </p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="color: #999; font-size: 12px;">
            © {{ date('Y') }} U-Connect. Tous droits réservés.
        </p>
    </div>
</div>
