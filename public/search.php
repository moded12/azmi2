<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>البحث عن المواضيع</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f7f9fc; }
    .thread-card { transition: 0.3s; }
    .thread-card:hover { transform: scale(1.02); box-shadow: 0 0 10px rgba(0,0,0,0.2); }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="text-center text-blue-900 mb-4">🔍 البحث عن المواضيع التعليمية</h2>

  <div class="row g-2 mb-4">
    <div class="col-md-2">
      <select id="class_id" class="form-select" onchange="fetchMaterials(); performSearch();">
        <option value="">اختر الصف</option>
      </select>
    </div>
    <div class="col-md-2">
      <select id="material_id" class="form-select" onchange="fetchGroups(); performSearch();">
        <option value="">اختر المادة</option>
      </select>
    </div>
    <div class="col-md-2">
      <select id="semester_id" class="form-select" onchange="fetchGroups(); performSearch();">
        <option value="">اختر الفصل</option>
      </select>
    </div>
    <div class="col-md-2">
      <select id="group_id" class="form-select" onchange="performSearch()">
        <option value="">اختر المجموعة</option>
      </select>
    </div>
    <div class="col-md-2">
      <select id="type" class="form-select" onchange="performSearch()">
        <option value="">نوع المحتوى</option>
        <option value="pdf">📄 PDF</option>
        <option value="image">🖼️ صورة</option>
        <option value="video">🎥 فيديو</option>
        <option value="doc">📃 Word</option>
        <option value="link">🔗 رابط خارجي</option>
      </select>
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-primary" onclick="performSearch()">بحث يدوي</button>
    </div>
  </div>

  <div id="results" class="mt-4">
    <div class="alert alert-info">يرجى اختيار الصف أولاً لبدء البحث.</div>
  </div>
</div>

<script>
  async function fetchSelect(url, elementId, defaultText) {
    const res = await fetch(url);
    const data = await res.json();
    const select = document.getElementById(elementId);
    select.innerHTML = `<option value="">${defaultText}</option>` + data.map(i => `<option value="${i.id}">${i.name}</option>`).join('');
  }

  async function fetchMaterials() {
    const classId = document.getElementById("class_id").value;
    if (!classId) return;
    await fetchSelect(`../api/materials.php?class_id=${classId}`, 'material_id', 'اختر المادة');
  }

  async function fetchGroups() {
    const matId = document.getElementById("material_id").value;
    const semId = document.getElementById("semester_id").value;
    if (!matId || !semId) return;
    await fetchSelect(`../api/groups.php?material_id=${matId}&semester_id=${semId}`, 'group_id', 'اختر المجموعة');
  }

  function performSearch() {
    const params = new URLSearchParams({
      class_id: document.getElementById("class_id").value,
      material_id: document.getElementById("material_id").value,
      semester_id: document.getElementById("semester_id").value,
      group_id: document.getElementById("group_id").value,
      type: document.getElementById("type").value
    });

    fetch(`search_threads.php?${params}`)
      .then(res => res.text())
      .then(html => {
        document.getElementById("results").innerHTML = html;
      });
  }

  window.onload = () => {
    fetchSelect('../api/classes.php', 'class_id', 'اختر الصف');
    fetchSelect('../api/semesters.php', 'semester_id', 'اختر الفصل');
  };
</script>

</body>
</html>