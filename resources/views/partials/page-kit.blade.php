{{-- Shared look for inner pages (Completed / Upcoming / Priority). Include with @include('partials.page-kit') --}}
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
    .pg { --pg-line: rgba(160,170,255,.14); --pg-panel: rgba(12,11,30,.62); --pg-dim: #8d92ad;
          max-width: 1100px; margin: 0 auto; padding: 22px 16px 40px; color: #f2f3fb; }
    .pg * { box-sizing: border-box; }
    .pg a { text-decoration: none; }
    .pg [hidden] { display: none !important; }

    .pg-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 14px; margin-bottom: 18px; padding: 22px 22px 20px; position: relative; overflow: hidden;
               border: 1px solid var(--pg-line); border-radius: 26px 26px 6px 26px; background: var(--pg-panel); backdrop-filter: blur(14px); }
    .pg-head::before { content: ""; position: absolute; width: 360px; height: 360px; right: -110px; top: -170px; border-radius: 50%; border: 1px solid var(--pg-line); pointer-events: none; }
    .pg-head::after  { content: ""; position: absolute; width: 560px; height: 560px; right: -210px; top: -270px; border-radius: 50%; border: 1px solid var(--pg-line); opacity: .55; pointer-events: none; }
    .pg-title { font-family: 'Sora', 'Inter', sans-serif; font-size: clamp(26px, 5.5vw, 40px); font-weight: 800; letter-spacing: -.035em; line-height: 1.05; }
    .pg-sub { margin-top: 8px; font-size: 13.5px; color: var(--pg-dim); }
    .pg-back { position: relative; z-index: 1; display: inline-flex; align-items: center; height: 40px; padding: 0 16px; border-radius: 12px; border: 1px solid var(--pg-line); background: rgba(255,255,255,.04); color: #cfd2e0; font-size: 13.5px; font-weight: 600; transition: .2s; }
    .pg-back:hover { color: #fff; border-color: rgba(125,180,255,.5); background: rgba(59,130,246,.1); }

    .pg-flash { margin-bottom: 14px; padding: 12px 16px; border-radius: 14px; border: 1px solid rgba(74,222,128,.3); background: rgba(74,222,128,.1); color: #86efac; font-size: 13.5px; }

    .pg-panel { border: 1px solid var(--pg-line); border-radius: 20px; background: var(--pg-panel); backdrop-filter: blur(14px); padding: 8px 14px; }
    .pg-panel.pad { padding: 18px 16px; }

    .pg-tools { display: grid; grid-template-columns: 1fr; gap: 10px; margin-bottom: 12px; }
    .pg-input { width: 100%; height: 46px; padding: 0 14px; border-radius: 13px; border: 1px solid #2a2842; background: #0f0e1c; color: #f4f5fa; font: inherit; font-size: 14px; transition: border-color .2s, box-shadow .2s; }
    .pg-input::placeholder { color: #5f6479; }
    .pg-input:focus { outline: 0; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,.16); }
    .pg-count { margin: 0 4px 10px; font-size: 12.5px; color: #6f748c; }

    /* Task rows */
    .pg-row { display: grid; grid-template-columns: 48px 1fr; gap: 14px; align-items: center; padding: 14px 6px; border-top: 1px solid var(--pg-line); border-radius: 10px; transition: background .2s; }
    .pg-row:first-child { border-top: 0; }
    .pg-row:hover { background: rgba(255,255,255,.03); }
    .pg-row.has-side { grid-template-columns: 48px 1fr auto; }
    .pg-date { width: 48px; padding: 7px 0; text-align: center; border-radius: 12px; border: 1px solid var(--pg-line); background: rgba(255,255,255,.03); line-height: 1; }
    .pg-date small { display: block; font-size: 9.5px; letter-spacing: .06em; text-transform: uppercase; color: var(--pg-dim); }
    .pg-date b { display: block; margin-top: 3px; font-family: 'Sora', sans-serif; font-size: 18px; }
    .pg-date.late { border-color: rgba(251,113,133,.5); } .pg-date.late b { color: #fb7185; }
    .pg-date.now  { border-color: rgba(251,191,36,.5); }  .pg-date.now  b { color: #fbbf24; }
    .pg-date.done { border-color: rgba(74,222,128,.4); background: rgba(74,222,128,.08); color: #4ade80; display: grid; place-items: center; height: 48px; padding: 0; }
    .pg-date.done svg { width: 22px; height: 22px; }
    .pg-name { font-size: 14.5px; font-weight: 500; line-height: 1.35; word-break: break-word; }
    .pg-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 6px; font-size: 12px; color: var(--pg-dim); }
    .pg-pill { font-size: 10.5px; font-weight: 600; padding: 2px 9px; border-radius: 99px; border: 1px solid var(--pg-line); color: var(--pg-dim); }
    .pg-p-high   { color: #fb7185; border-color: rgba(251,113,133,.4); background: rgba(251,113,133,.1); }
    .pg-p-medium { color: #fbbf24; border-color: rgba(251,191,36,.4);  background: rgba(251,191,36,.1); }
    .pg-p-low    { color: #7db4ff; border-color: rgba(125,180,255,.4); background: rgba(125,180,255,.1); }
    .pg-side { text-align: right; font-size: 12px; color: var(--pg-dim); }
    .pg-side b { display: block; font-size: 13px; font-weight: 500; color: #e4e6f2; margin-bottom: 3px; }
    .pg-side .late { color: #fb7185; } .pg-side .now { color: #fbbf24; }

    /* Empty state */
    .pg-empty { text-align: center; padding: 48px 16px; }
    .pg-empty .ring { position: relative; width: 64px; height: 64px; margin: 0 auto 18px; display: grid; place-items: center; border-radius: 50%; border: 1px solid var(--pg-line); color: #a78bfa; }
    .pg-empty .ring::after { content: ""; position: absolute; inset: -9px; border-radius: 50%; border: 1px dashed rgba(167,139,250,.3); animation: pgSpin 20s linear infinite; }
    .pg-empty .ring svg { width: 28px; height: 28px; }
    .pg-empty b { display: block; font-family: 'Sora', sans-serif; font-size: 17px; }
    .pg-empty p { margin-top: 6px; font-size: 13.5px; color: var(--pg-dim); }
    .pg-cta { display: inline-flex; align-items: center; height: 42px; margin-top: 18px; padding: 0 20px; border-radius: 12px; color: #fff; font-size: 14px; font-weight: 600; background: linear-gradient(135deg, #3b82f6, #6366f1); box-shadow: 0 10px 28px rgba(79,70,229,.4); }
    @keyframes pgSpin { to { transform: rotate(360deg); } }

    /* Priority spectrum */
    .pg-spectrum { display: flex; height: 10px; border-radius: 99px; overflow: hidden; background: rgba(255,255,255,.06); gap: 3px; }
    .pg-spectrum i { display: block; height: 100%; border-radius: 99px; }
    .pg-legend { display: flex; flex-wrap: wrap; gap: 6px 18px; margin-top: 10px; font-size: 12.5px; color: var(--pg-dim); }
    .pg-legend b { color: #f2f3fb; margin-left: 4px; }
    .pg-legend span::before { content: ""; display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 7px; background: var(--c); }

    @media (min-width: 640px) { .pg-tools { grid-template-columns: 1fr 240px; } }
    @media (min-width: 900px) { .pg { padding: 34px 32px 48px; } .pg-head { padding: 30px 34px 26px; } .pg-panel { padding: 10px 20px; } .pg-row { padding: 16px 8px; } }
    @media (max-width: 560px) { .pg-row.has-side { grid-template-columns: 48px 1fr; } .pg-row.has-side .pg-side { grid-column: 2; text-align: left; } }
    @media (prefers-reduced-motion: reduce) { .pg * { animation: none !important; } }
</style>

<style>
    /* ===== Kit additions: stats, buttons, chips, cards, dialogs ===== */
    .pg .hidden { display: none !important; }
    .pg-actions { position: relative; z-index: 1; display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .pg-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 42px; padding: 0 18px; border-radius: 12px; border: 1px solid transparent; font: inherit; font-size: 13.5px; font-weight: 600; cursor: pointer; color: #fff; text-decoration: none; transition: transform .15s, background .2s, box-shadow .2s; }
    .pg-btn svg { width: 16px; height: 16px; }
    .pg-btn.main { background: linear-gradient(135deg, #3b82f6, #6366f1); box-shadow: 0 10px 28px rgba(79,70,229,.4); }
    .pg-btn.main:hover { transform: translateY(-1px); box-shadow: 0 14px 34px rgba(79,70,229,.55); }
    .pg-btn.ghost { background: rgba(255,255,255,.04); border-color: rgba(160,170,255,.2); color: #cfd2e0; }
    .pg-btn.ghost:hover { background: rgba(255,255,255,.08); color: #fff; }
    .pg-btn.danger { background: rgba(248,113,113,.1); border-color: rgba(248,113,113,.35); color: #fca5a5; }
    .pg-btn.danger:hover { background: rgba(248,113,113,.2); }
    .pg-btn:disabled { opacity: .4; cursor: not-allowed; transform: none; }
    .pg-act { display: inline-flex; align-items: center; height: 34px; padding: 0 13px; border-radius: 10px; border: 1px solid; font: inherit; font-size: 12.5px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background .2s; }
    .pg-act.edit { color: #93c5fd; border-color: rgba(147,197,253,.3); background: rgba(59,130,246,.1); } .pg-act.edit:hover { background: rgba(59,130,246,.2); }
    .pg-act.ok   { color: #86efac; border-color: rgba(74,222,128,.3); background: rgba(74,222,128,.1); } .pg-act.ok:hover { background: rgba(74,222,128,.2); }
    .pg-act.del  { color: #fca5a5; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08); } .pg-act.del:hover { background: rgba(248,113,113,.18); }

    .pg-flash.err { border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.1); color: #fca5a5; }
    .pg-flash.info { border-color: var(--pg-line); background: rgba(255,255,255,.05); color: #cfd2e0; }

    /* Readout strip */
    .pg-stats { display: grid; grid-template-columns: repeat(3, 1fr); margin-bottom: 14px; border: 1px solid var(--pg-line); border-radius: 20px; background: var(--pg-panel); overflow: hidden; }
    .pg-stat { position: relative; padding: 16px 14px 14px 18px; border-right: 1px solid var(--pg-line); }
    .pg-stat:last-child { border-right: 0; }
    .pg-stat::before { content: ""; position: absolute; left: 0; top: 14px; bottom: 14px; width: 3px; border-radius: 0 3px 3px 0; background: var(--c); }
    .pg-stat b { display: block; font-family: 'Sora', sans-serif; font-size: 28px; font-weight: 700; letter-spacing: -.03em; line-height: 1; }
    .pg-stat span { display: block; margin-top: 7px; font-size: 12px; color: var(--pg-dim); }

    .pg-section { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; margin: 20px 4px 12px; }
    .pg-section h3 { font-family: 'Sora', sans-serif; font-size: 16px; font-weight: 600; }
    .pg-section small { font-size: 12.5px; color: #6f748c; }

    .pg-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
    .pg-chip { padding: 6px 14px; border-radius: 99px; border: 1px solid var(--pg-line); color: var(--pg-dim); font-size: 12.5px; font-weight: 600; text-decoration: none; transition: .2s; }
    .pg-chip:hover { color: #fff; background: rgba(255,255,255,.05); }
    .pg-chip.on { color: #c4b5fd; border-color: rgba(167,139,250,.5); background: rgba(139,92,246,.14); }
    .pg-chip i { font-style: normal; opacity: .6; margin-left: 4px; }

    /* Subject cards */
    .pg-sgrid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); }
    .pg-scard { position: relative; display: flex; flex-direction: column; padding: 18px; border: 1px solid var(--pg-line); border-radius: 20px; background: var(--pg-panel); transition: border-color .2s; }
    .pg-scard:hover { border-color: rgba(125,180,255,.4); }
    .pg-scard:nth-child(3n+1) { border-radius: 20px 20px 6px 20px; }
    .pg-letter { width: 44px; height: 44px; flex: none; display: grid; place-items: center; border-radius: 14px; border: 1px solid; font-family: 'Sora', sans-serif; font-size: 18px; font-weight: 700; }
    .pg-prog { margin-top: 18px; font-size: 12px; color: var(--pg-dim); }
    .pg-prog div:first-child { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .pg-track { height: 5px; border-radius: 9px; background: rgba(255,255,255,.07); overflow: hidden; }
    .pg-track i { display: block; height: 100%; border-radius: 9px; background: linear-gradient(90deg, #60a5fa, #a78bfa); }
    .pg-link { display: inline-block; margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--pg-line); font-size: 13px; font-weight: 600; color: #7db4ff; text-decoration: none; }
    .pg-menu { position: relative; z-index: 20; flex: none; }
    .pg-menu > summary { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 10px; color: var(--pg-dim); cursor: pointer; list-style: none; }
    .pg-menu > summary::-webkit-details-marker { display: none; }
    .pg-menu > summary:hover { color: #fff; background: rgba(255,255,255,.06); }
    .pg-menu > div { position: absolute; right: 0; top: 100%; margin-top: 4px; width: 140px; padding: 4px; border-radius: 14px; border: 1px solid rgba(160,170,255,.2); background: #14122a; box-shadow: 0 18px 40px rgba(0,0,0,.6); z-index: 30; }
    .pg-menu button { width: 100%; text-align: left; padding: 8px 12px; border: 0; border-radius: 9px; background: none; color: #e4e6f2; font: inherit; font-size: 13px; cursor: pointer; }
    .pg-menu button:hover { background: rgba(255,255,255,.06); }
    .pg-menu button.d { color: #fca5a5; } .pg-menu button.d:hover { background: rgba(248,113,113,.12); }

    /* Task cards */
    .pg-tlist { display: flex; flex-direction: column; gap: 10px; }
    .pg-task { display: grid; grid-template-columns: auto 48px 1fr; gap: 14px; align-items: center; padding: 16px 16px 16px 14px; border: 1px solid var(--pg-line); border-left: 3px solid var(--bar, #5b5f78); border-radius: 18px; background: var(--pg-panel); transition: border-color .2s; }
    .pg-task:hover { border-color: rgba(160,170,255,.28); border-left-color: var(--bar); }
    .pg-task .pg-check { width: 17px; height: 17px; accent-color: #6366f1; }
    .pg-task-acts { grid-column: 1 / -1; display: flex; flex-wrap: wrap; gap: 8px; padding-top: 12px; border-top: 1px solid var(--pg-line); }
    .pg-bulk { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; padding: 0 4px; font-size: 13px; color: var(--pg-dim); }
    .pg-bulk label { display: inline-flex; align-items: center; gap: 9px; cursor: pointer; }

    /* Dialogs (used outside .pg) */
    .pgd { padding: 24px; border-radius: 22px; border: 1px solid rgba(160,170,255,.2); background: #100e24; box-shadow: 0 40px 100px rgba(0,0,0,.7); color: #e4e6f2; display: flex; flex-direction: column; gap: 16px; }
    .pgd h3 { font-family: 'Sora', sans-serif; font-size: 18px; font-weight: 700; }
    .pgd p.s { margin-top: 4px; font-size: 13px; color: #8d92ad; }
    .pgd label { display: block; margin-bottom: 7px; font-size: 13px; font-weight: 600; }
    .pgd .row { display: flex; justify-content: flex-end; gap: 10px; padding-top: 6px; }

    @media (min-width: 900px) {
        .pg-stat { padding: 20px 20px 18px 24px; } .pg-stat b { font-size: 36px; }
        .pg-task { grid-template-columns: auto 52px 1fr auto; padding: 18px 20px 18px 16px; }
        .pg-task-acts { grid-column: auto; padding-top: 0; border-top: 0; justify-content: flex-end; }
    }
</style>