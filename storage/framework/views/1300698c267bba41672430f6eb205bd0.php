
<?php $__env->startSection('title', 'News & Événements'); ?>
<?php $__env->startSection('page-title', 'News & Événements'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = Auth::user();
    $role = $user->role;
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
    ];
    $p = $palettes[$role] ?? $palettes['gestionnaire'];
?>

<style>
:root {
    --accent:    <?php echo e($p['primary']); ?>;
    --accent-md: <?php echo e($p['medium']); ?>;
    --accent-lt: <?php echo e($p['light']); ?>;
    --accent-ltr:<?php echo e($p['lighter']); ?>;
    --accent-tx: <?php echo e($p['text']); ?>;
    --accent-bd: <?php echo e($p['border']); ?>;
    --accent-sh: <?php echo e($p['shadow']); ?>;
    --accent-gr: <?php echo e($p['gradient']); ?>;
}
* { box-sizing:border-box; }

.ig-page { font-family:'Segoe UI',system-ui,sans-serif; max-width:1000px; margin:0 auto; }

/* ── TOP BAR ── */
.ig-topbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:18px 0 22px; border-bottom:1px solid #dbdbdb; margin-bottom:28px;
    flex-wrap:wrap; gap:12px;
}
.ig-topbar-left { display:flex; align-items:center; gap:14px; }
.ig-topbar-avatar {
    width:42px; height:42px; border-radius:50%; padding:2.5px;
    background:var(--accent-gr);
    display:flex; align-items:center; justify-content:center;
    font-weight:900; font-size:16px; color:white; flex-shrink:0;
}
.ig-topbar-name { font-size:16px; font-weight:800; color:#1e293b; }
.ig-topbar-sub  { font-size:11px; color:#8e8e8e; margin-top:1px; }
.ig-btn-new {
    display:inline-flex; align-items:center; gap:7px;
    background:var(--accent-gr); color:white;
    font-size:12px; font-weight:700; padding:9px 18px;
    border-radius:10px; text-decoration:none; border:none; cursor:pointer;
    transition:opacity .15s; box-shadow:0 2px 8px var(--accent-sh);
}
.ig-btn-new:hover { opacity:.88; }

/* ── FLASH ── */
.flash-ok {
    display:flex; align-items:center; gap:12px; padding:14px 18px;
    border-radius:14px; margin-bottom:20px;
    background:var(--accent-ltr); border:1px solid var(--accent-bd);
    animation:fadeIn .3s ease;
}
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* ── INSTAGRAM GRID ── */
.ig-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:3px; }
.ig-tile {
    position:relative; cursor:pointer;
    aspect-ratio:1/1; overflow:hidden; background:#fafafa;
}
.ig-tile-img, .ig-tile-placeholder {
    width:100%; height:100%; object-fit:cover; display:block;
    transition:transform .3s ease;
}
.ig-tile-placeholder {
    background:var(--accent-gr);
    display:flex; align-items:center; justify-content:center;
}
.ig-tile:hover .ig-tile-img,
.ig-tile:hover .ig-tile-placeholder { transform:scale(1.04); }
.ig-tile-overlay {
    position:absolute; inset:0;
    background:rgba(0,0,0,0); transition:background .2s;
    display:flex; align-items:center; justify-content:center; gap:24px;
}
.ig-tile:hover .ig-tile-overlay { background:rgba(0,0,0,0.42); }
.ig-tile-stat {
    display:flex; align-items:center; gap:6px;
    color:white; font-size:15px; font-weight:800;
    opacity:0; transition:opacity .2s;
}
.ig-tile:hover .ig-tile-stat { opacity:1; }

/* ── MODAL BACKDROP ── */
.ig-modal-bg {
    display:none; position:fixed; inset:0; z-index:100;
    background:rgba(0,0,0,0.78); backdrop-filter:blur(2px);
    align-items:center; justify-content:center; padding:16px;
}
.ig-modal-bg.open { display:flex; animation:fadeIn .18s ease; }

/* ── MODAL BOX ── */
.ig-modal {
    background:white; border-radius:4px;
    width:100%; max-width:960px; max-height:92vh;
    display:flex; overflow:hidden;
    animation:modalIn .2s ease;
}
@keyframes modalIn { from{opacity:0;transform:scale(.96)} to{opacity:1;transform:scale(1)} }

/* image side */
.ig-modal-img { flex:0 0 57%; background:#000; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.ig-modal-img img { width:100%; height:100%; object-fit:contain; display:block; max-height:92vh; }
.ig-modal-img-ph {
    width:100%; min-height:420px; background:var(--accent-gr);
    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px;
}

/* right side */
.ig-modal-right { flex:1; display:flex; flex-direction:column; border-left:1px solid #dbdbdb; min-width:0; overflow:hidden; }

.ig-modal-head {
    padding:14px 16px; border-bottom:1px solid #efefef;
    display:flex; align-items:center; gap:10px; flex-shrink:0;
}
.ig-modal-ava {
    width:34px; height:34px; border-radius:50%; background:var(--accent-gr);
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:800; color:white; flex-shrink:0;
}
.ig-modal-author { font-size:13px; font-weight:700; color:#1e293b; }
.ig-modal-time   { font-size:11px; color:#8e8e8e; }
.ig-modal-close  {
    margin-left:auto; width:28px; height:28px; border-radius:50%;
    border:none; background:#f1f5f9; cursor:pointer; font-size:18px;
    line-height:1; color:#64748b; transition:background .15s;
    display:flex; align-items:center; justify-content:center;
}
.ig-modal-close:hover { background:#e2e8f0; }

/* caption */
.ig-modal-caption { padding:14px 16px; border-bottom:1px solid #efefef; flex-shrink:0; }
.ig-modal-caption h3 { font-size:14px; font-weight:800; color:#1e293b; margin:0 0 6px; }
.ig-modal-caption p  { font-size:13px; color:#374151; line-height:1.6; margin:0; white-space:pre-wrap; max-height:80px; overflow-y:auto; }

/* comments scroll */
.ig-modal-cmts { flex:1; overflow-y:auto; padding:12px 16px; }
.ig-cmt { display:flex; gap:9px; margin-bottom:14px; }
.ig-cmt-ava {
    width:30px; height:30px; border-radius:50%;
    background:linear-gradient(135deg,#64748b,#94a3b8);
    display:flex; align-items:center; justify-content:center;
    font-size:11px; font-weight:800; color:white; flex-shrink:0;
}
.ig-cmt-name { font-size:12px; font-weight:700; color:#1e293b; display:inline; margin-right:5px; }
.ig-cmt-text { font-size:12px; color:#374151; display:inline; line-height:1.5; }
.ig-cmt-foot { display:flex; align-items:center; gap:8px; margin-top:3px; }
.ig-cmt-time { font-size:10px; color:#8e8e8e; }
.ig-cmt-del  { font-size:10px; color:#dc2626; background:none; border:none; cursor:pointer; padding:0; opacity:0; transition:opacity .15s; }
.ig-cmt:hover .ig-cmt-del { opacity:1; }
.ig-no-cmt   { text-align:center; padding:32px 0; color:#8e8e8e; font-size:13px; }

/* actions */
.ig-modal-acts {
    padding:10px 16px; border-top:1px solid #efefef;
    display:flex; align-items:center; gap:10px; flex-shrink:0;
}
.ig-like-btn {
    background:none; border:none; cursor:pointer; padding:0;
    display:flex; align-items:center; gap:5px;
    color:#64748b; transition:color .15s;
}
.ig-like-btn.liked { color:#e11d48; }
.ig-like-btn.liked svg { stroke:#e11d48; fill:#fda4af; }
.ig-like-btn:not(.liked):hover svg { stroke:#e11d48; }
.ig-like-count { font-size:13px; font-weight:700; color:#1e293b; }
.ig-admin-acts { display:flex; gap:6px; margin-left:auto; }
.ig-edit-btn, .ig-del-btn {
    font-size:11px; font-weight:600; padding:6px 12px;
    border-radius:8px; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; gap:4px; border:none;
}
.ig-edit-btn { background:#f1f5f9; color:#475569; }
.ig-edit-btn:hover { background:#e2e8f0; }
.ig-del-btn  { background:#fee2e2; color:#dc2626; }
.ig-del-btn:hover  { background:#fecaca; }

/* comment input */
.ig-modal-input-row {
    padding:10px 16px; border-top:1px solid #efefef;
    display:flex; align-items:center; gap:10px; flex-shrink:0;
}
.ig-cmt-input {
    flex:1; border:none; outline:none;
    font-size:13px; font-family:inherit; color:#1e293b; background:transparent; padding:6px 0;
}
.ig-cmt-input::placeholder { color:#8e8e8e; }
.ig-cmt-send {
    background:none; border:none; cursor:pointer;
    font-size:12px; font-weight:700; color:var(--accent); padding:0;
    transition:opacity .15s;
}
.ig-cmt-send:hover { opacity:.7; }

/* ── DELETE CONFIRM ── */
.del-bg {
    display:none; position:fixed; inset:0; z-index:200;
    background:rgba(0,0,0,0.65); align-items:center; justify-content:center; padding:16px;
}
.del-bg.open { display:flex; }
.del-box { background:white; border-radius:16px; width:100%; max-width:380px; padding:24px; animation:modalIn .2s ease; }

/* ── PAGINATION ── */
.ig-pages { margin-top:28px; display:flex; justify-content:center; }

/* ── EMPTY ── */
.ig-empty { text-align:center; padding:80px 20px; background:white; border:1px solid #dbdbdb; border-radius:4px; }

/* ── RESPONSIVE ── */
@media(max-width:680px) {
    .ig-modal { flex-direction:column; }
    .ig-modal-img { flex:0 0 auto; height:46vw; min-height:180px; }
}
</style>

<div class="ig-page">


<?php if(session('success')): ?>
    <div class="flash-ok">
        <div style="width:34px;height:34px;border-radius:50%;background:var(--accent-gr);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;"><?php echo e(session('success')); ?></p>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;margin-bottom:16px;">
        <p style="font-size:12px;color:#be123c;margin:0;"><?php echo e(session('error')); ?></p>
    </div>
<?php endif; ?>


<div class="ig-topbar">
    <div class="ig-topbar-left">
        <div class="ig-topbar-avatar"><?php echo e(strtoupper(substr($user->name ?? '?', 0, 1))); ?></div>
        <div>
            <div class="ig-topbar-name">News &amp; Événements</div>
            <div class="ig-topbar-sub"><?php echo e($news->total()); ?> publication<?php echo e($news->total() > 1 ? 's' : ''); ?></div>
        </div>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('news-create')): ?>
        <a href="<?php echo e(route('news.create')); ?>" class="ig-btn-new">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Nouvelle publication
        </a>
    <?php endif; ?>
</div>


<?php if($news->isEmpty()): ?>
    <div class="ig-empty">
        <div style="width:64px;height:64px;border-radius:50%;border:3px solid #dbdbdb;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="28" height="28" fill="none" stroke="#8e8e8e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <p style="font-size:20px;font-weight:900;color:#1e293b;margin:0 0 8px;">Aucune publication</p>
        <p style="font-size:13px;color:#8e8e8e;margin:0 0 20px;">Partagez une actualité ou un événement</p>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('news-create')): ?>
            <a href="<?php echo e(route('news.create')); ?>" class="ig-btn-new" style="display:inline-flex;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Créer la première publication
            </a>
        <?php endif; ?>
    </div>
<?php else: ?>
<div class="ig-grid">
<?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $initiale = strtoupper(substr($item->auteur->name ?? '?', 0, 1));
        $isLiked  = $item->isLikedBy($user);
    ?>
    <div class="ig-tile" onclick="openPost(<?php echo e($item->id); ?>)">
        <?php if($item->image): ?>
            <img src="<?php echo e(Storage::url($item->image)); ?>" alt="<?php echo e($item->titre); ?>" class="ig-tile-img">
        <?php else: ?>
            <div class="ig-tile-placeholder">
                <svg width="36" height="36" fill="none" stroke="rgba(255,255,255,0.65)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
        <?php endif; ?>
        <div class="ig-tile-overlay">
            <span class="ig-tile-stat">
                <svg width="18" height="18" fill="white" stroke="white" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <?php echo e($item->likes->count()); ?>

            </span>
            <span class="ig-tile-stat">
                <svg width="18" height="18" fill="white" stroke="white" viewBox="0 0 24 24">
                    <path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <?php echo e($item->comments->count()); ?>

            </span>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<?php if($news->hasPages()): ?>
    <div class="ig-pages"><?php echo e($news->links()); ?></div>
<?php endif; ?>


<?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $initiale  = strtoupper(substr($item->auteur->name ?? '?', 0, 1));
    $isLiked   = $item->isLikedBy($user);
    $myInit    = strtoupper(substr($user->name ?? '?', 0, 1));
    $canEdit   = $user->can('news-edit')   && (Auth::id() === $item->id_user || $role === 'admin');
    $canDel    = $user->can('news-delete') && (Auth::id() === $item->id_user || $role === 'admin');
    $canLike   = $user->can('news-like');
    $canCmt    = $user->can('news-comment');
?>
<div id="modal-<?php echo e($item->id); ?>" class="ig-modal-bg" onclick="if(event.target===this)closePost(<?php echo e($item->id); ?>)">
    <div class="ig-modal">

        
        <div class="ig-modal-img">
            <?php if($item->image): ?>
                <img src="<?php echo e(Storage::url($item->image)); ?>" alt="<?php echo e($item->titre); ?>">
            <?php else: ?>
                <div class="ig-modal-img-ph">
                    <svg width="52" height="52" fill="none" stroke="rgba(255,255,255,0.55)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <span style="color:rgba(255,255,255,0.65);font-size:12px;font-weight:600;">Pas d'image</span>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="ig-modal-right">

            
            <div class="ig-modal-head">
                <div class="ig-modal-ava"><?php echo e($initiale); ?></div>
                <div>
                    <div class="ig-modal-author"><?php echo e($item->auteur->name ?? 'Inconnu'); ?></div>
                    <div class="ig-modal-time"><?php echo e($item->created_at->diffForHumans()); ?></div>
                </div>
                <button class="ig-modal-close" onclick="closePost(<?php echo e($item->id); ?>)">×</button>
            </div>

            
            <div class="ig-modal-caption">
                <h3><?php echo e($item->titre); ?></h3>
                <p><?php echo e($item->contenu); ?></p>
            </div>

            
            <div class="ig-modal-cmts">
                <?php $__empty_1 = true; $__currentLoopData = $item->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $cInit = strtoupper(substr($comment->auteur->name ?? '?', 0, 1)); ?>
                    <div class="ig-cmt">
                        <div class="ig-cmt-ava"><?php echo e($cInit); ?></div>
                        <div style="flex:1;">
                            <span class="ig-cmt-name"><?php echo e($comment->auteur->name ?? 'Inconnu'); ?></span>
                            <span class="ig-cmt-text"><?php echo e($comment->contenu); ?></span>
                            <div class="ig-cmt-foot">
                                <span class="ig-cmt-time"><?php echo e($comment->created_at->diffForHumans()); ?></span>
                                <?php if(Auth::id() === $comment->user_id || $user->can('news-delete')): ?>
                                    <form action="<?php echo e(route('news.comments.destroy', [$item, $comment])); ?>" method="POST" onsubmit="return confirm('Supprimer ?')" style="display:inline;">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="ig-cmt-del">Supprimer</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="ig-no-cmt">
                        <svg width="36" height="36" fill="none" stroke="#dbdbdb" viewBox="0 0 24 24" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Soyez le premier à commenter.
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="ig-modal-acts">
                <?php if($canLike): ?>
                    <button id="like-btn-<?php echo e($item->id); ?>" class="ig-like-btn <?php echo e($isLiked ? 'liked' : ''); ?>" onclick="toggleLike(<?php echo e($item->id); ?>, this)">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                <?php endif; ?>
                <span class="ig-like-count" id="like-count-<?php echo e($item->id); ?>"><?php echo e($item->likes->count()); ?> J'aime</span>
                <div class="ig-admin-acts">
                    <?php if($canEdit): ?>
                        <a href="<?php echo e(route('news.edit', $item)); ?>" class="ig-edit-btn">✎ Modifier</a>
                    <?php endif; ?>
                    <?php if($canDel): ?>
                        <button onclick="openDeleteModal('<?php echo e(route('news.destroy', $item)); ?>','<?php echo e(addslashes($item->titre)); ?>')" class="ig-del-btn">🗑️</button>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($canCmt): ?>
            <div class="ig-modal-input-row">
                <div class="ig-modal-ava" style="width:28px;height:28px;font-size:10px;flex-shrink:0;"><?php echo e($myInit); ?></div>
                <form action="<?php echo e(route('news.comments.store', $item)); ?>" method="POST" style="flex:1;display:flex;align-items:center;gap:8px;">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="contenu" class="ig-cmt-input" placeholder="Ajouter un commentaire…" required>
                    <button type="submit" class="ig-cmt-send">Publier</button>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<div id="del-modal" class="del-bg" onclick="if(event.target===this)closeDeleteModal()">
    <div class="del-box">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid #dc2626;">
            <div style="width:44px;height:44px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:800;color:#1e293b;">Supprimer la publication ?</div>
                <div style="font-size:11px;color:#64748b;" id="del-modal-title"></div>
            </div>
            <button onclick="closeDeleteModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;color:#64748b;">×</button>
        </div>
        <div style="padding:10px 14px;border-radius:10px;background:#fff1f2;border:1px solid #fecdd3;font-size:12px;color:#9f1239;margin-bottom:18px;">
            Cette action est irréversible. Les commentaires et likes seront supprimés.
        </div>
        <div style="display:flex;gap:10px;">
            <button onclick="closeDeleteModal()" style="flex:1;padding:9px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;cursor:pointer;font-size:12px;font-weight:600;color:#64748b;">Annuler</button>
            <form id="del-form" method="POST" style="flex:1;">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" style="width:100%;padding:9px;border-radius:10px;border:none;background:#fee2e2;color:#dc2626;cursor:pointer;font-size:12px;font-weight:700;">🗑️ Supprimer</button>
            </form>
        </div>
    </div>
</div>

</div>

<script>
let activeModal = null;

function openPost(id) {
    if (activeModal) closePost(activeModal);
    const m = document.getElementById('modal-' + id);
    if (!m) return;
    m.classList.add('open');
    document.body.style.overflow = 'hidden';
    activeModal = id;
}
function closePost(id) {
    const m = document.getElementById('modal-' + id);
    if (m) m.classList.remove('open');
    document.body.style.overflow = '';
    activeModal = null;
}
document.addEventListener('keydown', e => { if (e.key === 'Escape' && activeModal) closePost(activeModal); });

async function toggleLike(id, btn) {
    try {
        const res = await fetch('/news/' + id + '/like', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        btn.classList.toggle('liked', data.liked);
        const el = document.getElementById('like-count-' + id);
        if (el) el.textContent = data.count + " J'aime";
    } catch(e) { console.error(e); }
}

function openDeleteModal(action, title) {
    document.getElementById('del-form').action = action;
    document.getElementById('del-modal-title').textContent = title;
    document.getElementById('del-modal').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('del-modal').classList.remove('open');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/news/index.blade.php ENDPATH**/ ?>