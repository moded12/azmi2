document.addEventListener('DOMContentLoaded', function() {
  // تحميل المواضيع عند بداية الصفحة
  loadThreads();

  // البحث أو الفلاتر
  document.getElementById('searchBtn').addEventListener('click', loadThreads);
  document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') loadThreads();
  });

  // تحديث النتائج تلقائياً عند تغيير الفلاتر
  document.getElementById('materialFilter').addEventListener('change', loadThreads);
  document.getElementById('classFilter').addEventListener('change', loadThreads);
  document.getElementById('semesterFilter').addEventListener('change', loadThreads);
  document.getElementById('groupFilter').addEventListener('change', loadThreads);
});

function showLoading(show) {
  document.getElementById('loadingSpinner').style.display = show ? 'flex' : 'none';
}

function loadThreads() {
  showLoading(true);
  const search = encodeURIComponent(document.getElementById('searchInput').value.trim());
  const material = encodeURIComponent(document.getElementById('materialFilter').value);
  const class_id = encodeURIComponent(document.getElementById('classFilter').value);
  const semester = encodeURIComponent(document.getElementById('semesterFilter').value);
  const group = encodeURIComponent(document.getElementById('groupFilter').value);
  let url = `api.php?action=list_threads&search=${search}&material=${material}&class_id=${class_id}&semester=${semester}&group=${group}`;

  fetch(url)
    .then(res => res.json())
    .then(data => {
      showLoading(false);
      if (data.status === 'success') {
        displayThreads(data.data);
      } else {
        document.getElementById('cardsArea').innerHTML = `<div class="alert alert-danger">فشل في تحميل البيانات!</div>`;
      }
    })
    .catch(() => {
      showLoading(false);
      document.getElementById('cardsArea').innerHTML = `<div class="alert alert-danger">خطأ في الاتصال بالخادم!</div>`;
    });
}

// عرض المواضيع مع الصور كسلايدر (carousel) وزر مشاركة
function displayThreads(threads) {
  // دمج الملفات المرتبطة لكل موضوع
  const threadsMap = {};
  threads.forEach(row => {
    if (!threadsMap[row.thread_id]) {
      threadsMap[row.thread_id] = {
        ...row,
        files: []
      };
    }
    if (row.file_path) {
      threadsMap[row.thread_id].files.push("https://www.shneler.com/azmi3/admin/" + row.file_path.replace(/^ *uploads\//, "uploads/"));
    }
  });

  const container = document.getElementById('cardsArea');
  container.innerHTML = '';

  Object.values(threadsMap).forEach((thread, idx) => {
    let filesHtml = '';
    if (thread.files.length === 1) {
      filesHtml = `<img src="${thread.files[0]}" alt="مرفق" class="w-100 mb-2" style="max-height:220px;object-fit:contain;border-radius:8px;">`;
    } else if (thread.files.length > 1) {
      const carouselId = 'carousel_' + thread.thread_id;
      filesHtml = `
        <div id="${carouselId}" class="carousel slide mb-2" data-bs-ride="carousel">
          <div class="carousel-inner">
            ${thread.files.map((file, i) => `
              <div class="carousel-item${i==0?' active':''}">
                <img src="${file}" class="d-block w-100" style="max-height:220px;object-fit:contain;border-radius:8px;" alt="مرفق ${i+1}">
              </div>
            `).join('')}
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">السابق</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">التالي</span>
          </button>
        </div>
      `;
    }

    // زر مشاركة: ينسخ رابط الموضوع (أو يمكنك تطويره لمشاركة واتساب أو غيره)
    let shareUrl = window.location.origin + window.location.pathname + `?thread=${thread.thread_id}`;
    let shareBtnHtml = `<button class="share-btn" onclick="copyToClipboard('${shareUrl}', this)"><i class="bi bi-share-fill"></i> مشاركة</button>`;

    container.innerHTML += `
      <div class="thread-card">
        <div class="thread-title">${thread.thread_title}</div>
        <div class="thread-desc">${thread.thread_description || ''}</div>
        <div class="thread-meta">
          <span><i class="bi bi-journal-text"></i> المادة: <b>${thread.material_name}</b></span> <br>
          <span><i class="bi bi-book"></i> الصف: <b>${thread.class_id}</b></span> &nbsp; 
          <span><i class="bi bi-calendar2-week"></i> الفصل: <b>${thread.semester_name}</b></span><br>
          <span><i class="bi bi-people"></i> المجموعة: <b>${thread.group_name}</b></span>
        </div>
        ${shareBtnHtml}
        ${filesHtml ? `<div>${filesHtml}</div>` : `<div class="p-2 text-danger">لا يوجد مرفقات</div>`}
      </div>
    `;
  });

  if (!Object.keys(threadsMap).length) {
    container.innerHTML = `<div class="alert alert-warning mt-3">لا يوجد مواضيع متاحة حالياً.</div>`;
  }
}

// زر نسخ الرابط (مشاركة)
window.copyToClipboard = function(text, btn) {
  navigator.clipboard.writeText(text).then(function() {
    btn.innerHTML = '<i class="bi bi-clipboard-check"></i> تم النسخ!';
    setTimeout(() => { btn.innerHTML = '<i class="bi bi-share-fill"></i> مشاركة'; }, 1800);
  });
}