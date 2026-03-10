<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Cagnote - À Valider</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); overflow: hidden; }
        .header { background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%); color: white; padding: 50px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 32px; font-weight: 700; }
        .content { padding: 40px 30px; }
        .details-box { background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%); border: 1px solid #e0e7ff; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .detail-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0; }
        .detail-label { font-size: 12px; font-weight: 700; color: #12546D; text-transform: uppercase; }
        .detail-value { font-size: 15px; color: #333; margin-top: 4px; }
        .text-muted { color: #999; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 35px; background-color: #FFA500; color: white; text-decoration: none; border-radius: 6px; font-weight: 700; margin-top: 20px; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Nouvelle Cagnote</h1>
            <p>À Valider</p>
        </div>
        
        <div class="content">
            <p>Une nouvelle cagnote nécessite votre validation:</p>
            
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">📝 Titre</div>
                    <div class="detail-value">{{ $cagnote->title }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">🏢 Association</div>
                    <div class="detail-value">{{ $association->name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">💰 Objectif</div>
                    <div class="detail-value">{{ $cagnote->goal_amount }} EUR</div>
                </div>
            </div>
            
            <p class="text-muted">Veuillez consulter le tableau d'administration pour évaluer et valider cette cagnote.</p>
            
            <a href="https://uconnect.vibecro.com/admin/cagnotes" class="btn">Voir les Cagnotes en Attente</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nouvelle Cagnote à Valider</h2>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Une nouvelle cagnote a été créée et attend votre validation.</p>
            
            <h3>Détails de la cagnote:</h3>
            <ul>
                <li><strong>Titre:</strong> {{ $cagnote->title }}</li>
                <li><strong>Association:</strong> {{ $association->name }}</li>
                <li><strong>Catégorie:</strong> {{ $cagnote->category }}</li>
                <li><strong>Objectif:</strong> {{ $cagnote->objective_amount }}€</li>
                <li><strong>Localisation:</strong> {{ $cagnote->location }}</li>
                <li><strong>Description:</strong> {{ $cagnote->description }}</li>
            </ul>
            
            <p>Veuillez analyser cette cagnote et la valider ou la rejeter.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('admin.cagnotes.review', $cagnote->id) }}" class="button">Consulter la cagnote</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2026 U-Connect. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
