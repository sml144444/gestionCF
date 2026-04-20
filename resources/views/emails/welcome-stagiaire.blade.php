<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenue sur OFPPT</title>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    background: linear-gradient(145deg, #eef2f7 0%, #e2e8f0 100%);
    font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    padding: 2rem 1rem;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .wrapper {
    max-width: 640px;
    width: 100%;
    margin: 0 auto;
    border-radius: 28px;
    box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.28);
    transition: all 0.2s ease;
  }

  /* ── HEADER (style email.blade.php sans cercle) ── */
  .header {
    background: linear-gradient(135deg, #0f3b5f 0%, #1a5fa8 55%, #0e7a5e 100%);
    border-radius: 24px 24px 0 0;
    padding: 2.4rem 2rem 2rem;
    text-align: center;
  }

  /* Badge OFPPT style email.blade.php */
  .ofppt-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(2px);
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    border-radius: 60px;
    padding: 0.7rem 1.8rem;
    margin-bottom: 1.2rem;
    transition: transform 0.2s;
  }

  .ofppt-badge .brand {
    font-size: 1.8rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 4px;
    display: block;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  .ofppt-badge .tagline {
    font-size: 0.65rem;
    color: rgba(255, 255, 255, 0.8);
    letter-spacing: 3px;
    text-transform: uppercase;
    display: block;
    margin-top: 4px;
    font-weight: 500;
  }

  .header h1 {
    color: #ffffff;
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    letter-spacing: -0.2px;
    text-align: center;
  }

  .header p {
    color: rgba(255, 255, 255, 0.85);
    font-size: 0.85rem;
    font-weight: 500;
    text-align: center;
  }

  /* ── BODY ── */
  .body {
    background: #ffffff;
    padding: 2.2rem 2rem;
    border-left: 1px solid rgba(0, 0, 0, 0.04);
    border-right: 1px solid rgba(0, 0, 0, 0.04);
    text-align: center;
  }

  .badge {
    display: inline-block;
    background: #e6f9ef;
    border: 1px solid #b3e4c7;
    color: #0f6e3f;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.3rem 1.2rem;
    border-radius: 60px;
    margin-bottom: 1.2rem;
    letter-spacing: 0.3px;
  }

  .body h2 {
    font-size: 1.7rem;
    font-weight: 800;
    color: #0a2c3e;
    margin-bottom: 0.75rem;
    letter-spacing: -0.3px;
    text-align: center;
  }

  .body .sub {
    font-size: 0.95rem;
    color: #2c4c6e;
    margin-bottom: 1.8rem;
    line-height: 1.55;
    max-width: 90%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
  }

  /* Info card - design épuré mais centré dans le flux */
  .info-card {
    background: #F9FCFE;
    border: 1px solid #e9edf2;
    border-radius: 1.5rem;
    padding: 1.4rem 1.8rem;
    margin-bottom: 2rem;
    text-align: left;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
  }

  .info-card .row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 0.7rem 0;
    border-bottom: 1px solid #ecf3f9;
    flex-wrap: wrap;
  }

  .info-card .row:last-child {
    border-bottom: none;
  }

  .info-card .label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #5f7f9e;
  }

  .info-card .value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1c3a53;
    background: #ffffff;
    padding: 0.1rem 0.5rem;
    border-radius: 30px;
    word-break: break-word;
    text-align: right;
  }

  /* CTA button (centré & large) */
  .btn {
    display: block;
    width: 100%;
    text-align: center;
    background: linear-gradient(95deg, #15607A, #0F7B62);
    color: #ffffff;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 0.85rem 1rem;
    border-radius: 60px;
    margin-bottom: 1.5rem;
    transition: all 0.25s ease;
    box-shadow: 0 8px 18px rgba(21, 96, 122, 0.25);
    letter-spacing: 0.3px;
  }

  .btn:hover {
    background: linear-gradient(95deg, #0e4b62, #0c644f);
    transform: translateY(-2px);
    box-shadow: 0 12px 22px -6px rgba(0, 80, 70, 0.35);
  }

  /* Tip / conseil (centré dans le texte) */
  .tip {
    background: #eef6ff;
    border-left: 4px solid #1a8c6d;
    border-radius: 1rem;
    padding: 1rem 1.4rem;
    font-size: 0.85rem;
    color: #1f5e68;
    text-align: left;
    line-height: 1.5;
    font-weight: 500;
  }

  /* ── FOOTER (centré) ── */
  .footer {
    background: #f9fbfd;
    border: 1px solid #eef2f8;
    border-top: none;
    border-radius: 0 0 24px 24px;
    padding: 1.4rem 2rem;
    text-align: center;
  }

  .footer p {
    font-size: 0.7rem;
    color: #6e90b2;
    line-height: 1.7;
  }

  .footer a {
    color: #1a6b5e;
    text-decoration: none;
    font-weight: 600;
    transition: 0.2s;
    border-bottom: 1px dotted rgba(26, 107, 94, 0.3);
  }

  .footer a:hover {
    color: #0e4e44;
    border-bottom-color: #0e4e44;
  }

  /* Responsive: toujours centré */
  @media (max-width: 560px) {
    body {
      padding: 1rem;
    }
    .body {
      padding: 1.8rem 1.2rem;
    }
    .header {
      padding: 1.8rem 1rem;
    }
    .header h1 {
      font-size: 1.3rem;
    }
    .body h2 {
      font-size: 1.5rem;
    }
    .body .sub {
      max-width: 100%;
    }
    .info-card .row {
      flex-direction: column;
      align-items: flex-start;
      gap: 5px;
    }
    .info-card .value {
      text-align: left;
      width: 100%;
    }
    .tip {
      text-align: center;
    }
    .ofppt-badge .brand {
      font-size: 1.5rem;
      letter-spacing: 3px;
    }
  }
</style>
</head>
<body>
<div class="wrapper">

  {{-- Header sans cercle, avec badge OFPPT comme email.blade.php --}}
  <div class="header">
    <div class="ofppt-badge">
      <span class="brand">OFPPT</span>
      <span class="tagline">La voie de l'avenir</span>
    </div>
    <h1>OFPPT – Plateforme de formation</h1>
    <p>La voie de l'avenir</p>
  </div>

  {{-- Body --}}
  <div class="body">
    <span class="badge">✅ Compte créé avec succès</span>
    <h2>Bienvenue, {{ $user->name }} !</h2>
    <p class="sub">
      Votre compte stagiaire a été créé avec succès sur la plateforme OFPPT.<br>
      Vous pouvez dès maintenant accéder à votre espace personnel : emplois du temps,
      ressources de cours, présences et bien plus.
    </p>

    {{-- Info card --}}
    <div class="info-card">
      <div class="row">
        <span class="label">📧 Email</span>
        <span class="value">{{ $user->email }}</span>
      </div>
      <div class="row">
        <span class="label">👤 Rôle</span>
        <span class="value">Stagiaire</span>
      </div>
      @if($user->groupe)
      <div class="row">
        <span class="label">👥 Groupe</span>
        <span class="value">{{ $user->groupe->name ?? '—' }}</span>
      </div>
      @endif
      @if($user->filiere)
      <div class="row">
        <span class="label">📚 Filière</span>
        <span class="value">{{ $user->filiere->name ?? '—' }}</span>
      </div>
      @endif
    </div>

    {{-- CTA --}}
    <a href="{{ url('/stagiaire/dashboard') }}" class="btn">
      Accéder à mon espace →
    </a>

    <div class="tip">
      💡 <strong>Conseil de sécurité</strong> — Conservez vos identifiants EDU en lieu sûr. Si vous avez oublié votre mot de passe,
      utilisez le lien <strong>« Mot de passe oublié »</strong> sur la page de connexion.
    </div>
  </div>

  {{-- Footer --}}
  <div class="footer">
    <p>
      📩 Cet email a été envoyé automatiquement par la plateforme OFPPT.<br>
      Si vous n'êtes pas à l'origine de cette inscription, contactez l'administration.<br>
      <a href="{{ url('/login') }}">Se connecter</a> &nbsp;·&nbsp;
      <a href="mailto:admin@ofppt.ma">Contacter l'administration</a>
    </p>
  </div>

</div>
</body>
</html>