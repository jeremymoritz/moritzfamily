function renderCast(cast) {
  const gallery = document.querySelector('.gallery');
  gallery.innerHTML = cast
    .map(p => {
      const id = String(p.auditionId).padStart(3, '0');
      const name = [p.firstName, p.lastName].filter(Boolean).join(' ');
      const groupsFiltered = p.groups.filter(g => g !== 'Leads');
      const groupsLine = groupsFiltered.length
        ? `<p class="gallery-groups">${groupsFiltered.join(' / ')}</p>`
        : '';
      const rolesLine = p.roles.length
        ? `<p class="gallery-roles">${p.roles.join(' / ')}</p>`
        : '';

      return `<li class="gallery-item">
        <div class="gallery-img">
          <img src="img/2026/auditions/aud-${id}.jpg" alt="Audition Photo ${id}">
        </div>
        <div class="card-text">
          <input type="checkbox" id="reveal-${p.auditionId}" class="reveal-cb">
          <div class="card-content">
            <p class="gallery-name">${name}<small>${p.auditionId}</small></p>
            ${groupsLine}
            ${rolesLine}
          </div>
          <label for="reveal-${p.auditionId}" class="ellipsis-btn">&hellip;</label>
        </div>
      </li>`;
    })
    .join('');
}

function shuffle(arr) {
  const a = [...arr];

  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }

  return a;
}

fetch('bbb-cast.json')
  .then(r => r.json())
  .then(data => {
    const fullCast = data.cast.slice();
    let cast = data.cast.slice();
    renderCast(cast);
    document.querySelector('.shuffle-btn').addEventListener('click', () => {
      cast = shuffle(cast);
      renderCast(cast);
    });
    document.querySelector('.next-round-btn').addEventListener('click', () => {
      const revealedIds = [...document.querySelectorAll('.reveal-cb:checked')].map(
        cb => Number(cb.id.replace(/^reveal-/, '')),
      );
      if (revealedIds.length === 0) {
        cast = fullCast.slice();
      } else {
        const idSet = new Set(revealedIds);
        cast = cast.filter(p => idSet.has(p.auditionId));
      }
      renderCast(cast);
    });
    document.getElementById('hide-names').addEventListener('change', e => {
      if (e.target.checked) return;
      cast = fullCast.slice();
      renderCast(cast);
    });
  })
  .catch(error => {
    console.error('Error fetching cast data:', error);
  });
