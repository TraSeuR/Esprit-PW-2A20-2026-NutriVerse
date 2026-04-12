
feather.replace();

// =========================
// MENU SIDEBAR
// =========================
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const closeSidebar = document.getElementById("closeSidebar");

if (menuBtn && sidebar) {
  menuBtn.addEventListener("click", () => {
    sidebar.classList.add("active");
  });
}

if (closeSidebar && sidebar) {
  closeSidebar.addEventListener("click", () => {
    sidebar.classList.remove("active");
  });
}

// =========================
// FADE-UP ANIMATION ON SCROLL
// =========================
const faders = document.querySelectorAll(".fade-up");

if (faders.length > 0) {
  const appearOnScroll = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("show");
        }
      });
    },
    {
      threshold: 0.15,
    }
  );

  faders.forEach((fader) => {
    appearOnScroll.observe(fader);
  });
}

// =========================
// SECTION SWITCHING (sidebar nav)
// =========================
const allSections = {
  dashboard: document.getElementById('section-dashboard'),
  utilisateurs: document.getElementById('section-utilisateurs'),
};

document.querySelectorAll('.menu-item[data-section]').forEach(function (link) {
  link.addEventListener('click', function (e) {
    e.preventDefault();

    // Update active state
    document.querySelectorAll('.menu-item').forEach(function (el) {
      el.classList.remove('active');
    });
    this.classList.add('active');

    const target = this.getAttribute('data-section');

    // Show the correct section, hide others
    Object.keys(allSections).forEach(function (key) {
      const el = allSections[key];
      if (!el) return;
      if (key === target) {
        el.style.display = 'block';
        // Re-trigger fade-up animations
        el.querySelectorAll('.fade-up').forEach(function (f) {
          f.classList.remove('show');
          setTimeout(function () { f.classList.add('show'); }, 50);
        });
        // Re-render feather icons inside the new section
        feather.replace();
      } else {
        el.style.display = 'none';
      }
    });

    // Close mobile sidebar
    if (sidebar) sidebar.classList.remove('active');
  });
});

// =========================
// UTILISATEURS – STATIC SAMPLE DATA
// Used to populate modals (replace with PHP/Ajax later)
// =========================
var usersData = [
  {
    prenom: 'Ali', nom: 'Ben Salah', email: 'ali.bensalah@nutriverse.tn',
    tel: '+216 71 000 001', dob: '05/04/1985', sexe: 'Homme',
    poids: '80 kg', taille: '182 cm', objectif: 'Maintien',
    pref: 'Omnivore', allergies: 'Aucune', role: 'admin',
    status: 'actif', inscription: '12 Jan 2025', initial: 'A', color: 'green'
  },
  {
    prenom: 'Sara', nom: 'Mejri', email: 'sara.mejri@email.com',
    tel: '+216 98 200 300', dob: '17/09/1999', sexe: 'Femme',
    poids: '58 kg', taille: '165 cm', objectif: 'Perte de poids',
    pref: 'Végétarien', allergies: 'Lactose', role: 'utilisateur',
    status: 'actif', inscription: '03 Fév 2025', initial: 'S', color: 'blue'
  },
  {
    prenom: 'Mohamed', nom: 'Trabelsi', email: 'm.trabelsi@gmail.com',
    tel: '+216 55 123 456', dob: '22/11/1993', sexe: 'Homme',
    poids: '75 kg', taille: '176 cm', objectif: 'Prise de masse',
    pref: 'Omnivore', allergies: 'Arachides', role: 'utilisateur',
    status: 'actif', inscription: '18 Fév 2025', initial: 'M', color: 'orange'
  },
  {
    prenom: 'Leila', nom: 'Chaabane', email: 'leila.chaabane@outlook.com',
    tel: '+216 22 987 654', dob: '30/06/2001', sexe: 'Femme',
    poids: '62 kg', taille: '168 cm', objectif: 'Amélioration santé',
    pref: 'Vegan', allergies: 'Gluten, Soja', role: 'utilisateur',
    status: 'desactive', inscription: '25 Mars 2025', initial: 'L', color: 'purple'
  },
  {
    prenom: 'Rania', nom: 'Gharbi', email: 'rania.gharbi@nutriverse.tn',
    tel: '+216 71 000 002', dob: '12/01/1990', sexe: 'Femme',
    poids: '65 kg', taille: '170 cm', objectif: 'Maintien',
    pref: 'Omnivore', allergies: 'Aucune', role: 'admin',
    status: 'actif', inscription: '01 Jan 2025', initial: 'R', color: 'green'
  },
  {
    prenom: 'Karim', nom: 'Nasri', email: 'k.nasri@hotmail.com',
    tel: '+216 99 654 321', dob: '08/03/1997', sexe: 'Homme',
    poids: '78 kg', taille: '179 cm', objectif: 'Augmenter l\'énergie',
    pref: 'Pescétarien', allergies: 'Lactose, Fruits de mer', role: 'utilisateur',
    status: 'desactive', inscription: '10 Avr 2025', initial: 'K', color: 'blue'
  }
];

// Track which user index is open in modal
var currentModalIndex = 0;

// =========================
// UTILISATEURS – MODAL HELPERS
// =========================
function openModal(id) {
  var overlay = document.getElementById(id);
  if (overlay) {
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    feather.replace();
  }
}

function closeModal(id) {
  var overlay = document.getElementById(id);
  if (overlay) {
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }
}

// Close on Escape key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeModal('modal-view');
    closeModal('modal-edit');
  }
});

// =========================
// UTILISATEURS – OPEN VIEW MODAL
// =========================
function openViewModal(index) {
  currentModalIndex = index;
  var u = usersData[index];
  if (!u) return;

  // Populate hero
  document.getElementById('mv-avatar').textContent = u.initial;
  document.getElementById('mv-avatar').className = 'u-modal-avatar u-avatar-' + u.color;
  document.getElementById('mv-name').textContent = u.prenom + ' ' + u.nom;

  var roleEl = document.getElementById('mv-role');
  roleEl.textContent = u.role === 'admin' ? 'Admin' : 'Utilisateur';
  roleEl.className = 'u-role-badge ' + (u.role === 'admin' ? 'role-admin' : 'role-user');

  var statusEl = document.getElementById('mv-status');
  statusEl.textContent = u.status === 'actif' ? 'Actif' : 'Désactivé';
  statusEl.className = 'status ' + (u.status === 'actif' ? 'delivered' : 'pending');

  // Populate detail fields
  document.getElementById('mv-prenom').textContent = u.prenom;
  document.getElementById('mv-nom').textContent = u.nom;
  document.getElementById('mv-email').textContent = u.email;
  document.getElementById('mv-tel').textContent = u.tel;
  document.getElementById('mv-dob').textContent = u.dob;
  document.getElementById('mv-sexe').textContent = u.sexe;
  document.getElementById('mv-poids').textContent = u.poids;
  document.getElementById('mv-taille').textContent = u.taille;
  document.getElementById('mv-objectif').textContent = u.objectif;
  document.getElementById('mv-pref').textContent = u.pref;
  document.getElementById('mv-allergies').textContent = u.allergies;
  document.getElementById('mv-inscription').textContent = u.inscription;

  openModal('modal-view');
}

// =========================
// UTILISATEURS – OPEN EDIT/ADD MODAL
// =========================
function openEditModal(index) {
  currentModalIndex = index;

  if (index === null || index === undefined || index < 0) {
    // ADD mode
    document.getElementById('me-title').textContent = 'Ajouter un utilisateur';
    document.getElementById('me-submit-label').textContent = 'Créer le compte';
    document.getElementById('form-edit-user').reset();
  } else {
    // EDIT mode
    var u = usersData[index];
    if (!u) return;
    document.getElementById('me-title').textContent = 'Modifier : ' + u.prenom + ' ' + u.nom;
    document.getElementById('me-submit-label').textContent = 'Enregistrer';

    document.getElementById('me-prenom').value = u.prenom;
    document.getElementById('me-nom').value = u.nom;
    document.getElementById('me-email').value = u.email;
    document.getElementById('me-password').value = '';
    document.getElementById('me-role').value = u.role;
    document.getElementById('me-etat').value = u.status;
    document.getElementById('me-tel').value = u.tel;
    document.getElementById('me-sexe').value = u.sexe;
    document.getElementById('me-poids').value = parseInt(u.poids);
    document.getElementById('me-taille').value = parseInt(u.taille);
    document.getElementById('me-objectif').value = u.objectif;
    document.getElementById('me-pref').value = u.pref;
    document.getElementById('me-allergies').value = u.allergies;
  }

  openModal('modal-edit');
}

// Wire "Ajouter" button to add modal
var btnAddUser = document.getElementById('btn-add-user');
if (btnAddUser) {
  btnAddUser.addEventListener('click', function () {
    openEditModal(-1);
  });
}

// =========================
// UTILISATEURS – LIVE SEARCH & FILTER
// =========================
function filterTable() {
  var search = (document.getElementById('u-search-input').value || '').toLowerCase();
  var role = (document.getElementById('u-filter-role').value || '').toLowerCase();
  var status = (document.getElementById('u-filter-status').value || '').toLowerCase();

  var rows = document.querySelectorAll('#users-tbody tr');
  var visible = 0;

  rows.forEach(function (row) {
    var text = row.textContent.toLowerCase();
    var rowRole = (row.getAttribute('data-role') || '').toLowerCase();
    var rowStatus = (row.getAttribute('data-status') || '').toLowerCase();

    var matchSearch = !search || text.includes(search);
    var matchRole = !role || rowRole === role;
    var matchStatus = !status || rowStatus === status;

    if (matchSearch && matchRole && matchStatus) {
      row.style.display = '';
      visible++;
    } else {
      row.style.display = 'none';
    }
  });

  // Update count label
  var countEl = document.getElementById('u-count-label');
  if (countEl) {
    countEl.textContent = visible + ' utilisateur' + (visible !== 1 ? 's' : '') + ' trouvé' + (visible !== 1 ? 's' : '');
  }

  // Show/hide empty state
  var emptyState = document.getElementById('u-empty-state');
  var tableEl = document.getElementById('users-table');
  if (emptyState && tableEl) {
    if (visible === 0) {
      tableEl.style.display = 'none';
      emptyState.style.display = 'block';
      feather.replace();
    } else {
      tableEl.style.display = '';
      emptyState.style.display = 'none';
    }
  }
}

var searchInput = document.getElementById('u-search-input');
var filterRole = document.getElementById('u-filter-role');
var filterStatus = document.getElementById('u-filter-status');

if (searchInput) searchInput.addEventListener('input', filterTable);
if (filterRole) filterRole.addEventListener('change', filterTable);
if (filterStatus) filterStatus.addEventListener('change', filterTable);

// Refresh button restores all rows
var btnRefresh = document.getElementById('btn-refresh-table');
if (btnRefresh) {
  btnRefresh.addEventListener('click', function () {
    if (searchInput) searchInput.value = '';
    if (filterRole) filterRole.value = '';
    if (filterStatus) filterStatus.value = '';
    filterTable();
  });
}