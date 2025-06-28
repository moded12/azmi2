// 📄 public/js/filter_dropdowns.js
function loadFilterOptions(baseApiUrl = '../api/') {
  // الصفوف
  fetch(`${baseApiUrl}classes.php`)
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('search-class');
      select.innerHTML = '<option value="">اختر الصف</option>';
      data.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        select.appendChild(opt);
      });
    });

  // الفصول
  fetch(`${baseApiUrl}semesters.php`)
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('search-semester');
      select.innerHTML = '<option value="">اختر الفصل</option>';
      data.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.id;
        opt.textContent = f.name;
        select.appendChild(opt);
      });
    });

  // المواد (يتم تحميلها بناءً على الصف)
  document.getElementById('search-class').addEventListener('change', () => {
    const classId = document.getElementById('search-class').value;
    fetch(`${baseApiUrl}subjects.php?id=${classId}`)
      .then(res => res.json())
      .then(data => {
        const select = document.getElementById('search-material');
        select.innerHTML = '<option value="">اختر المادة</option>';
        data.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m.id;
          opt.textContent = m.name;
          select.appendChild(opt);
        });
      });
  });

  // المجموعات (تُحمّل حسب المادة والفصل)
  document.getElementById('search-material').addEventListener('change', loadGroups);
  document.getElementById('search-semester').addEventListener('change', loadGroups);

  function loadGroups() {
    const materialId = document.getElementById('search-material').value;
    const semesterId = document.getElementById('search-semester').value;
    if (materialId && semesterId !== '') {
      fetch(`${baseApiUrl}groups.php?id=${materialId}&semester=${semesterId}`)
        .then(res => res.json())
        .then(data => {
          const select = document.getElementById('search-stage');
          select.innerHTML = '<option value="">اختر المجموعة</option>';
          data.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.name;
            select.appendChild(opt);
          });
        });
    }
  }
}