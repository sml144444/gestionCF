<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vos identifiants OFPPT</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

      
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
            Vos identifiants de connexion
          </h1>
          <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.82);line-height:1.5;">
            Un compte stagiaire a été créé pour vous.<br>
            Utilisez ces identifiants pour activer votre espace.
          </p>
        </td>
      </tr>

      
      <tr>
        <td style="background:white;padding:36px 48px;">

          <p style="margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;">
            Bonjour <?php echo e($edu->prenom); ?> <?php echo e($edu->nom); ?>,
          </p>
          <p style="margin:0 0 28px;font-size:14px;color:#475569;line-height:1.7;">
            Votre compte stagiaire sur la plateforme <strong style="color:#0f3b5f;">OFPPT</strong>
            a été créé par l'administration. Voici vos identifiants pour vous connecter
            et activer votre espace personnel.
          </p>

          
          <div style="background:#f0f9ff;border-radius:12px;border:1.5px solid #bae6fd;
                      padding:16px 20px;margin-bottom:28px;">
            <div style="margin-bottom:8px;">
              <span style="font-size:11px;font-weight:700;color:#0369a1;
                           text-transform:uppercase;letter-spacing:1px;">📚 Filière : </span>
              <span style="font-size:14px;font-weight:700;color:#0c4a6e;">
                <?php echo e($edu->filiere_code); ?>

              </span>
            </div>
            <div>
              <span style="font-size:11px;font-weight:700;color:#0369a1;
                           text-transform:uppercase;letter-spacing:1px;">👥 Groupe : </span>
              <span style="font-size:14px;font-weight:700;color:#0c4a6e;">
                <?php echo e($edu->groupe_code); ?>

              </span>
            </div>
          </div>

          
          <div style="background:#f8fafc;border-radius:16px;border:1.5px solid #e2e8f0;
                      padding:28px 32px;margin-bottom:28px;">
            <div style="font-size:10px;font-weight:800;color:#94a3b8;letter-spacing:2px;
                        text-transform:uppercase;margin-bottom:20px;">
              🔐 Vos identifiants de connexion
            </div>

            
            <div style="margin-bottom:16px;">
              <div style="font-size:11px;font-weight:700;color:#64748b;
                          text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">
                Adresse e-mail
              </div>
              <div style="background:white;border:1.5px solid #e2e8f0;border-radius:10px;
                          padding:12px 16px;font-size:14px;font-weight:600;color:#0f172a;
                          font-family:'Courier New',monospace;letter-spacing:0.3px;">
                <?php echo e($edu->edu_email); ?>

              </div>
            </div>

            
            <div>
              <div style="font-size:11px;font-weight:700;color:#64748b;
                          text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">
                Mot de passe temporaire
              </div>
              <div style="background:linear-gradient(135deg,#0f3b5f,#1a5fa8);border-radius:10px;
                          padding:14px 20px;font-size:20px;font-weight:800;color:white;
                          text-align:center;font-family:'Courier New',monospace;letter-spacing:4px;">
                <?php echo e($plainPassword); ?>

              </div>
            </div>
          </div>

          
          <div style="margin-bottom:28px;">
            <div style="font-size:11px;font-weight:800;color:#94a3b8;letter-spacing:2px;
                        text-transform:uppercase;margin-bottom:14px;">
              📋 Comment activer votre compte ?
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
              <?php $__currentLoopData = [
                ['1', '#0f3b5f', 'Rendez-vous sur la page d\'inscription'],
                ['2', '#1a5fa8', 'Entrez votre email EDU et ce mot de passe temporaire'],
                ['3', '#0e7a5e', 'Votre espace personnel est immédiatement accessible'],
              ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num, $color, $text]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div style="display:flex;align-items:center;gap:14px;
                          background:#f8fafc;border-radius:10px;padding:12px 16px;">
                <div style="width:28px;height:28px;border-radius:50%;background:<?php echo e($color); ?>;
                            color:white;font-size:13px;font-weight:800;text-align:center;
                            line-height:28px;flex-shrink:0;"><?php echo e($num); ?></div>
                <span style="font-size:13px;color:#374151;font-weight:500;"><?php echo e($text); ?></span>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>

          
          <div style="text-align:center;margin-bottom:28px;">
            <a href="<?php echo e(url('/register')); ?>"
               style="display:inline-block;padding:14px 40px;border-radius:14px;
                      background:linear-gradient(135deg,#0f3b5f,#1a5fa8);
                      color:white;font-size:14px;font-weight:800;text-decoration:none;
                      box-shadow:0 6px 20px rgba(15,59,95,0.35);">
              Activer mon compte →
            </a>
          </div>

          
          <div style="background:#fffbeb;border-radius:12px;border:1px solid #fde68a;
                      padding:14px 18px;font-size:12px;color:#92400e;line-height:1.6;">
            <strong>⚠️ Important :</strong> Ce mot de passe est à usage unique pour activer
            votre compte. Vous pourrez le modifier après votre première connexion depuis
            votre profil. Ne partagez jamais vos identifiants.
          </div>

        </td>
      </tr>

      
      <tr>
        <td style="background:#f8fafc;border-radius:0 0 20px 20px;
                   border-top:1px solid #e2e8f0;padding:24px 48px;text-align:center;">
          <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;">
            Cet e-mail a été généré automatiquement — merci de ne pas y répondre.
          </p>
          <p style="margin:0;font-size:11px;color:#cbd5e1;">
            © <?php echo e(date('Y')); ?> Plateforme OFPPT — Tous droits réservés
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html><?php /**PATH C:\Project\gestion-CF\resources\views/emails/welcome-edu.blade.php ENDPATH**/ ?>