require('dotenv').config();
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

const ADMIN_USER = process.env.ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.ADMIN_PASS || 'admin123';
const OPENAI_API_KEY = process.env.OPENAI_API_KEY || '';

// MySQL bağlantı havuzu
const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  port: process.env.DB_PORT || 3306,
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'pestisit_proje',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// Veritabanı bağlantı testi
async function initializeDatabase() {
  try {
    const connection = await pool.getConnection();
    console.log('MySQL veritabanına bağlanıldı: pestisit_proje');
    connection.release();
  } catch (err) {
    console.error('Veritabanı bağlantı hatası:', err);
    process.exit(1);
  }
}

// Admin kimlik doğrulama
function adminAuth(req, res, next) {
  const authHeader = req.headers.authorization || '';
  if (!authHeader.startsWith('Basic ')) {
    res.set('WWW-Authenticate', 'Basic realm="Admin Area"');
    return res.status(401).json({ error: 'Yetkisiz erişim' });
  }

  const base64 = authHeader.slice(6);
  const [username, password] = Buffer.from(base64, 'base64').toString('utf8').split(':');
  if (username === ADMIN_USER && password === ADMIN_PASS) {
    return next();
  }

  res.set('WWW-Authenticate', 'Basic realm="Admin Area"');
  return res.status(401).json({ error: 'Yetkisiz erişim' });
}

// Middleware
app.use(cors());
app.use(express.json());
app.use((req, res, next) => {
  res.set('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate');
  res.set('Pragma', 'no-cache');
  res.set('Expires', '0');
  next();
});
app.use(express.static('.'));

// Root route
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'test.html'));
});

// -----------------------------------------------
// PUBLIC API ROUTES
// -----------------------------------------------

// Tüm kategorileri getir
app.get('/api/kategoriler', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM kategoriler ORDER BY kategori_id');
    res.json({ kategoriler: rows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Tüm etken maddeleri getir (kategori adıyla birlikte)
app.get('/api/pesticides', async (req, res) => {
  try {
    const sql = `
      SELECT 
        e.madde_id as id,
        e.madde_adi as name,
        k.kategori_adi as type,
        e.kimyasal_grup,
        e.molekuler_formul,
        e.genel_bilgi
      FROM etken_maddeler e
      JOIN kategoriler k ON e.kategori_id = k.kategori_id
      ORDER BY e.madde_id
    `;
    const [rows] = await pool.query(sql);
    res.json({ pesticides: rows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Tek bir etken maddenin tüm detayları
app.get('/api/pesticides/:id', async (req, res) => {
  const id = req.params.id;
  try {
    // Ana bilgi
    const [madde] = await pool.query(`
      SELECT e.*, k.kategori_adi 
      FROM etken_maddeler e
      JOIN kategoriler k ON e.kategori_id = k.kategori_id
      WHERE e.madde_id = ?
    `, [id]);

    if (madde.length === 0) {
      return res.status(404).json({ error: 'Pestisit bulunamadı' });
    }

    // Etki mekanizmaları
    const [mekanizmalar] = await pool.query(
      'SELECT mekanizma_tanimi FROM etki_mekanizmalari WHERE madde_id = ?', [id]
    );

    // Hedef hastalıklar
    const [hastaliklar] = await pool.query(
      'SELECT hastalik_zararli_adi FROM hedef_hastaliklar_zararlilar WHERE madde_id = ?', [id]
    );

    // Kullanım alanları
    const [kullanim] = await pool.query(
      'SELECT alan_tanimi FROM kullanim_alanlari WHERE madde_id = ?', [id]
    );

    res.json({
      pesticide: madde[0],
      mekanizmalar: mekanizmalar.map(m => m.mekanizma_tanimi),
      hastaliklar: hastaliklar.map(h => h.hastalik_zararli_adi),
      kullanim_alanlari: kullanim.map(k => k.alan_tanimi)
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Arama endpoint'i
app.get('/api/search', async (req, res) => {
  const query = (req.query.q || '').toLowerCase().trim();
  if (!query) {
    return res.json({ pesticides: [] });
  }

  try {
    const sql = `
      SELECT 
        e.madde_id as id,
        e.madde_adi as name,
        k.kategori_adi as type,
        e.kimyasal_grup,
        e.genel_bilgi
      FROM etken_maddeler e
      JOIN kategoriler k ON e.kategori_id = k.kategori_id
      WHERE 
        LOWER(e.madde_adi) LIKE ? OR
        LOWER(e.kimyasal_grup) LIKE ? OR
        LOWER(e.genel_bilgi) LIKE ? OR
        LOWER(k.kategori_adi) LIKE ?
      ORDER BY e.madde_id
      LIMIT 10
    `;
    const like = `%${query}%`;
    const [rows] = await pool.query(sql, [like, like, like, like]);
    res.json({ pesticides: rows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// AI soru-cevap endpoint'i
app.post('/api/ai', async (req, res) => {
  const question = (req.body.question || '').trim();
  if (!question) {
    return res.json({ answer: 'Lütfen bir soru giriniz.' });
  }

  try {
    const like = `%${question.toLowerCase()}%`;
    const [rows] = await pool.query(`
      SELECT 
        e.madde_adi as name,
        k.kategori_adi as type,
        e.genel_bilgi,
        GROUP_CONCAT(DISTINCT h.hastalik_zararli_adi SEPARATOR ', ') as hastaliklar,
        GROUP_CONCAT(DISTINCT m.mekanizma_tanimi SEPARATOR '. ') as mekanizmalar
      FROM etken_maddeler e
      JOIN kategoriler k ON e.kategori_id = k.kategori_id
      LEFT JOIN hedef_hastaliklar_zararlilar h ON e.madde_id = h.madde_id
      LEFT JOIN etki_mekanizmalari m ON e.madde_id = m.madde_id
      WHERE LOWER(e.madde_adi) LIKE ? OR LOWER(k.kategori_adi) LIKE ? OR LOWER(e.genel_bilgi) LIKE ?
      GROUP BY e.madde_id
      LIMIT 1
    `, [like, like, like]);

    if (rows.length === 0) {
      return res.json({ answer: 'Sorduğunuz kriterle eşleşen pestisit bulunamadı.' });
    }

    const item = rows[0];
    res.json({
      answer: `${item.name} (${item.type}): ${item.genel_bilgi} Hedef hastalıklar: ${item.hastaliklar || 'bilgi yok'}. Etki mekanizması: ${item.mekanizmalar || 'bilgi yok'}.`
    });
  } catch (err) {
    res.status(500).json({ answer: 'Veritabanı hatası oluştu.' });
  }
});

// AI Chat endpoint'i (OpenAI)
app.post('/api/chat', async (req, res) => {
  const userMessage = (req.body.message || '').trim();
  if (!userMessage) {
    return res.status(400).json({ error: 'Mesaj boş olamaz.' });
  }

  if (!OPENAI_API_KEY) {
    return res.status(500).json({ error: 'OpenAI API anahtarı ayarlı değil.' });
  }

  try {
    const like = `%${userMessage.toLowerCase()}%`;
    const [rows] = await pool.query(`
      SELECT 
        e.madde_adi as name,
        k.kategori_adi as type,
        e.genel_bilgi,
        GROUP_CONCAT(DISTINCT h.hastalik_zararli_adi SEPARATOR ', ') as hastaliklar,
        GROUP_CONCAT(DISTINCT m.mekanizma_tanimi SEPARATOR '. ') as mekanizmalar,
        GROUP_CONCAT(DISTINCT u.alan_tanimi SEPARATOR ', ') as kullanim
      FROM etken_maddeler e
      JOIN kategoriler k ON e.kategori_id = k.kategori_id
      LEFT JOIN hedef_hastaliklar_zararlilar h ON e.madde_id = h.madde_id
      LEFT JOIN etki_mekanizmalari m ON e.madde_id = m.madde_id
      LEFT JOIN kullanim_alanlari u ON e.madde_id = u.madde_id
      WHERE LOWER(e.madde_adi) LIKE ? OR LOWER(k.kategori_adi) LIKE ? OR LOWER(e.genel_bilgi) LIKE ?
      GROUP BY e.madde_id
      LIMIT 4
    `, [like, like, like]);

    const context = rows.length > 0
      ? rows.map((r, i) => `#${i+1}: ${r.name} (${r.type}). ${r.genel_bilgi} Hedefler: ${r.hastaliklar}. Mekanizma: ${r.mekanizmalar}. Kullanım: ${r.kullanim}.`).join('\n')
      : 'Veritabanında ilgili kayıt bulunamadı.';

    const response = await fetch('https://api.openai.com/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${OPENAI_API_KEY}`
      },
      body: JSON.stringify({
        model: 'gpt-4o-mini',
        messages: [
          {
            role: 'system',
            content: 'Sen tarım kimyası ve pestisit veritabanı konusunda uzman bir asistansın. Cevabını kısa, Türkçe ve anlaşılır ver.'
          },
          {
            role: 'assistant',
            content: `Veritabanı bilgileri:\n${context}`
          },
          {
            role: 'user',
            content: userMessage
          }
        ],
        max_tokens: 320,
        temperature: 0.45
      })
    });

    if (!response.ok) {
      throw new Error('OpenAI hatası');
    }

    const data = await response.json();
    const answer = data?.choices?.[0]?.message?.content?.trim() || 'Yanıt alınamadı.';
    res.json({ message: answer });
  } catch (error) {
    console.error('OpenAI hatası:', error);
    res.status(500).json({ error: 'AI servisi şu an kullanılamıyor.' });
  }
});

// -----------------------------------------------
// ADMIN API ROUTES
// -----------------------------------------------

app.use('/api/admin', adminAuth);

// Admin - tüm pestisitleri listele
app.get('/api/admin/pesticides', async (req, res) => {
  try {
    const [rows] = await pool.query(`
      SELECT 
        e.madde_id as id,
        e.madde_adi as name,
        k.kategori_adi as type,
        e.kimyasal_grup,
        e.molekuler_formul,
        e.genel_bilgi
      FROM etken_maddeler e
      JOIN kategoriler k ON e.kategori_id = k.kategori_id
      ORDER BY e.madde_id DESC
    `);
    res.json({ pesticides: rows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin - istatistikler
app.get('/api/admin/stats', async (req, res) => {
  try {
    const [[total]] = await pool.query('SELECT COUNT(*) as total FROM etken_maddeler');
    const [[types]] = await pool.query('SELECT COUNT(*) as types FROM kategoriler');
    const [[fungisit]] = await pool.query('SELECT COUNT(*) as count FROM etken_maddeler e JOIN kategoriler k ON e.kategori_id = k.kategori_id WHERE k.kategori_adi LIKE "%Fungisit%"');
    const [[nematisit]] = await pool.query('SELECT COUNT(*) as count FROM etken_maddeler e JOIN kategoriler k ON e.kategori_id = k.kategori_id WHERE k.kategori_adi LIKE "%Nematisit%"');
    const [[bakterisit]] = await pool.query('SELECT COUNT(*) as count FROM etken_maddeler e JOIN kategoriler k ON e.kategori_id = k.kategori_id WHERE k.kategori_adi LIKE "%Bakterisit%"');

    res.json({
      total: total.total,
      types: types.types,
      fungisit: fungisit.count,
      nematisit: nematisit.count,
      bakterisit: bakterisit.count
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin - analitik
app.get('/api/admin/analytics', async (req, res) => {
  try {
    const [riskDistribution] = await pool.query(`
      SELECT k.kategori_adi as risk_level, COUNT(*) as count
      FROM etken_maddeler e
      JOIN kategoriler k ON e.kategori_id = k.kategori_id
      GROUP BY k.kategori_id
    `);

    const total = riskDistribution.reduce((sum, row) => sum + row.count, 0);
    res.json({
      riskDistribution: riskDistribution.map(row => ({
        risk_level: row.risk_level,
        count: row.count,
        percentage: total > 0 ? Math.round((row.count / total) * 100) : 0
      }))
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin - yeni pestisit ekle
app.post('/api/admin/pesticides', async (req, res) => {
  const { madde_adi, kategori_id, molekuler_formul, kimyasal_grup, genel_bilgi } = req.body;

  if (!madde_adi || !kategori_id) {
    return res.status(400).json({ error: 'madde_adi ve kategori_id zorunludur' });
  }

  try {
    const sql = 'INSERT INTO etken_maddeler (madde_adi, kategori_id, molekuler_formul, kimyasal_grup, genel_bilgi) VALUES (?, ?, ?, ?, ?)';
    const [result] = await pool.query(sql, [madde_adi, kategori_id, molekuler_formul, kimyasal_grup, genel_bilgi]);
    res.json({ id: result.insertId, message: 'Pestisit eklendi' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin - pestisit güncelle
app.put('/api/admin/pesticides/:id', async (req, res) => {
  const id = req.params.id;
  const { madde_adi, kategori_id, molekuler_formul, kimyasal_grup, genel_bilgi } = req.body;

  if (!madde_adi || !kategori_id) {
    return res.status(400).json({ error: 'madde_adi ve kategori_id zorunludur' });
  }

  try {
    const sql = 'UPDATE etken_maddeler SET madde_adi=?, kategori_id=?, molekuler_formul=?, kimyasal_grup=?, genel_bilgi=? WHERE madde_id=?';
    const [result] = await pool.query(sql, [madde_adi, kategori_id, molekuler_formul, kimyasal_grup, genel_bilgi, id]);

    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'Pestisit bulunamadı' });
    }
    res.json({ message: 'Pestisit güncellendi' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin - pestisit sil
app.delete('/api/admin/pesticides/:id', async (req, res) => {
  const id = req.params.id;

  try {
    const [result] = await pool.query('DELETE FROM etken_maddeler WHERE madde_id=?', [id]);

    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'Pestisit bulunamadı' });
    }
    res.json({ message: 'Pestisit silindi' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Sunucuyu başlat
async function startServer() {
  await initializeDatabase();
  app.listen(PORT, () => {
    console.log(`Server http://localhost:${PORT} adresinde çalışıyor`);
  });
}

startServer().catch(err => {
  console.error('Sunucu başlatma hatası:', err);
  process.exit(1);
});