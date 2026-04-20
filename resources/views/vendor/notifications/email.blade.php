{{-- resources/views/vendor/notifications/email.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? config('app.name') }}</title>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    background: linear-gradient(135deg, #eef2f7 0%, #e2e8f0 100%);
    font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    -webkit-font-smoothing: antialiased;
    padding: 2rem 1rem;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .wrapper {
    max-width: 620px;
    width: 100%;
    margin: 0 auto;
    border-radius: 28px;
    box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.25);
    transition: all 0.2s ease;
  }

  /* ========== HEADER ========== */
  .header {
    background: linear-gradient(135deg, #0f3b5f 0%, #1a5fa8 50%, #0e7a5e 100%);
    border-radius: 24px 24px 0 0;
    padding: 2.5rem 2rem 2rem;
    text-align: center;
    position: relative;
  }

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

  /* ----- TITRE CENTRÉ ----- */
  .header h1 {
    color: rgba(255, 255, 255, 0.92);
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
    text-transform: uppercase;
    text-align: center;
    width: 100%;
    display: block;
  }

  /* ========== BODY ========== */
  .body {
    background: #ffffff;
    border-left: 1px solid rgba(0, 0, 0, 0.05);
    border-right: 1px solid rgba(0, 0, 0, 0.05);
    padding: 2.5rem 2.2rem;
    text-align: center;
  }

  .greeting {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0a2c3e;
    margin-bottom: 1rem;
    letter-spacing: -0.3px;
    text-align: center;
  }

  .intro-line {
    font-size: 0.95rem;
    color: #334155;
    line-height: 1.6;
    margin-bottom: 0.8rem;
    max-width: 90%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
  }

  /* Bouton principal centré avec design premium */
  .btn-wrap {
    text-align: center;
    margin: 2rem 0 1.8rem;
  }

  .btn {
    display: inline-block;
    background: linear-gradient(105deg, #1a5fa8, #0f4f8a);
    color: #ffffff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 700;
    padding: 0.85rem 2.2rem;
    border-radius: 60px;
    box-shadow: 0 8px 18px rgba(26, 95, 168, 0.3);
    transition: all 0.25s ease;
    letter-spacing: 0.3px;
  }

  .btn:hover {
    transform: translateY(-2px);
    background: linear-gradient(105deg, #0f4f8a, #0c3d69);
    box-shadow: 0 12px 22px rgba(26, 95, 168, 0.4);
  }

  .divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e2e8f0, #cbd5e1, #e2e8f0, transparent);
    margin: 1.5rem 0 1.2rem;
  }

  .extra {
    font-size: 0.85rem;
    color: #475569;
    line-height: 1.6;
    margin-top: 0.6rem;
    text-align: center;
  }

  /* ========== SUBCOPY (lien alternatif) ========== */
  .subcopy {
    background: #fafcff;
    border: 1px solid #eef2f8;
    border-top: none;
    padding: 1.2rem 2rem;
    font-size: 0.7rem;
    color: #6b8aae;
    line-height: 1.7;
    text-align: center;
    word-break: break-all;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
  }

  .subcopy a {
    color: #1a5fa8;
    text-decoration: none;
    font-weight: 600;
    border-bottom: 1px dotted rgba(26, 95, 168, 0.3);
  }

  /* ========== FOOTER ========== */
  .footer {
    background: #f9fbfd;
    border: 1px solid #eef2f8;
    border-top: none;
    border-radius: 0 0 24px 24px;
    padding: 1.4rem 2rem;
    text-align: center;
    font-size: 0.7rem;
    color: #7c9bc0;
    line-height: 1.8;
  }

  .footer a {
    color: #1a5fa8;
    text-decoration: none;
    font-weight: 600;
    transition: 0.2s;
    border-bottom: 1px dotted rgba(26, 95, 168, 0.2);
  }

  .footer a:hover {
    color: #0c4879;
    border-bottom-color: #0c4879;
  }

  /* Responsive : toujours centré et lisible */
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
    .greeting {
      font-size: 1.3rem;
    }
    .intro-line {
      max-width: 100%;
    }
    .ofppt-badge .brand {
      font-size: 1.5rem;
      letter-spacing: 3px;
    }
    .btn {
      padding: 0.75rem 1.8rem;
      font-size: 0.85rem;
    }
    .header h1 {
      font-size: 0.8rem;
    }
  }
</style>
</head>
<body>
<div class="wrapper">

  {{-- HEADER centré, badge moderne --}}
  <div class="header">
    <div class="ofppt-badge">
      <span class="brand">OFPPT</span>
      <span class="tagline">La voie de l'avenir</span>
    </div>
    <h1>Plateforme de gestion de formation</h1>
  </div>

  {{-- BODY principal : tout centré, avec gestion dynamique des contenus --}}
  <div class="body">
    @if (! empty($greeting))
      <p class="greeting">{{ $greeting }}</p>
    @else
      <p class="greeting">Bonjour,</p>
    @endif

    @foreach ($introLines as $line)
      <p class="intro-line">{{ $line }}</p>
    @endforeach

    @isset($actionText)
      <div class="btn-wrap">
        <a href="{{ $actionUrl }}" class="btn">{{ $actionText }}</a>
      </div>
    @endisset

    @foreach ($outroLines as $line)
      <p class="extra">{{ $line }}</p>
    @endforeach

    <div class="divider"></div>
    <p class="extra">
      {{ $salutation ?? 'Cordialement,' }}<br>
      <strong style="color:#1e293b; font-weight:700;">L'équipe OFPPT</strong>
    </p>
  </div>

  {{-- Lien alternatif si besoin (subcopy centré) --}}
  @isset($actionText)
  <div class="subcopy">
    🔗 Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
    <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
  </div>
  @endisset

  {{-- Footer centré, liens d'aide --}}
  <div class="footer">
    📧 Cet email a été envoyé automatiquement — merci de ne pas y répondre.<br>
    <a href="{{ url('/login') }}">Se connecter</a> &nbsp;·&nbsp;
    <a href="mailto:admin@ofppt.ma">Contacter l'administration</a>
  </div>

</div>
</body>
</html>