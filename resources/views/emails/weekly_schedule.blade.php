
{{-- resources/views/emails/weekly_schedule.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du temps disponible</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:'Segoe UI', Arial, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            <table role="presentation" width="100%" style="max-width:560px; background:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                {{-- Header --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%); padding:36px 32px 28px; text-align:center;">
                        <div style="font-size:40px; margin-bottom:12px;">📅</div>
                        <h1 style="margin:0 0 6px; color:#ffffff; font-size:22px; font-weight:800;">
                            Emploi du temps disponible
                        </h1>
                        <p style="margin:0; color:rgba(255,255,255,0.80); font-size:14px;">
                            Semaine du <strong>{{ $weekStart->translatedFormat('d M Y') }}</strong>
                            au <strong>{{ $weekStart->copy()->addDays(5)->translatedFormat('d M Y') }}</strong>
                        </p>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:32px;">

                        <p style="margin:0 0 20px; font-size:15px; color:#1e293b; font-weight:600;">
                            Bonjour {{ $stagiaire->name ?? 'Stagiaire' }} 👋
                        </p>

                        <p style="margin:0 0 20px; font-size:14px; color:#475569; line-height:1.7;">
                            Votre emploi du temps pour la semaine prochaine
                            (<strong style="color:#1e293b;">
                                {{ $weekStart->translatedFormat('d M') }} –
                                {{ $weekStart->copy()->addDays(5)->translatedFormat('d M Y') }}
                            </strong>)
                            vient d'être publié. Vous pouvez le consulter dès maintenant.
                        </p>

                        {{-- Stats pill --}}
                        <table role="presentation" width="100%" style="margin-bottom:24px;">
                            <tr>
                                <td align="center">
                                    <div style="display:inline-block; padding:10px 24px; border-radius:99px;
                                                background:#eff6ff; border:1.5px solid #bfdbfe;
                                                font-size:13px; color:#1e40af; font-weight:700;">
                                        {{ $sessionCount }} séance{{ $sessionCount > 1 ? 's' : '' }} planifiée{{ $sessionCount > 1 ? 's' : '' }}
                                        &nbsp;·&nbsp; Groupe : {{ $stagiaire->groupe->name ?? '—' }}
                                    </div>
                                </td>
                            </tr>
                        </table>

                        {{-- Module list --}}
                        @if($subjects->isNotEmpty())
                        <table role="presentation" width="100%" style="margin-bottom:24px; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0;">
                            <tr>
                                <td style="padding:12px 16px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                    <span style="font-size:10px; font-weight:800; color:#64748b; letter-spacing:1.5px; text-transform:uppercase;">
                                        Modules de la semaine
                                    </span>
                                </td>
                            </tr>
                            @foreach($subjects as $subject)
                            <tr>
                                <td style="padding:10px 16px; border-bottom:{{ $loop->last ? 'none' : '1px solid #f1f5f9' }}; font-size:13px; color:#334155;">
                                    <span style="display:inline-block; width:7px; height:7px; border-radius:50%;
                                                 background:#2563eb; margin-right:8px; vertical-align:middle;"></span>
                                    {{ $subject }}
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @endif

                        {{-- CTA Button --}}
                        <table role="presentation" width="100%" style="margin-bottom:28px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ url(route('stagiaire.dashboard')) }}"
                                       style="display:inline-block; padding:14px 36px;
                                              background:linear-gradient(135deg,#1a4f8a,#2563eb);
                                              color:#ffffff; font-size:14px; font-weight:700;
                                              text-decoration:none; border-radius:12px;
                                              box-shadow:0 4px 16px rgba(37,99,235,0.35);">
                                        Voir mon emploi du temps →
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Info box --}}
                        <table role="presentation" width="100%" style="border-radius:12px; background:#fffbeb; border:1px solid #fde68a;">
                            <tr>
                                <td style="padding:14px 16px; font-size:12px; color:#92400e; line-height:1.6;">
                                    💡 <strong>Bon à savoir :</strong> L'emploi du temps peut évoluer.
                                    Consultez-le régulièrement pour rester informé des éventuels changements.
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:20px 32px; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
                        <p style="margin:0 0 4px; font-size:11px; color:#94a3b8;">
                            Cet email a été envoyé automatiquement — merci de ne pas y répondre.
                        </p>
                        <p style="margin:0; font-size:11px; color:#cbd5e1;">
                            © {{ date('Y') }} — Système de gestion de formation
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>