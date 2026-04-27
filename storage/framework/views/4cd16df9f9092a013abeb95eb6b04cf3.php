<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe modifié</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI', Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                
                <tr>
                    <td style="background:#1a4f8a; border-radius:16px 16px 0 0; padding:32px 36px; text-align:center;">
                        <div style="display:inline-block; background:#dbeafe; border-radius:50%;
                                    width:56px; height:56px; line-height:56px;
                                    font-size:28px; text-align:center; margin-bottom:16px;">🔐</div>
                        <h1 style="margin:0; color:#93c5fd; font-size:22px; font-weight:800; letter-spacing:-0.5px;">
                            Mot de passe modifié
                        </h1>
                        <p style="margin:8px 0 0; color:#94a3b8; font-size:13px;">
                            Notification de sécurité — Système de gestion OFPPT
                        </p>
                    </td>
                </tr>

                
                <tr>
                    <td style="background:#ffffff; padding:32px 36px;">

                        
                        <p style="margin:0 0 20px; color:#1e293b; font-size:15px; line-height:1.6;">
                            Bonjour <strong><?php echo e($user->name); ?></strong>,
                        </p>
                        <p style="margin:0 0 28px; color:#475569; font-size:14px; line-height:1.7;">
                            Le mot de passe de votre compte a été <strong>modifié avec succès</strong>.
                            Voici un récapitulatif de cette action.
                        </p>

                        
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background:#f8fafc; border:1px solid #e2e8f0;
                                      border-left:4px solid #1a4f8a; border-radius:10px; margin-bottom:28px;">
                            <tr>
                                <td style="padding:20px 22px;">
                                    <p style="margin:0 0 12px; font-size:15px; font-weight:700; color:#1e293b;">
                                        Détails de la modification
                                    </p>
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:4px 0; vertical-align:top;">
                                                <span style="font-size:12px; color:#64748b; display:inline-block; width:140px;">👤&nbsp; Compte</span>
                                                <span style="font-size:13px; color:#1e293b; font-weight:600;"><?php echo e($user->email); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; vertical-align:top;">
                                                <span style="font-size:12px; color:#64748b; display:inline-block; width:140px;">🕐&nbsp; Date &amp; heure</span>
                                                <span style="font-size:13px; color:#475569;"><?php echo e($changedAt); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; vertical-align:top;">
                                                <span style="font-size:12px; color:#64748b; display:inline-block; width:140px;">🌐&nbsp; Adresse IP</span>
                                                <span style="font-size:13px; color:#475569;"><?php echo e($ipAddress); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; vertical-align:top;">
                                                <span style="font-size:12px; color:#64748b; display:inline-block; width:140px;">🎓&nbsp; Rôle</span>
                                                <span style="font-size:13px; color:#475569;"><?php echo e(ucfirst($user->role)); ?></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background:#f0fdf4; border:1px solid #bbf7d0;
                                      border-radius:10px; margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 4px; font-size:12px; font-weight:700; color:#16a34a;">
                                        ✅ Action confirmée
                                    </p>
                                    <p style="margin:0; font-size:13px; color:#15803d; line-height:1.7;">
                                        Votre nouveau mot de passe est actif immédiatement.
                                        Vous pouvez l'utiliser dès à présent pour vous connecter à votre espace.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background:#fff7ed; border:1px solid #fed7aa;
                                      border-radius:10px; margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 6px; font-size:12px; font-weight:700; color:#c2410c;">
                                        ⚠️ Vous n'êtes pas à l'origine de ce changement ?
                                    </p>
                                    <p style="margin:0; font-size:13px; color:#9a3412; line-height:1.7;">
                                        Si vous n'avez pas effectué cette modification, votre compte est peut-être compromis.
                                        Contactez <strong>immédiatement</strong> l'administration de votre établissement.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        
                        <p style="margin:0 0 12px; font-size:13px; font-weight:700; color:#1e293b;
                                  letter-spacing:0.5px; text-transform:uppercase;">
                            Bonnes pratiques de sécurité
                        </p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="padding:5px 0; border-bottom:1px solid #f1f5f9;">
                                    <span style="font-size:13px; color:#475569; line-height:1.6;">
                                        🔒&nbsp; Ne partagez jamais votre mot de passe avec quiconque
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; border-bottom:1px solid #f1f5f9;">
                                    <span style="font-size:13px; color:#475569; line-height:1.6;">
                                        🔄&nbsp; Changez votre mot de passe régulièrement
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0;">
                                    <span style="font-size:13px; color:#475569; line-height:1.6;">
                                        📵&nbsp; Ne vous connectez pas depuis des réseaux publics non sécurisés
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0; color:#94a3b8; font-size:12px; line-height:1.7;">
                            Ce message est envoyé automatiquement suite à une modification de sécurité sur votre compte.
                            Pour toute question, contactez l'administration de votre établissement.
                        </p>
                    </td>
                </tr>

                
                <tr>
                    <td style="background:#f8fafc; border:1px solid #e2e8f0;
                               border-radius:0 0 16px 16px; padding:20px 36px; text-align:center;">
                        <p style="margin:0; color:#94a3b8; font-size:11px;">
                            © <?php echo e(date('Y')); ?> Système de gestion OFPPT — Ne pas répondre à cet e-mail
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html><?php /**PATH C:\Project\gestion-CF\resources\views/emails/password-changed.blade.php ENDPATH**/ ?>