<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absence enregistrée</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI', Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                
                <tr>
                    <td style="background:#1e293b; border-radius:16px 16px 0 0; padding:32px 36px; text-align:center;">
                        <div style="display:inline-block; background:#fef3c7; border-radius:50%; width:56px; height:56px; line-height:56px; font-size:28px; text-align:center; margin-bottom:16px;">⚠️</div>
                        <h1 style="margin:0; color:#fbbf24; font-size:22px; font-weight:800; letter-spacing:-0.5px;">Absence enregistrée</h1>
                        <p style="margin:8px 0 0; color:#94a3b8; font-size:13px;">Notification automatique du système de gestion</p>
                    </td>
                </tr>

                
                <tr>
                    <td style="background:#ffffff; padding:32px 36px;">

                        
                        <p style="margin:0 0 20px; color:#1e293b; font-size:15px; line-height:1.6;">
                            Bonjour <strong><?php echo e($stagiaire->name); ?></strong>,
                        </p>
                        <p style="margin:0 0 24px; color:#475569; font-size:14px; line-height:1.7;">
                            Une absence a été enregistrée pour la séance suivante&nbsp;:
                        </p>

                        
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #f59e0b; border-radius:10px; margin-bottom:28px;">
                            <tr>
                                <td style="padding:20px 22px;">
                                    <p style="margin:0 0 10px; font-size:16px; font-weight:700; color:#1e293b;">
                                        <?php echo e($emploi->module?->name ?? 'Module'); ?>

                                    </p>
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:3px 0;">
                                                <span style="font-size:11px; color:#64748b;">📅&nbsp;</span>
                                                <span style="font-size:12px; color:#475569;">
                                                    <?php echo e(\Carbon\Carbon::parse($emploi->date_debut)->translatedFormat('l d M Y')); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;">
                                                <span style="font-size:11px; color:#64748b;">🕐&nbsp;</span>
                                                <span style="font-size:12px; color:#475569;">
                                                    <?php echo e(\Carbon\Carbon::parse($emploi->date_debut)->format('H:i')); ?>

                                                    →
                                                    <?php echo e(\Carbon\Carbon::parse($emploi->date_fin)->format('H:i')); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <?php if($emploi->salle): ?>
                                        <tr>
                                            <td style="padding:3px 0;">
                                                <span style="font-size:11px; color:#64748b;">📍&nbsp;</span>
                                                <span style="font-size:12px; color:#475569;"><?php echo e($emploi->salle->name); ?></span>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if($enregistreePar): ?>
                                        <tr>
                                            <td style="padding:3px 0;">
                                                <span style="font-size:11px; color:#64748b;">👤&nbsp;</span>
                                                <span style="font-size:12px; color:#475569;">Enregistrée par <strong><?php echo e($enregistreePar->name); ?></strong></span>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        
                        <?php if($justified && $justification): ?>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 4px; font-size:12px; font-weight:700; color:#16a34a;">✅ Absence justifiée</p>
                                    <p style="margin:0; font-size:13px; color:#15803d; line-height:1.6;"><?php echo e($justification); ?></p>
                                </td>
                            </tr>
                        </table>
                        <?php else: ?>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 4px; font-size:12px; font-weight:700; color:#c2410c;">⏳ En attente de justification</p>
                                    <p style="margin:0; font-size:13px; color:#9a3412; line-height:1.6;">
                                        Si cette absence est justifiée, veuillez contacter votre formateur ou l'administration dans les meilleurs délais.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <?php endif; ?>

                        
                        <p style="margin:0 0 14px; font-size:13px; font-weight:700; color:#1e293b; letter-spacing:0.5px; text-transform:uppercase;">Votre bilan de présence</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td width="32%" style="padding-right:6px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px;">
                                        <tr>
                                            <td style="padding:16px; text-align:center;">
                                                <div style="font-size:26px; font-weight:800; color:#be123c;"><?php echo e($totalAbsences); ?></div>
                                                <div style="font-size:10px; color:#9f1239; margin-top:3px; font-weight:600;">Absences</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="32%" style="padding:0 3px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px;">
                                        <tr>
                                            <td style="padding:16px; text-align:center;">
                                                <div style="font-size:26px; font-weight:800; color:#c2410c;"><?php echo e($totalHeuresAbsence); ?>h</div>
                                                <div style="font-size:10px; color:#9a3412; margin-top:3px; font-weight:600;">Heures manquées</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="32%" style="padding-left:6px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="background:<?php echo e($tauxPresence >= 75 ? '#f0fdf4' : '#fff1f2'); ?>; border:1px solid <?php echo e($tauxPresence >= 75 ? '#bbf7d0' : '#fecdd3'); ?>; border-radius:10px;">
                                        <tr>
                                            <td style="padding:16px; text-align:center;">
                                                <div style="font-size:26px; font-weight:800; color:<?php echo e($tauxPresence >= 75 ? '#16a34a' : '#be123c'); ?>;"><?php echo e($tauxPresence); ?>%</div>
                                                <div style="font-size:10px; color:<?php echo e($tauxPresence >= 75 ? '#15803d' : '#9f1239'); ?>; margin-top:3px; font-weight:600;">Taux de présence</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        
                        <?php if($tauxPresence < 75): ?>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0; font-size:13px; color:#be123c; line-height:1.6;">
                                        🚨 <strong>Attention :</strong> Votre taux de présence est inférieur à 75%. Veuillez régulariser votre situation auprès de l'administration.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <?php endif; ?>

                        <p style="margin:0; color:#94a3b8; font-size:12px; line-height:1.7;">
                            Ce message est envoyé automatiquement. Pour toute question, contactez directement votre formateur ou l'administration de l'établissement.
                        </p>
                    </td>
                </tr>

                
                <tr>
                    <td style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:0 0 16px 16px; padding:20px 36px; text-align:center;">
                        <p style="margin:0; color:#94a3b8; font-size:11px;">
                            © <?php echo e(date('Y')); ?> Système de gestion — Ne pas répondre à cet e-mail
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html><?php /**PATH C:\Project\gestion-CF\resources\views/emails/absence.blade.php ENDPATH**/ ?>