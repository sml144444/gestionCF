<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenue</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f4f8; font-family:'Segoe UI', Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8; padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

        
        <tr>
          <td style="background:linear-gradient(135deg,#0a6640 0%,#0d8a56 60%,#10b366 100%);
                     border-radius:20px 20px 0 0; padding:40px 48px 36px; text-align:center;">

            
            <div style="width:64px; height:64px; border-radius:18px;
                        background:rgba(255,255,255,0.18); display:inline-flex;
                        align-items:center; justify-content:center; margin-bottom:20px;
                        font-size:28px; line-height:64px;">🎓</div>

            <h1 style="margin:0 0 8px; font-size:26px; font-weight:800; color:white;
                       letter-spacing:-0.5px;">Bienvenue sur la plateforme !</h1>

            <p style="margin:0; font-size:14px; color:rgba(255,255,255,0.80); line-height:1.5;">
              Votre compte a été créé avec succès.<br>
              Voici vos identifiants de connexion.
            </p>
          </td>
        </tr>

        
        <tr>
          <td style="background:white; padding:36px 48px;">

            
            <p style="margin:0 0 24px; font-size:16px; font-weight:700; color:#0f172a;">
              Bonjour <?php echo e($user->name); ?>,
            </p>
            <p style="margin:0 0 28px; font-size:14px; color:#475569; line-height:1.7;">
              Un administrateur vient de créer votre compte
              <strong style="color:#0a6640;"><?php echo e(ucfirst($user->role)); ?></strong>
              sur la plateforme de gestion.
              Utilisez les accès ci-dessous pour vous connecter.
            </p>

            
            <?php
              $roleColor = $user->role === 'formateur' ? '#9333ea' : '#2563eb';
              $roleBg    = $user->role === 'formateur' ? '#fdf4ff' : '#eff6ff';
              $roleIcon  = $user->role === 'formateur' ? '🎓' : '🏢';
            ?>
            <div style="text-align:center; margin-bottom:28px;">
              <span style="display:inline-block; padding:6px 20px; border-radius:99px;
                           background:<?php echo e($roleBg); ?>; color:<?php echo e($roleColor); ?>;
                           font-size:13px; font-weight:700; border:1.5px solid <?php echo e($roleColor); ?>20;">
                <?php echo e($roleIcon); ?> <?php echo e(ucfirst($user->role)); ?>

              </span>
            </div>

            
            <div style="background:#f8fafc; border-radius:16px; border:1.5px solid #e2e8f0;
                        padding:28px 32px; margin-bottom:28px;">
              <div style="font-size:10px; font-weight:800; color:#94a3b8; letter-spacing:2px;
                          text-transform:uppercase; margin-bottom:20px;">
                🔐 Vos identifiants de connexion
              </div>

              
              <div style="margin-bottom:16px;">
                <div style="font-size:11px; font-weight:700; color:#64748b;
                            text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                  Adresse e-mail
                </div>
                <div style="background:white; border:1.5px solid #e2e8f0; border-radius:10px;
                            padding:12px 16px; font-size:14px; font-weight:600; color:#0f172a;
                            font-family:'Courier New', monospace; letter-spacing:0.3px;">
                  <?php echo e($user->email); ?>

                </div>
              </div>

              
              <div>
                <div style="font-size:11px; font-weight:700; color:#64748b;
                            text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                  Mot de passe temporaire
                </div>
                <div style="background:#0a6640; border-radius:10px; padding:14px 20px;
                            font-size:18px; font-weight:800; color:white; text-align:center;
                            font-family:'Courier New', monospace; letter-spacing:3px;">
                  <?php echo e($plainPassword); ?>

                </div>
              </div>
            </div>

            
            <?php if($user->matricule_formateur): ?>
            <div style="background:#fdf4ff; border-radius:12px; border:1.5px solid #e9d5ff;
                        padding:14px 20px; margin-bottom:28px; display:flex; align-items:center; gap:12px;">
              <span style="font-size:20px;">🪪</span>
              <div>
                <div style="font-size:10px; font-weight:700; color:#9333ea;
                            text-transform:uppercase; letter-spacing:1px;">Matricule formateur</div>
                <div style="font-size:15px; font-weight:800; color:#581c87;
                            font-family:'Courier New',monospace; letter-spacing:1px;">
                  <?php echo e($user->matricule_formateur); ?>

                </div>
              </div>
            </div>
            <?php endif; ?>

            
            <div style="text-align:center; margin-bottom:28px;">
              <a href="<?php echo e(url('/login')); ?>"
                 style="display:inline-block; padding:14px 40px; border-radius:14px;
                        background:linear-gradient(135deg,#0a6640,#0d8a56);
                        color:white; font-size:14px; font-weight:800; text-decoration:none;
                        box-shadow:0 6px 20px rgba(10,102,64,0.35);">
                Se connecter →
              </a>
            </div>

            
            <div style="background:#fffbeb; border-radius:12px; border:1px solid #fde68a;
                        padding:14px 18px; font-size:12px; color:#92400e; line-height:1.6;">
              <strong>⚠️ Sécurité :</strong> Ce mot de passe est temporaire.
              Nous vous recommandons de le changer dès votre première connexion
              depuis votre profil.
            </div>

          </td>
        </tr>

        
        <tr>
          <td style="background:#f8fafc; border-radius:0 0 20px 20px;
                     border-top:1px solid #e2e8f0; padding:24px 48px; text-align:center;">
            <p style="margin:0 0 6px; font-size:12px; color:#94a3b8;">
              Cet e-mail a été généré automatiquement — merci de ne pas y répondre.
            </p>
            <p style="margin:0; font-size:11px; color:#cbd5e1;">
              © <?php echo e(date('Y')); ?> Plateforme OFPPT — Tous droits réservés
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html><?php /**PATH C:\Project\gestion-CF\resources\views/emails/welcome.blade.php ENDPATH**/ ?>