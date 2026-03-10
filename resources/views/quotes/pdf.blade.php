<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis {{ $quote->quote_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .company-info {
            max-width: 50%;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
        }
        .company-details {
            color: #666;
            font-size: 11px;
        }
        .quote-info {
            text-align: right;
        }
        .quote-title {
            font-size: 28px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
        }
        .quote-number {
            font-size: 14px;
            color: #666;
        }
        .quote-meta {
            margin-top: 10px;
        }
        .quote-meta p {
            margin: 3px 0;
        }
        .addresses {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .address-block {
            width: 45%;
        }
        .address-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .address-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .address-details {
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead th {
            background-color: #f0fdf4;
            padding: 12px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #059669;
        }
        thead th:last-child,
        thead th:nth-child(2),
        thead th:nth-child(3),
        thead th:nth-child(4) {
            text-align: right;
        }
        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody td:last-child,
        tbody td:nth-child(2),
        tbody td:nth-child(3),
        tbody td:nth-child(4) {
            text-align: right;
        }
        .item-description {
            font-weight: 500;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals-row.grand-total {
            border-bottom: none;
            border-top: 2px solid #059669;
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            padding-top: 12px;
        }
        .totals-label {
            color: #666;
        }
        .totals-value {
            font-weight: 500;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft { background: #f3f4f6; color: #6b7280; }
        .status-sent { background: #dbeafe; color: #1d4ed8; }
        .status-accepted { background: #d1fae5; color: #059669; }
        .status-rejected { background: #fee2e2; color: #dc2626; }
        .status-expired { background: #fef3c7; color: #d97706; }
        .notes {
            margin-top: 40px;
            padding: 20px;
            background: #f0fdf4;
            border-radius: 8px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #059669;
        }
        .terms {
            margin-top: 20px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .terms-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #666;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            color: #999;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .validity-info {
            margin-top: 30px;
            padding: 15px;
            background: #fef3c7;
            border-radius: 8px;
            border-left: 4px solid #d97706;
        }
        .validity-info-title {
            font-weight: bold;
            color: #d97706;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @php
        $currency = $quote->tenant->currency ?? 'EUR';
        $currencySymbols = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'CHF' => 'CHF',
            'CAD' => 'CAD',
            'MAD' => 'DH',
            'TND' => 'DT',
            'XOF' => 'FCFA',
        ];
        $symbol = $currencySymbols[$currency] ?? $currency;
    @endphp
    <div class="header">
        <div class="company-info">
            @if($quote->tenant->logo)
                <img src="{{ public_path('storage/' . $quote->tenant->logo) }}" alt="Logo" style="max-width: 120px; max-height: 60px; margin-bottom: 10px;">
            @endif
            <div class="company-name">{{ $quote->tenant->name ?? 'Business Suite' }}</div>
            <div class="company-details">
                @if($quote->tenant->address ?? false)
                    {{ $quote->tenant->address }}<br>
                @endif
                @php
                    $settings = $quote->tenant->settings ?? [];
                @endphp
                @if(($settings['postal_code'] ?? '') || ($settings['city'] ?? ''))
                    {{ $settings['postal_code'] ?? '' }} {{ $settings['city'] ?? '' }}<br>
                @endif
                @if($settings['country'] ?? false)
                    @php
                        $countries = ['FR' => 'France', 'BE' => 'Belgique', 'CH' => 'Suisse', 'LU' => 'Luxembourg', 'CA' => 'Canada', 'MA' => 'Maroc', 'TN' => 'Tunisie', 'SN' => 'Sénégal', 'CI' => "Côte d'Ivoire"];
                    @endphp
                    {{ $countries[$settings['country']] ?? $settings['country'] }}<br>
                @endif
                @if($quote->tenant->phone ?? false)
                    Tél : {{ $quote->tenant->phone }}<br>
                @endif
                @if($quote->tenant->email ?? false)
                    {{ $quote->tenant->email }}<br>
                @endif
                @if($settings['tax_number'] ?? false)
                    N° TVA : {{ $settings['tax_number'] }}<br>
                @endif
                @if($settings['registration_number'] ?? false)
                    SIRET : {{ $settings['registration_number'] }}
                @endif
            </div>
        </div>
        <div class="quote-info">
            <div class="quote-title">DEVIS</div>
            <div class="quote-number">{{ $quote->quote_number }}</div>
            <div class="quote-meta">
                <p><strong>Date :</strong> {{ $quote->quote_date->format('d/m/Y') }}</p>
                <p><strong>Validité :</strong> {{ $quote->valid_until->format('d/m/Y') }}</p>
                <p>
                    <span class="status-badge status-{{ $quote->status }}">
                        @switch($quote->status)
                            @case('draft') Brouillon @break
                            @case('sent') Envoyé @break
                            @case('accepted') Accepté @break
                            @case('rejected') Refusé @break
                            @case('expired') Expiré @break
                            @default {{ $quote->status }}
                        @endswitch
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="addresses">
        <div class="address-block">
            <div class="address-label">Destinataire</div>
            <div class="address-name">{{ $quote->customer->display_name }}</div>
            <div class="address-details">
                @if($quote->customer->billing_address)
                    {{ $quote->customer->billing_address }}<br>
                @endif
                @if($quote->customer->billing_postal_code || $quote->customer->billing_city)
                    {{ $quote->customer->billing_postal_code }} {{ $quote->customer->billing_city }}<br>
                @endif
                @if($quote->customer->billing_country)
                    {{ $quote->customer->billing_country }}<br>
                @endif
                @if($quote->customer->email)
                    {{ $quote->customer->email }}
                @endif
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%">Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>TVA</th>
                <th>Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
                <tr>
                    <td>
                        <div class="item-description">{{ $item->description }}</div>
                        @if($item->product)
                            <div style="color: #999; font-size: 10px;">Réf: {{ $item->product->sku }}</div>
                        @endif
                    </td>
                    <td>{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                    <td>{{ number_format($item->unit_price, 2, ',', ' ') }} {{ $symbol }}</td>
                    <td>{{ $item->tax_rate }}%</td>
                    <td>{{ number_format($item->subtotal, 2, ',', ' ') }} {{ $symbol }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span class="totals-label">Sous-total HT</span>
            <span class="totals-value">{{ number_format($quote->subtotal, 2, ',', ' ') }} {{ $symbol }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">TVA</span>
            <span class="totals-value">{{ number_format($quote->tax_amount, 2, ',', ' ') }} {{ $symbol }}</span>
        </div>
        @if($quote->discount_amount > 0)
            <div class="totals-row">
                <span class="totals-label">Remise</span>
                <span class="totals-value">-{{ number_format($quote->discount_amount, 2, ',', ' ') }} {{ $symbol }}</span>
            </div>
        @endif
        <div class="totals-row grand-total">
            <span>Total TTC</span>
            <span>{{ number_format($quote->total, 2, ',', ' ') }} {{ $symbol }}</span>
        </div>
    </div>

    @if($quote->status === 'sent' && $quote->valid_until->isFuture())
        <div class="validity-info">
            <div class="validity-info-title">Validité du devis</div>
            <p>Ce devis est valable jusqu'au {{ $quote->valid_until->format('d/m/Y') }}. Passé cette date, les prix et conditions peuvent être modifiés.</p>
        </div>
    @endif

    @if($quote->notes)
        <div class="notes">
            <div class="notes-title">Notes</div>
            <p>{{ $quote->notes }}</p>
        </div>
    @endif

    @if($quote->terms)
        <div class="terms">
            <div class="terms-title">Conditions</div>
            <p>{{ $quote->terms }}</p>
        </div>
    @endif

    <div class="footer">
        <p>{{ $quote->tenant->name ?? 'Business Suite' }} - Devis généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
