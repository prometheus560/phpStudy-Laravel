{{-- Extra styles for the Study Plan pages. Use together with partials.page-kit --}}
<style>
    .sp-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }

    .sp-note { margin-bottom: 14px; padding: 12px 16px; border-radius: 14px; border: 1px solid rgba(251,191,36,.3); background: rgba(251,191,36,.08); color: #fcd34d; font-size: 13.5px; }

    /* ----- Week timetable ----- */
    .sp-week { display: grid; gap: 12px; grid-template-columns: repeat(auto-fill, minmax(255px, 1fr)); }
    .sp-day { display: flex; flex-direction: column; padding: 14px; border: 1px solid var(--pg-line); border-radius: 18px; background: var(--pg-panel); }
    .sp-day.today { border-color: rgba(125,180,255,.55); box-shadow: 0 0 0 1px rgba(125,180,255,.18); }
    .sp-day.past { opacity: .5; }
    .sp-dayhead { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; margin-bottom: 4px; }
    .sp-dayname { font-family: 'Sora', sans-serif; font-size: 15px; font-weight: 600; }
    .sp-daydate { font-size: 12px; color: var(--pg-dim); }
    .sp-daysum { font-size: 12px; color: var(--pg-dim); margin-bottom: 10px; }
    .sp-tag { display: inline-block; font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 99px; border: 1px solid var(--pg-line); color: var(--pg-dim); }
    .sp-tag.now { color: #7db4ff; border-color: rgba(125,180,255,.45); background: rgba(125,180,255,.1); }
    .sp-tag.buf { color: #c4b5fd; border-color: rgba(167,139,250,.45); background: rgba(139,92,246,.12); }
    .sp-tag.ok { color: #86efac; border-color: rgba(74,222,128,.4); background: rgba(74,222,128,.1); }
    .sp-tag.warn { color: #fbbf24; border-color: rgba(251,191,36,.4); background: rgba(251,191,36,.1); }

    .sp-events { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
    .sp-ev { font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 8px; }
    .sp-ev.exam { color: #fda4af; background: rgba(251,113,133,.12); border: 1px solid rgba(251,113,133,.35); }
    .sp-ev.due { color: #fcd34d; background: rgba(251,191,36,.1); border: 1px solid rgba(251,191,36,.3); }

    .sp-items { display: flex; flex-direction: column; gap: 8px; }
    .sp-item { position: relative; padding: 10px 12px 10px 14px; border-radius: 12px; border: 1px solid var(--pg-line); background: rgba(255,255,255,.03); }
    .sp-item::before { content: ""; position: absolute; left: 0; top: 8px; bottom: 8px; width: 3px; border-radius: 0 3px 3px 0; background: var(--bar, #5b5f78); }
    .sp-item.d3 { --bar: #fb7185; } .sp-item.d2 { --bar: #fbbf24; } .sp-item.d1 { --bar: #60a5fa; }
    .sp-item.busy { background: transparent; border-style: dashed; color: var(--pg-dim); }
    .sp-item.busy::before { background: #3b3f55; }
    .sp-item.buffer { border-style: dashed; border-color: rgba(167,139,250,.45); background: rgba(139,92,246,.07); }
    .sp-item.buffer::before { background: #a78bfa; }
    .sp-item.free { background: transparent; color: var(--pg-dim); }
    .sp-item.late { opacity: .5; }
    .sp-time { display: block; font-size: 11.5px; color: var(--pg-dim); margin-bottom: 3px; }
    .sp-what { font-size: 14px; font-weight: 600; line-height: 1.3; word-break: break-word; }
    .sp-how { margin-top: 3px; font-size: 12.5px; color: #b9bdd0; line-height: 1.4; }
    .sp-foot { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-top: 7px; font-size: 11.5px; color: var(--pg-dim); }
    .sp-empty-day { font-size: 12.5px; color: var(--pg-dim); padding: 6px 2px; }

    /* ----- Strategy cards ----- */
    .sp-grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }
    .sp-card { padding: 18px; border: 1px solid var(--pg-line); border-radius: 18px; background: var(--pg-panel); }
    .sp-card h4 { font-family: 'Sora', sans-serif; font-size: 15.5px; font-weight: 600; }
    .sp-card .sp-meta { display: flex; flex-wrap: wrap; gap: 6px; margin: 8px 0 12px; }
    .sp-tech { margin-top: 10px; font-size: 13px; line-height: 1.45; color: #b9bdd0; }
    .sp-tech b { display: block; color: #f2f3fb; font-weight: 600; margin-bottom: 2px; }
    .sp-card details summary { cursor: pointer; font-size: 12.5px; font-weight: 600; color: #7db4ff; list-style: none; }
    .sp-card details summary::-webkit-details-marker { display: none; }

    .sp-how-panel summary { cursor: pointer; font-weight: 600; font-size: 14px; list-style: none; }
    .sp-how-panel summary::-webkit-details-marker { display: none; }
    .sp-how-panel ul { margin: 12px 0 0 18px; font-size: 13.5px; color: #b9bdd0; line-height: 1.55; }

    /* ----- Setup form ----- */
    .sp-form { display: flex; flex-direction: column; gap: 14px; }
    .sp-block { padding: 20px 18px; border: 1px solid var(--pg-line); border-radius: 20px; background: var(--pg-panel); }
    .sp-block h3 { font-family: 'Sora', sans-serif; font-size: 16px; font-weight: 600; }
    .sp-block > p { margin: 4px 0 14px; font-size: 13px; color: var(--pg-dim); }
    .sp-label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: #b9bdd0; }
    .sp-srow { display: grid; grid-template-columns: 1fr; gap: 10px; padding: 14px 0; border-top: 1px solid var(--pg-line); }
    .sp-srow:first-of-type { border-top: 0; padding-top: 4px; }
    .sp-sname { font-size: 14.5px; font-weight: 600; align-self: center; word-break: break-word; }
    .sp-wrow { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; align-items: center; padding: 10px 0; border-top: 1px solid var(--pg-line); }
    .sp-wrow:first-of-type { border-top: 0; }
    .sp-wrow label.sp-on { grid-column: 1 / -1; display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .sp-wrow input[type=checkbox] { width: 18px; height: 18px; accent-color: #6366f1; }
    .sp-wrow.off .pg-input { opacity: .4; }
    .sp-wrow .sp-t { display: flex; flex-direction: column; gap: 4px; font-size: 11.5px; color: var(--pg-dim); }
    .sp-opts { display: grid; gap: 10px; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
    .sp-opt { position: relative; display: block; cursor: pointer; }
    .sp-opt input { position: absolute; opacity: 0; inset: 0; cursor: pointer; }
    .sp-opt span { display: block; padding: 13px 14px; border-radius: 14px; border: 1px solid #2a2842; background: #0f0e1c; font-size: 13px; color: #b9bdd0; line-height: 1.4; transition: border-color .2s, background .2s; }
    .sp-opt span b { display: block; margin-bottom: 2px; color: #f2f3fb; font-size: 14px; }
    .sp-opt input:checked + span { border-color: #6366f1; background: rgba(99,102,241,.14); }
    .sp-opt input:focus-visible + span { outline: 2px solid #7db4ff; outline-offset: 2px; }
    .sp-crow { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 12px 0; border-top: 1px solid var(--pg-line); }
    .sp-crow:first-child { border-top: 0; }
    .sp-crow .full { grid-column: 1 / -1; }
    .sp-err { margin-bottom: 14px; padding: 12px 16px; border-radius: 14px; border: 1px solid rgba(248,113,113,.3); background: rgba(248,113,113,.1); color: #fca5a5; font-size: 13.5px; }
    .sp-err ul { margin-left: 18px; }

    /* ----- Checklist ----- */
    .sp-prog { margin-bottom: 14px; padding: 14px 16px; border: 1px solid var(--pg-line); border-radius: 18px; background: var(--pg-panel); }
    .sp-prog div:first-child { display: flex; justify-content: space-between; font-size: 13px; color: var(--pg-dim); margin-bottom: 8px; }
    .sp-prog b { color: #f2f3fb; }
    .sp-list { display: flex; flex-direction: column; gap: 8px; }
    .sp-check { display: grid; grid-template-columns: auto 1fr auto; gap: 12px; align-items: start; padding: 13px 14px; border: 1px solid var(--pg-line); border-radius: 14px; background: var(--pg-panel); }
    .sp-check input[type=checkbox] { width: 20px; height: 20px; margin-top: 2px; accent-color: #6366f1; cursor: pointer; }
    .sp-check.done .sp-what { text-decoration: line-through; color: var(--pg-dim); }
    .sp-check.done { opacity: .65; }
    .sp-check.hot { border-left: 3px solid #fb7185; }
    .sp-check.focus { border-left: 3px solid #6366f1; }
    .sp-check form { margin: 0; }
    .sp-calm { margin-bottom: 14px; padding: 14px 16px; border-radius: 16px; border: 1px solid rgba(74,222,128,.3); background: rgba(74,222,128,.08); color: #bbf7d0; font-size: 14px; line-height: 1.5; }

    @media (min-width: 720px) {
        .sp-srow { grid-template-columns: 1.4fr 1fr .7fr 1fr; align-items: end; }
        .sp-srow .sp-sname { align-self: center; padding-bottom: 12px; }
        .sp-wrow { grid-template-columns: 1.3fr 1fr 1fr; }
        .sp-wrow label.sp-on { grid-column: auto; }
        .sp-crow { grid-template-columns: 1fr 1fr 1fr 1.4fr auto; align-items: end; }
        .sp-crow .full { grid-column: auto; }
    }
</style>