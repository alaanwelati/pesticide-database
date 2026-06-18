const mysql = require("mysql2/promise");

function fixEncoding(str) {
  if (!str) return str;
  try {
    return Buffer.from(str, "binary")
      .toString("binary")
      .split("")
      .map(c => c.charCodeAt(0))
      .length; // placeholder, real logic below
  } catch (e) {
    return str;
  }
}

// الإصلاح الحقيقي: النص كان UTF-8 صحيح، انقرا بـ cp437، واتخزن كـ UTF-8.
// نعكس العملية: ناخذ كل حرف (code point) ونلاقي بايته الأصلي بـ cp437، ونجمع البايتات ونفك ترميزها كـ UTF-8.

const iconv = require("iconv-lite");

function fix(str) {
  if (!str) return str;
  try {
    const cp437Bytes = iconv.encode(str, "cp437");
    return iconv.decode(cp437Bytes, "utf8");
  } catch (e) {
    return str;
  }
}

async function run() {
  const connection = await mysql.createConnection({
    host: "caboose.proxy.rlwy.net",
    port: 46760,
    user: "root",
    password: "LOrOUSRVrdSKLPOCnPrQTozeNTpAOCqT",
    database: "railway",
    charset: "utf8mb4"
  });

  console.log("Connected.");

  // 1. etken_maddeler
  const [maddeler] = await connection.query("SELECT madde_id, madde_adi, molekuler_formul, kimyasal_grup, genel_bilgi FROM etken_maddeler");
  for (const row of maddeler) {
    await connection.query(
      "UPDATE etken_maddeler SET madde_adi=?, molekuler_formul=?, kimyasal_grup=?, genel_bilgi=? WHERE madde_id=?",
      [fix(row.madde_adi), fix(row.molekuler_formul), fix(row.kimyasal_grup), fix(row.genel_bilgi), row.madde_id]
    );
  }
  console.log("Fixed etken_maddeler:", maddeler.length);

  // 2. kategoriler
  const [kats] = await connection.query("SELECT kategori_id, kategori_adi FROM kategoriler");
  for (const row of kats) {
    await connection.query("UPDATE kategoriler SET kategori_adi=? WHERE kategori_id=?", [fix(row.kategori_adi), row.kategori_id]);
  }
  console.log("Fixed kategoriler:", kats.length);

  // 3. hedef_hastaliklar_zararlilar
  const [hastaliklar] = await connection.query("SELECT hastalik_id, hastalik_zararli_adi FROM hedef_hastaliklar_zararlilar");
  for (const row of hastaliklar) {
    await connection.query("UPDATE hedef_hastaliklar_zararlilar SET hastalik_zararli_adi=? WHERE hastalik_id=?", [fix(row.hastalik_zararli_adi), row.hastalik_id]);
  }
  console.log("Fixed hedef_hastaliklar_zararlilar:", hastaliklar.length);

  // 4. etki_mekanizmalari
  const [mekanizmalar] = await connection.query("SELECT mekanizma_id, mekanizma_tanimi FROM etki_mekanizmalari");
  for (const row of mekanizmalar) {
    await connection.query("UPDATE etki_mekanizmalari SET mekanizma_tanimi=? WHERE mekanizma_id=?", [fix(row.mekanizma_tanimi), row.mekanizma_id]);
  }
  console.log("Fixed etki_mekanizmalari:", mekanizmalar.length);

  // 5. kullanim_alanlari
  const [alanlar] = await connection.query("SELECT alan_id, alan_tanimi FROM kullanim_alanlari");
  for (const row of alanlar) {
    await connection.query("UPDATE kullanim_alanlari SET alan_tanimi=? WHERE alan_id=?", [fix(row.alan_tanimi), row.alan_id]);
  }
  console.log("Fixed kullanim_alanlari:", alanlar.length);

  console.log("ALL DONE!");
  await connection.end();
}

run().catch(err => console.error("Error:", err.message));