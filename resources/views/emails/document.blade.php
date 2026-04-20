<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau document partagé</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI', Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                {{-- Header --}}
                <tr>
                    <td style="background:#1e3a5f; border-radius:16px 16px 0 0; padding:32px 36px; text-align:center;">
                        <div style="display:inline-block; background:#eff6ff; border-radius:50%; width:56px; height:56px; line-height:56px; font-size:28px; text-align:center; margin-bottom:16px;">📄</div>
                        <h1 style="margin:0; color:#93c5fd; font-size:22px; font-weight:800; letter-spacing:-0.5px;">Nouveau document disponible</h1>
                        <p style="margin:8px 0 0; color:#94a3b8; font-size:13px;">Un formateur vient de partager une ressource avec votre groupe</p>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="background:#ffffff; padding:32px 36px;">

                        {{-- Greeting --}}
                        <p style="margin:0 0 20px; color:#1e293b; font-size:15px; line-height:1.6;">
                            Bonjour <strong>{{ $recipient->name }}</strong>,
                        </p>
                        <p style="margin:0 0 24px; color:#475569; font-size:14px; line-height:1.7;">
                            <strong>{{ $sharedBy->name }}</strong> a partagé un nouveau document dans le cadre de la séance suivante&nbsp;:
                        </p>

                        {{-- Session context --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #2563eb; border-radius:10px; margin-bottom:20px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 8px; font-size:14px; font-weight:700; color:#1e293b;">
                                        {{ $emploi->module?->name ?? 'Module' }}
                                    </p>
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:2px 0;">
                                                <span style="font-size:11px; color:#64748b;">📅&nbsp;</span>
                                                <span style="font-size:12px; color:#475569;">
                                                    {{ \Carbon\Carbon::parse($emploi->date_debut)->translatedFormat('l d M Y') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0;">
                                                <span style="font-size:11px; color:#64748b;">🕐&nbsp;</span>
                                                <span style="font-size:12px; color:#475569;">
                                                    {{ \Carbon\Carbon::parse($emploi->date_debut)->format('H:i') }}
                                                    →
                                                    {{ \Carbon\Carbon::parse($emploi->date_fin)->format('H:i') }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- New document highlight --}}
                        <p style="margin:0 0 12px; font-size:13px; font-weight:700; color:#1e293b; text-transform:uppercase; letter-spacing:0.5px;">Nouveau document</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; margin-bottom:28px;">
                            <tr>
                                <td style="padding:18px 20px;">
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="vertical-align:middle;">
                                                @php
                                                    $ext  = $document->fichier ? strtolower(pathinfo($document->fichier[0] ?? '', PATHINFO_EXTENSION)) : null;
                                                    $icon = $document->lien ? '🔗' : match($ext) {
                                                        'pdf'  => '📕',
                                                        'pptx' => '📊',
                                                        'docx', 'doc' => '📝',
                                                        'xlsx', 'xls' => '📊',
                                                        'zip'  => '📦',
                                                        'jpg', 'jpeg', 'png' => '🖼️',
                                                        default => '📄',
                                                    };
                                                @endphp
                                                <span style="font-size:24px; line-height:1; margin-right:12px; vertical-align:middle;">{{ $icon }}</span>
                                            </td>
                                            <td style="vertical-align:middle; width:100%;">
                                                <div style="font-size:14px; font-weight:700; color:#1e40af;">{{ $document->titre }}</div>
                                                @if($document->description)
                                                <div style="font-size:12px; color:#3b82f6; margin-top:4px; line-height:1.5;">{{ \Illuminate\Support\Str::limit($document->description, 120) }}</div>
                                                @endif
                                                @if($document->lien)
                                                <div style="margin-top:8px;">
                                                    <a href="{{ $document->lien }}" style="font-size:11px; color:#2563eb; text-decoration:underline; font-weight:600;">🔗 Ouvrir le lien</a>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- Other recent docs --}}
                        @if(!empty($otherDocs) && count($otherDocs) > 0)
                        <p style="margin:0 0 12px; font-size:13px; font-weight:700; color:#1e293b; text-transform:uppercase; letter-spacing:0.5px;">Autres ressources disponibles</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            @foreach($otherDocs as $doc)
                            <tr>
                                <td style="padding:6px 0; border-bottom:1px solid #f1f5f9;">
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="width:28px; vertical-align:middle;">
                                                @php
                                                    $dExt  = $doc->fichier ? strtolower(pathinfo($doc->fichier[0] ?? '', PATHINFO_EXTENSION)) : null;
                                                    $dIcon = $doc->lien ? '🔗' : match($dExt) {
                                                        'pdf'  => '📕', 'pptx' => '📊',
                                                        'docx', 'doc' => '📝',
                                                        default => '📄',
                                                    };
                                                @endphp
                                                <span style="font-size:16px;">{{ $dIcon }}</span>
                                            </td>
                                            <td style="vertical-align:middle; padding-left:8px;">
                                                <span style="font-size:13px; color:#374151; font-weight:600;">{{ $doc->titre }}</span>
                                                @if($doc->created_at)
                                                <span style="font-size:10px; color:#94a3b8; margin-left:8px;">{{ \Carbon\Carbon::parse($doc->created_at)->diffForHumans() }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @endif

                        {{-- CTA info --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0; font-size:13px; color:#15803d; line-height:1.7;">
                                        💡 Connectez-vous à votre espace pour consulter et télécharger les ressources de votre cours.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0; color:#94a3b8; font-size:12px; line-height:1.7;">
                            Ce message est envoyé automatiquement. Pour toute question, contactez directement votre formateur.
                        </p>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:0 0 16px 16px; padding:20px 36px; text-align:center;">
                        <p style="margin:0; color:#94a3b8; font-size:11px;">
                            © {{ date('Y') }} Système de gestion — Ne pas répondre à cet e-mail
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>