<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $contract->title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .contract-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .contract-number {
            font-size: 14px;
            color: #7f8c8d;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .party-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .party-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .party-details {
            font-size: 14px;
            color: #555;
        }
        .contract-content {
            text-align: justify;
            line-height: 1.8;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 30px;
        }
        .signature-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
        }
        .metadata {
            background-color: #ecf0f1;
            padding: 10px;
            border-radius: 3px;
            font-size: 12px;
            color: #555;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="contract-title">{{ $contract->title }}</div>
        <div class="contract-number">Contract #{{ $contract->id }}</div>
        <div style="font-size: 14px; color: #7f8c8d;">
            Generated on {{ $contract->generated_at->format('F j, Y') }}
        </div>
    </div>

    @if($contract->description)
    <div class="section">
        <div class="section-title">Description</div>
        <div class="contract-content">{{ $contract->description }}</div>
    </div>
    @endif

    @if($contract->parties && $contract->parties->count() > 0)
    <div class="section">
        <div class="section-title">Parties</div>
        @foreach($contract->parties as $party)
        <div class="party-info">
            <div class="party-name">{{ $party->name }} ({{ ucfirst($party->type) }})</div>
            @if($party->email)
            <div class="party-details">Email: {{ $party->email }}</div>
            @endif
            @if($party->phone)
            <div class="party-details">Phone: {{ $party->phone }}</div>
            @endif
            @if($party->address)
            <div class="party-details">
                Address: {{ $party->address }}, {{ $party->city }}, {{ $party->state }} {{ $party->zip_code }}, {{ $party->country }}
            </div>
            @endif
            @if($party->tax_id)
            <div class="party-details">Tax ID: {{ $party->tax_id }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($contract->total_value)
    <div class="section">
        <div class="section-title">Contract Value</div>
        <div class="contract-content">
            Total Value: {{ $contract->currency }} {{ number_format($contract->total_value, 2) }}
        </div>
    </div>
    @endif

    @if($contract->expires_at)
    <div class="section">
        <div class="section-title">Expiration</div>
        <div class="contract-content">
            This contract expires on {{ $contract->expires_at->format('F j, Y') }}
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Contract Terms</div>
        <div class="contract-content">
            {!! nl2br(e($contract->content)) !!}
        </div>
    </div>

    <div class="signature-section">
        <div class="section-title">Signatures</div>
        <div style="display: flex; justify-content: space-between;">
            <div style="text-align: center;">
                <div class="signature-line"></div>
                <div class="signature-label">Party Signature</div>
                <div class="signature-label">Date: _________________</div>
            </div>
            <div style="text-align: center;">
                <div class="signature-line"></div>
                <div class="signature-label">Company Representative</div>
                <div class="signature-label">Date: _________________</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This document was generated electronically by Contract Generator Pro</p>
        <p>For questions or concerns, please contact the contract administrator</p>
    </div>

    @if($contract->metadata)
    <div class="metadata">
        <strong>Additional Information:</strong><br>
        @foreach($contract->metadata as $key => $value)
            {{ ucfirst($key) }}: {{ $value }}<br>
        @endforeach
    </div>
    @endif
</body>
</html>
