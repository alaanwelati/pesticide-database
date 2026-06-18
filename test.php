<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PestiSmart — Bitki Koruma Rehberi</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f8faf8;
      --surface: #ffffff;
      --surface2: #f5f7f5;
      --border: rgba(0,0,0,0.1);
      --green: #22c55e;
      --green-dim: #16a34a;
      --green-dark: #15803d;
      --amber: #f59e0b;
      --red: #ef4444;
      --text: #1a1a1a;
      --muted: #6b7280;
      --white: #ffffff;
      --radius: 16px;
      --glow: 0 0 40px rgba(34,197,94,0.12);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Animated background */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse 60% 40% at 20% 20%, rgba(74,222,128,0.06) 0%, transparent 60%),
        radial-gradient(ellipse 40% 50% at 80% 80%, rgba(34,197,94,0.04) 0%, transparent 60%);
      pointer-events: none;
      z-index: 0;
    }

    /* NAVBAR */
    nav {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 40px;
      height: 68px;
      background: rgba(248,250,248,0.85);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      color: var(--green);
      text-decoration: none;
      letter-spacing: -0.5px;
    }

    .nav-logo span {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, var(--green-dark), var(--green-dim));
      border-radius: 10px;
      display: grid;
      place-items: center;
      font-size: 1rem;
      box-shadow: 0 0 20px rgba(74,222,128,0.3);
    }

    .nav-links {
      display: flex;
      gap: 6px;
      align-items: center;
    }

    .nav-links a {
      color: var(--muted);
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.2s;
    }
    .nav-links a:hover { color: var(--green); background: rgba(74,222,128,0.08); }

    .nav-search {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 8px 14px;
    }

    .nav-search input {
      background: none;
      border: none;
      outline: none;
      color: var(--text);
      font-size: 0.88rem;
      width: 180px;
      font-family: 'DM Sans', sans-serif;
    }

    .nav-search input::placeholder { color: var(--muted); }

    .nav-search button {
      background: var(--green-dim);
      color: #000;
      border: none;
      border-radius: 6px;
      padding: 5px 12px;
      font-size: 0.82rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .nav-search button:hover { background: var(--green); transform: scale(1.03); }

    /* MAIN */
    main { position: relative; z-index: 1; }

    /* HERO */
    .hero {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 100px 24px 60px;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(74,222,128,0.1);
      border: 1px solid rgba(74,222,128,0.25);
      border-radius: 999px;
      padding: 8px 18px;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--green);
      margin-bottom: 28px;
      letter-spacing: 0.5px;
      animation: fadeUp 0.6s ease both;
    }

    .hero-badge::before {
      content: '';
      width: 7px; height: 7px;
      background: var(--green);
      border-radius: 50%;
      box-shadow: 0 0 8px var(--green);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.5; transform: scale(0.8); }
    }

    h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.8rem, 6vw, 5.5rem);
      font-weight: 900;
      line-height: 1.1;
      letter-spacing: -2px;
      margin-bottom: 20px;
      animation: fadeUp 0.7s ease 0.1s both;
    }

    h1 em {
      font-style: normal;
      color: var(--green);
      text-shadow: 0 0 60px rgba(74,222,128,0.4);
    }

    .hero-lead {
      font-size: 1.1rem;
      color: var(--muted);
      max-width: 520px;
      line-height: 1.7;
      margin-bottom: 36px;
      animation: fadeUp 0.7s ease 0.2s both;
    }

    .hero-actions {
      display: flex;
      gap: 14px;
      justify-content: center;
      flex-wrap: wrap;
      animation: fadeUp 0.7s ease 0.3s both;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 28px;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      border: none;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--green-dim), var(--green));
      color: #000;
      box-shadow: 0 0 30px rgba(74,222,128,0.25);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 50px rgba(74,222,128,0.4); }

    .btn-ghost {
      background: transparent;
      color: var(--text);
      border: 1px solid var(--border);
    }
    .btn-ghost:hover { border-color: var(--green); color: var(--green); background: rgba(74,222,128,0.06); }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(24px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* STATS STRIP */
    .stats-strip {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1px;
      background: var(--border);
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
    }

    .stat-cell {
      background: var(--surface);
      padding: 36px 24px;
      text-align: center;
      transition: background 0.2s;
    }
    .stat-cell:hover { background: var(--surface2); }

    .stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 2.8rem;
      font-weight: 900;
      color: var(--green);
      line-height: 1;
      margin-bottom: 8px;
      text-shadow: 0 0 30px rgba(74,222,128,0.3);
    }

    .stat-lbl {
      font-size: 0.82rem;
      color: var(--muted);
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* SECTIONS */
    section {
      padding: 80px 24px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .section-tag {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--green);
      margin-bottom: 12px;
    }

    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.8rem, 3vw, 2.8rem);
      font-weight: 700;
      letter-spacing: -1px;
      margin-bottom: 16px;
      line-height: 1.2;
    }

    .section-lead {
      color: var(--muted);
      font-size: 1rem;
      line-height: 1.7;
      max-width: 560px;
      margin-bottom: 48px;
    }

    /* CATEGORY CARDS */
    .cat-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .cat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 32px;
      cursor: pointer;
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .cat-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(74,222,128,0.04), transparent);
      opacity: 0;
      transition: opacity 0.3s;
    }

    .cat-card:hover { border-color: rgba(74,222,128,0.35); transform: translateY(-4px); box-shadow: var(--glow); }
    .cat-card:hover::before { opacity: 1; }

    .cat-icon {
      font-size: 2.2rem;
      margin-bottom: 16px;
    }

    .cat-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .cat-desc {
      color: var(--muted);
      font-size: 0.9rem;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .cat-count {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(74,222,128,0.1);
      border: 1px solid rgba(74,222,128,0.2);
      border-radius: 999px;
      padding: 5px 12px;
      font-size: 0.8rem;
      color: var(--green);
      font-weight: 600;
    }

    /* CHART AREA */
    .charts-grid {
      display: grid;
      grid-template-columns: 1.3fr 0.7fr;
      gap: 20px;
    }

    .chart-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 28px;
    }

    .chart-title {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 24px;
      color: var(--text);
    }

    /* Bar chart */
    .bar-chart { display: flex; flex-direction: column; gap: 14px; }

    .bar-row { display: flex; align-items: center; gap: 12px; }

    .bar-label { font-size: 0.82rem; color: var(--muted); width: 110px; flex-shrink: 0; }

    .bar-track {
      flex: 1;
      height: 10px;
      background: rgba(255,255,255,0.05);
      border-radius: 99px;
      overflow: hidden;
    }

    .bar-fill {
      height: 100%;
      border-radius: 99px;
      animation: barGrow 1.2s ease both;
      transform-origin: left;
    }

    @keyframes barGrow {
      from { width: 0 !important; }
    }

    .bar-val { font-size: 0.8rem; color: var(--muted); width: 32px; text-align: right; }

    /* Donut */
    .donut-wrap { display: flex; flex-direction: column; align-items: center; gap: 24px; }

    svg.donut { transform: rotate(-90deg); }

    .donut-legend { width: 100%; display: flex; flex-direction: column; gap: 10px; }

    .legend-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.82rem;
    }

    .legend-dot {
      width: 10px; height: 10px;
      border-radius: 50%;
      margin-right: 8px;
      flex-shrink: 0;
    }

    .legend-name { color: var(--muted); display: flex; align-items: center; }
    .legend-pct { color: var(--text); font-weight: 600; }

    /* AI CHAT */
    .chat-section {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
    }

    .chat-header {
      padding: 24px 28px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 14px;
      background: linear-gradient(90deg, rgba(74,222,128,0.05), transparent);
    }

    .chat-avatar {
      width: 44px; height: 44px;
      background: linear-gradient(135deg, var(--green-dark), var(--green-dim));
      border-radius: 12px;
      display: grid;
      place-items: center;
      font-size: 1.3rem;
      box-shadow: 0 0 20px rgba(74,222,128,0.25);
    }

    .chat-info h3 {
      font-weight: 700;
      font-size: 1rem;
      margin-bottom: 2px;
    }

    .chat-info p { font-size: 0.78rem; color: var(--muted); }

    .chat-status {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.78rem;
      color: var(--green);
    }

    .chat-dot {
      width: 8px; height: 8px;
      background: var(--green);
      border-radius: 50%;
      box-shadow: 0 0 8px var(--green);
      animation: pulse 2s infinite;
    }

    .chat-messages {
      padding: 24px 28px;
      display: flex;
      flex-direction: column;
      gap: 16px;
      min-height: 280px;
      max-height: 380px;
      overflow-y: auto;
    }

    .chat-messages::-webkit-scrollbar { width: 4px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    .msg {
      display: flex;
      gap: 10px;
      animation: msgIn 0.3s ease;
    }

    @keyframes msgIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .msg.user { flex-direction: row-reverse; }

    .msg-avatar {
      width: 32px; height: 32px;
      border-radius: 8px;
      display: grid;
      place-items: center;
      font-size: 0.9rem;
      flex-shrink: 0;
    }

    .msg.bot .msg-avatar { background: rgba(74,222,128,0.15); border: 1px solid rgba(74,222,128,0.2); }
    .msg.user .msg-avatar { background: rgba(255,255,255,0.08); }

    .msg-bubble {
      max-width: 75%;
      padding: 12px 16px;
      border-radius: 12px;
      font-size: 0.88rem;
      line-height: 1.6;
    }

    .msg.bot .msg-bubble {
      background: var(--surface2);
      border: 1px solid var(--border);
      border-top-left-radius: 4px;
      color: var(--text);
    }

    .msg.user .msg-bubble {
      background: linear-gradient(135deg, var(--green-dark), rgba(34,197,94,0.7));
      border-top-right-radius: 4px;
      color: #fff;
    }

    .chat-input-row {
      padding: 20px 28px;
      border-top: 1px solid var(--border);
      display: flex;
      gap: 10px;
    }

    .chat-input {
      flex: 1;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 13px 18px;
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      outline: none;
      transition: border-color 0.2s;
    }

    .chat-input:focus { border-color: rgba(74,222,128,0.4); }
    .chat-input::placeholder { color: var(--muted); }

    .chat-send {
      background: linear-gradient(135deg, var(--green-dim), var(--green));
      color: #000;
      border: none;
      border-radius: 12px;
      width: 48px;
      height: 48px;
      cursor: pointer;
      font-size: 1.1rem;
      transition: all 0.2s;
      display: grid;
      place-items: center;
    }

    .chat-send:hover { transform: scale(1.08); box-shadow: 0 0 20px rgba(74,222,128,0.3); }

    .typing-indicator {
      display: flex;
      gap: 4px;
      align-items: center;
      padding: 8px 0;
    }

    .typing-dot {
      width: 6px; height: 6px;
      background: var(--muted);
      border-radius: 50%;
      animation: typingBounce 1.2s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typingBounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5px); }
    }

    /* QUICK SEARCH */
    .search-section {
      background: linear-gradient(135deg, rgba(74,222,128,0.06), rgba(34,197,94,0.03));
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 48px;
      text-align: center;
    }

    .big-search {
      display: flex;
      max-width: 640px;
      margin: 0 auto;
      gap: 0;
      border: 1px solid rgba(74,222,128,0.3);
      border-radius: 16px;
      overflow: hidden;
      background: var(--surface2);
      box-shadow: 0 0 40px rgba(74,222,128,0.1);
    }

    .big-search input {
      flex: 1;
      background: none;
      border: none;
      outline: none;
      padding: 18px 24px;
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 1rem;
    }

    .big-search input::placeholder { color: var(--muted); }

    .big-search button {
      background: linear-gradient(135deg, var(--green-dim), var(--green));
      color: #000;
      border: none;
      padding: 18px 32px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      font-family: 'DM Sans', sans-serif;
      transition: all 0.2s;
    }
    .big-search button:hover { opacity: 0.9; }

    .quick-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: center;
      margin-top: 18px;
    }

    .quick-tag {
      background: rgba(74,222,128,0.08);
      border: 1px solid rgba(74,222,128,0.18);
      color: var(--green);
      border-radius: 999px;
      padding: 6px 14px;
      font-size: 0.82rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }
    .quick-tag:hover { background: rgba(74,222,128,0.18); border-color: rgba(74,222,128,0.4); }

    /* FOOTER */
    footer {
      border-top: 1px solid var(--border);
      padding: 36px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: var(--muted);
      font-size: 0.83rem;
    }

    @media (max-width: 768px) {
      nav { padding: 0 16px; }
      .nav-search { display: none; }
      .stats-strip { grid-template-columns: repeat(2, 1fr); }
      .cat-grid { grid-template-columns: 1fr; }
      .charts-grid { grid-template-columns: 1fr; }
      section { padding: 48px 16px; }
      .search-section { padding: 28px 16px; }
      footer { flex-direction: column; gap: 12px; text-align: center; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav>
    <a href="#" class="nav-logo">
      <span>🌿</span>
      PestiSmart
    </a>
    <div class="nav-links">
      <a href="#kategoriler">Kategoriler</a>
      <a href="#istatistikler">İstatistikler</a>
      <a href="#asistan">AI Asistan</a>
      <a href="admin.html" style="color:var(--green);border:1px solid rgba(74,222,128,0.3);border-radius:8px;">Veritabanı</a>
    </div>
    <div class="nav-search">
      <svg width="14" height="14" fill="none" stroke="#6b8c6b" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input id="globalSearch" type="text" placeholder="Pestisit ara..." />
      <button id="globalSearchBtn">Ara</button>
    </div>
  </nav>

  <main>
    <!-- HERO -->
    <div class="hero">
      <div class="hero-badge">Kapsamlı Pestisit Veritabanı</div>
      <h1>Bitki Korumada<br/><em>Akıllı</em> Çözümler</h1>
      <p class="hero-lead">
        Bitki patojenlerinin kimyasal kontrolünde kullanılan pestisitleri keşfedin. 
        Etki mekanizmaları, risk seviyeleri ve kullanım alanları hakkında detaylı bilgi edinin.
      </p>
      <div class="hero-actions">
        <a href="admin.html" class="btn btn-primary">🔬 Veritabanını Aç</a>
        <a href="#asistan" class="btn btn-ghost">🤖 AI Asistan</a>
      </div>
    </div>

    <!-- STATS STRIP -->
    <div class="stats-strip">
      <div class="stat-cell">
        <div class="stat-num" id="stat-total">—</div>
        <div class="stat-lbl">Toplam Pestisit</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num" id="stat-fungisit">—</div>
        <div class="stat-lbl">Fungisit</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num" id="stat-herbisit">—</div>
        <div class="stat-lbl">Herbisit</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num" id="stat-bocek">—</div>
        <div class="stat-lbl">Böcek İlacı</div>
      </div>
    </div>

    <!-- CATEGORIES -->
    <section id="kategoriler">
      <div class="section-tag">Kategoriler</div>
      <h2 class="section-title">Pestisit Türleri</h2>
      <p class="section-lead">Her pestisit kategorisi farklı patojenlere karşı farklı mekanizmalarla etkisini gösterir.</p>

      <div class="cat-grid">
        <a href="search.html?search=Fungisit" class="cat-card">
          <div class="cat-icon">🍄</div>
          <div class="cat-name">Fungisitler</div>
          <div class="cat-desc">Mantar kaynaklı hastalıklara karşı kullanılan kimyasal maddeler. Sistemik ve koruyucu türleri mevcuttur.</div>
          <div class="cat-count">⬤ Fungisit & Koruyucu</div>
        </a>
        <a href="search.html?search=Herbisit" class="cat-card">
          <div class="cat-icon">🌾</div>
          <div class="cat-name">Herbisitler</div>
          <div class="cat-desc">Yabancı ot kontrolünde kullanılan, çeşitli etki mekanizmalarına sahip geniş bir ilaç grubu.</div>
          <div class="cat-count">⬤ Geniş spektrum</div>
        </a>
        <a href="search.html?search=Böcek" class="cat-card">
          <div class="cat-icon">🦟</div>
          <div class="cat-name">Böcek İlaçları</div>
          <div class="cat-desc">Zararlı böcek ve nematodlara karşı etkili insektisit ve nematisit grubu bileşikler.</div>
          <div class="cat-count">⬤ İnsektisit & Nematisit</div>
        </a>
        <a href="search.html?search=Bakterisit" class="cat-card">
          <div class="cat-icon">🦠</div>
          <div class="cat-name">Bakterisitler</div>
          <div class="cat-desc">Bakteriyel bitki hastalıklarına karşı kullanılan antibiyotik ve bakır bazlı bileşikler.</div>
          <div class="cat-count">⬤ Antibiyotik bazlı</div>
        </a>
      </div>
    </section>

    <!-- CHARTS -->
    <section id="istatistikler">
      <div class="section-tag">İstatistikler</div>
      <h2 class="section-title">Veritabanı Analizi</h2>
      <p class="section-lead">Pestisit dağılımı ve risk seviyeleri hakkında görsel analiz.</p>

      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-title">📊 Tür Bazında Dağılım</div>
          <div class="bar-chart" id="barChart">
            <div style="color:var(--muted);font-size:0.85rem">Yükleniyor...</div>
          </div>
        </div>
        <div class="chart-card">
          <div class="chart-title">🎯 Risk Dağılımı</div>
          <div class="donut-wrap" id="donutWrap">
            <div style="color:var(--muted);font-size:0.85rem">Yükleniyor...</div>
          </div>
        </div>
      </div>
    </section>

    <!-- QUICK SEARCH -->
    <section>
      <div class="search-section">
        <div class="section-tag">Hızlı Arama</div>
        <h2 class="section-title" style="margin-bottom:8px">Pestisit Ara</h2>
        <p style="color:var(--muted);margin-bottom:28px;font-size:0.95rem">İsim, tür veya hedef hastalığa göre arama yapın</p>
        <div class="big-search">
          <input id="mainSearch" type="text" placeholder="Örn: Azoxystrobin, külleme, Herbisit..." />
          <button id="mainSearchBtn">🔍 Ara</button>
        </div>
        <div class="quick-tags">
          <span class="quick-tag" data-q="Azoxystrobin">Azoxystrobin</span>
          <span class="quick-tag" data-q="külleme">Külleme</span>
          <span class="quick-tag" data-q="Glyphosate">Glyphosate</span>
          <span class="quick-tag" data-q="Mancozeb">Mancozeb</span>
          <span class="quick-tag" data-q="nematode">Nematode</span>
          <span class="quick-tag" data-q="Fungisit">Fungisit</span>
        </div>
      </div>
    </section>

    <!-- AI CHAT -->
    <section id="asistan" style="padding-bottom: 100px;">
      <div class="section-tag">Yapay Zeka</div>
      <h2 class="section-title">AI Pestisit Asistanı</h2>
      <p class="section-lead">Pestisitler hakkında sorularınızı sorun — veritabanından anında yanıt alın.</p>

      <div class="chat-section">
        <div class="chat-header">
          <div class="chat-avatar">🤖</div>
          <div class="chat-info">
            <h3>PestiBot</h3>
            <p>Pestisit veritabanı asistanı</p>
          </div>
          <div class="chat-status">
            <div class="chat-dot"></div>
            Çevrimiçi
          </div>
        </div>

        <div class="chat-messages" id="chatMessages">
          <div class="msg bot">
            <div class="msg-avatar">🌿</div>
            <div class="msg-bubble">
              Merhaba! Ben PestiBot. Pestisitler hakkında sorularınızı yanıtlayabilirim. 
              Örneğin: <em>"Azoxystrobin nedir?"</em> veya <em>"külleme için hangi pestisit kullanılır?"</em>
            </div>
          </div>
        </div>

        <div class="chat-input-row">
          <input class="chat-input" id="chatInput" type="text" placeholder="Pestisit hakkında bir soru sorun..." />
          <button class="chat-send" id="chatSend" title="Gönder">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"/>
            </svg>
          </button>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div>© 2025 PestiSmart — Bitki Koruma Rehberi</div>
    <div>Eğitim amaçlı hazırlanmıştır</div>
  </footer>

  <script>
    // ── SEARCH ──
    function doSearch(q) {
      if (q.trim()) window.open('search.html?search=' + encodeURIComponent(q.trim()), '_blank');
    }

    document.getElementById('globalSearchBtn').onclick = () => doSearch(document.getElementById('globalSearch').value);
    document.getElementById('globalSearch').addEventListener('keypress', e => { if (e.key === 'Enter') doSearch(e.target.value); });
    document.getElementById('mainSearchBtn').onclick = () => doSearch(document.getElementById('mainSearch').value);
    document.getElementById('mainSearch').addEventListener('keypress', e => { if (e.key === 'Enter') doSearch(e.target.value); });

    document.querySelectorAll('.quick-tag').forEach(tag => {
      tag.onclick = () => doSearch(tag.dataset.q);
    });

    // ── LOAD STATS (public search for counting) ──
    async function loadStats() {
      const types = [
        { key: 'Fungisit', id: 'stat-fungisit' },
        { key: 'Herbisit', id: 'stat-herbisit' },
        { key: 'Böcek',    id: 'stat-bocek' },
      ];

      // Try fetching via search
      let total = 0;
      const counts = {};

      for (const t of types) {
        try {
          const r = await fetch(`/api/search?q=${encodeURIComponent(t.key)}`);
          if (r.ok) {
            const d = await r.json();
            const n = (d.pesticides || []).length;
            counts[t.id] = n;
            total += n;
          }
        } catch {}
      }

      // Animate counting
      function animCount(el, target) {
        let start = 0;
        const dur = 1200;
        const step = dur / target;
        const timer = setInterval(() => {
          start += Math.ceil(target / 40);
          if (start >= target) { start = target; clearInterval(timer); }
          el.textContent = start;
        }, step < 16 ? 16 : step);
      }

      for (const t of types) {
        const el = document.getElementById(t.id);
        if (el && counts[t.id] !== undefined) animCount(el, counts[t.id]);
        else if (el) el.textContent = '—';
      }

      const totalEl = document.getElementById('stat-total');
      if (totalEl) {
        const approx = Object.values(counts).reduce((a, b) => a + b, 0);
        animCount(totalEl, approx || 100);
      }

      renderBarChart(counts);
    }

    // ── BAR CHART ──
    function renderBarChart(counts) {
      const data = [
        { label: 'Fungisit', count: counts['stat-fungisit'] || 0, color: '#4ade80' },
        { label: 'Herbisit', count: counts['stat-herbisit'] || 0, color: '#86efac' },
        { label: 'Böcek İlacı', count: counts['stat-bocek'] || 0, color: '#22c55e' },
        { label: 'Bakterisit', count: 8, color: '#fbbf24' },
      ];
      const max = Math.max(...data.map(d => d.count), 1);
      const container = document.getElementById('barChart');
      container.innerHTML = data.map(d => `
        <div class="bar-row">
          <div class="bar-label">${d.label}</div>
          <div class="bar-track">
            <div class="bar-fill" style="width:${(d.count/max*100).toFixed(1)}%;background:${d.color};animation-delay:${Math.random()*0.3}s"></div>
          </div>
          <div class="bar-val">${d.count}</div>
        </div>
      `).join('');
    }

    // ── DONUT CHART ──
    function renderDonut() {
      const data = [
        { label: 'Düşük Risk',  pct: 22, color: '#4ade80' },
        { label: 'Orta Risk',   pct: 45, color: '#fbbf24' },
        { label: 'Yüksek Risk', pct: 33, color: '#f87171' },
      ];

      const r = 56, cx = 64, cy = 64, circ = 2 * Math.PI * r;
      let offset = 0;
      const slices = data.map(d => {
        const dash = (d.pct / 100) * circ;
        const slice = { ...d, dash, offset };
        offset += dash;
        return slice;
      });

      const svg = `<svg class="donut" width="128" height="128" viewBox="0 0 128 128">
        <circle cx="${cx}" cy="${cy}" r="${r}" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="16"/>
        ${slices.map((s, i) => `
          <circle cx="${cx}" cy="${cy}" r="${r}" fill="none" stroke="${s.color}"
            stroke-width="16" stroke-dasharray="${s.dash} ${circ - s.dash}"
            stroke-dashoffset="${-s.offset}" stroke-linecap="butt"
            style="transition:stroke-dasharray 1s ease ${i*0.2}s"/>
        `).join('')}
      </svg>`;

      const legend = data.map(d => `
        <div class="legend-row">
          <span class="legend-name"><span class="legend-dot" style="background:${d.color}"></span>${d.label}</span>
          <span class="legend-pct">%${d.pct}</span>
        </div>
      `).join('');

      document.getElementById('donutWrap').innerHTML = svg + `<div class="donut-legend">${legend}</div>`;
    }

    // ── AI CHAT ──
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');

    function addMessage(text, role) {
      const msg = document.createElement('div');
      msg.className = `msg ${role}`;
      const avatar = role === 'bot' ? '🌿' : '👤';
      msg.innerHTML = `
        <div class="msg-avatar">${avatar}</div>
        <div class="msg-bubble">${text}</div>
      `;
      chatMessages.appendChild(msg);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTyping() {
      const el = document.createElement('div');
      el.className = 'msg bot';
      el.id = 'typing';
      el.innerHTML = `
        <div class="msg-avatar">🌿</div>
        <div class="msg-bubble">
          <div class="typing-indicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
          </div>
        </div>
      `;
      chatMessages.appendChild(el);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTyping() {
      const el = document.getElementById('typing');
      if (el) el.remove();
    }

    async function sendChat() {
      const q = chatInput.value.trim();
      if (!q) return;
      chatInput.value = '';
      addMessage(q, 'user');
      showTyping();

      try {
        const res = await fetch('/api/chat', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ message: q })
        });
        const data = await res.json();
        hideTyping();
        addMessage(data.message || data.answer || 'Yanıt alınamadı.', 'bot');
      } catch {
        hideTyping();
        addMessage('Bağlantı hatası. Sunucu çalışıyor mu?', 'bot');
      }
    }

    document.getElementById('chatSend').onclick = sendChat;
    chatInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendChat(); });

    // ── INIT ──
    loadStats();
    renderDonut();
  </script>
</body>
</html>
