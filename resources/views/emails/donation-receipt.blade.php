@component('mail::message')
# 🎉 Merci pour votre donation!

Bonjour {{ $donation->donor_name ?? 'Donateur' }},

Nous vous confirmons la réception de votre donation pour la campagne **{{ $donation->cagnote->title }}**.

---

## Récapitulatif de la transaction

| | |
|---|---|
| **Montant du don** | {{ number_format($donation->amount, 2) }}€ |
| **Frais de transaction** | {{ number_format($donation->fees, 2) }}€ |
| **Total débité** | **{{ number_format($donation->total_amount, 2) }}€** |
| **Date** | {{ $donation->created_at->format('d/m/Y à H:i') }} |
| **N° de transaction** | `{{ $donation->stripe_charge_id ?? 'Traitement en cours...' }}` |

---

**Important :** Les frais de transaction nous permettent de couvrir les coûts du service de paiement Stripe, garantissant que l'ASBL reçoit le montant complet de votre donation.

@if($donation->donor_message)
### Votre message
> {{ $donation->donor_message }}

@endif

@component('mail::button', ['url' => config('app.url') . '/donations'])
Voir l'historique de vos donations
@endcomponent

---

## Besoin d'aide?

Si vous avez des questions concernant votre donation ou nos campagnes, n'hésitez pas à nous contacter:

📧 **contact@u-connect.org**

Merci de soutenir U-Connect! 💙

**L'équipe U-Connect**

@endcomponent
