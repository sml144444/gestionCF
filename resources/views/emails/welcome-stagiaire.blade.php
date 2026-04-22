<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Compte activé</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

      {{-- HEADER --}}
      <tr>
        <td style="background:linear-gradient(135deg,#0f3b5f 0%,#1a5fa8 55%,#0e7a5e 100%);
                   border-radius:20px 20px 0 0;padding:40px 48px 36px;text-align:center;">
          <div style="display:inline-block;background:rgba(255,255,255,0.12);
                      border:1.5px solid rgba(255,255,255,0.35);border-radius:60px;
                      padding:10px 28px;margin-bottom:20px;">
            <div style="font-size:24px;font-weight:800;color:#fff;letter-spacing:4px;">OFPPT</div>
            <div style="font-size:10px;color:rgba(255,255,255,0.8);letter-spacing:3px;
                        text-transform:uppercase;margin-top:3px;">La voie de l'avenir</div>
          </div>
          <h1 style="margin:0 0 8px;font-size:24px;font-weight:800;color:white;">
            🎉 Compte activé avec succès !
          </h1>
          <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.82);line-height:1.5;">
            Votre espace personnel OFPPT est maintenant accessible.
          </p>
        </td>
      </tr>

      {{-- BODY --}}
      <tr>
        <td style="background:white;padding:36px 48px;">

          {{-- Success badge --}}
          <div style="text-align:center;margin-bottom:24px;">
            <span style="display:inline-block;padding:8px 24px;border-radius:99px;
                         background:#e6f9ef;color:#0f6e3f;font-size:13px;font-weight:700;
                         border:1.5px solid #b3e4c7;">
              ✅ Compte stagiaire activé
            </span>
          </div>

          <p style="margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;">
            Bonjour {{ $user->name }},
          </p>
          <p style="margin:0 0 28px;font-size:14px;color:#475569;line-height:1.7;">
            Votre inscription sur la plateforme <strong style="color:#0f3b5f;">OFPPT</strong>
            est confirmée. Vous avez maintenant accès à votre espace stagiaire :
            emplois du temps, ressources de cours, suivi des présences et bien plus.
          </p>

          {{-- Account info --}}
          <div style="background:#f8fafc;border-radius:16px;border:1.5px solid #e2e8f0;
                      padding:24px 28px;margin-bottom:28px;">
            <div style="font-size:10px;font-weight:800;color:#94a3b8;letter-spacing:2px;
                        text-transform:uppercase;margin-bottom:16px;">
              👤 Informations de votre compte
            </div>

            <div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f1f5f9;">
              <span style="font-size:11px;font-weight:700;color:#64748b;
                           text-transform:uppercase;letter-spacing:1px;">Nom complet</span><br>
              <span style="font-size:15px;font-weight:700;color:#0f172a;">{{ $user->name }}</span>
            </div>

            <div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f1f5f9;">
              <span style="font-size:11px;font-weight:700;color:#64748b;
                           text-transform:uppercase;letter-spacing:1px;">Adresse e-mail</span><br>
              <span style="font-size:14px;font-weight:600;color:#0f172a;
                           font-family:'Courier New',monospace;">{{ $user->email }}</span>
            </div>

            @if($user->groupe)
            <div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f1f5f9;">
              <span style="font-size:11px;font-weight:700;color:#64748b;
                           text-transform:uppercase;letter-spacing:1px;">👥 Groupe</span><br>
              <span style="font-size:14px;font-weight:700;color:#0c4a6e;">
                {{ $user->groupe->name ?? '—' }}
              </span>
            </div>
            @endif

            @if($user->filiere)
            <div>
              <span style="font-size:11px;font-weight:700;color:#64748b;
                           text-transform:uppercase;letter-spacing:1px;">📚 Filière</span><br>
              <span style="font-size:14px;font-weight:700;color:#0c4a6e;">
                {{ $user->filiere->name ?? '—' }}
              </span>
            </div>
            @endif
          </div>

          {{-- CTA --}}
          <div style="text-align:center;margin-bottom:28px;">
            <a href="{{ url('/stagiaire/dashboard') }}"
               style="display:inline-block;padding:14px 40px;border-radius:14px;
                      background:linear-gradient(135deg,#0f3b5f,#1a5fa8);
                      color:white;font-size:14px;font-weight:800;text-decoration:none;
                      box-shadow:0 6px 20px rgba(15,59,95,0.35);">
              Accéder à mon espace →
            </a>
          </div>

          {{-- Tip --}}
          <div style="background:#eef6ff;border-left:4px solid #1a8c6d;border-radius:10px;
                      padding:14px 18px;font-size:12px;color:#1f5e68;line-height:1.6;">
            💡 <strong>Conseil :</strong> Pensez à changer votre mot de passe depuis votre
            profil pour sécuriser votre compte.
          </div>

        </td>
      </tr>

      {{-- FOOTER --}}
      <tr>
        <td style="background:#f8fafc;border-radius:0 0 20px 20px;
                   border-top:1px solid #e2e8f0;padding:24px 48px;text-align:center;">
          <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;">
            Cet e-mail a été généré automatiquement — merci de ne pas y répondre.
          </p>
          <p style="margin:0;font-size:11px;color:#cbd5e1;">
            © {{ date('Y') }} Plateforme OFPPT — Tous droits réservés
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>