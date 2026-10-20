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