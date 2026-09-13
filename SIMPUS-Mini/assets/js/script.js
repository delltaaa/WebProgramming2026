window.onload = function () {
    if (document.getElementById('login-section')) {
        checkLoginStatus();
        setupNavToggle();
        setupLiveValidation();
        showFlashToastIfAny();
    } else {
        initSubpage();
    }
};

function showFlashToastIfAny() {
    const flash = localStorage.getItem('flashToast');
    if (!flash) return;
    localStorage.removeItem('flashToast');
    try {
        const { message, type } = JSON.parse(flash);
        showToast(message, type, 2500);
    } catch (err) { }
}

function getRootPath() {
    return '../';
}

function initSubpage() {
    const role = localStorage.getItem('role');
    const username = localStorage.getItem('username');

    if (!role) {
        window.location.href = getRootPath() + 'index.html';
        return;
    }

    if (document.body.getAttribute('data-restrict') === 'admin' && role !== 'admin') {
        window.location.href = getRootPath() + 'index.html';
        return;
    }

    const userDisplay = document.getElementById('user-display');
    if (userDisplay) userDisplay.innerText = `Halo, ${username}!`;

    if (role === 'anggota') {
        document.body.classList.add('role-anggota');
    }

    setupNavToggle();
    setupLogoutButton();
    setupTablePaginationAndSearch();
}

function setupLogoutButton() {
    document.querySelectorAll('[data-logout]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (!confirm('Yakin ingin keluar dari akun?')) return;

            localStorage.removeItem('role');
            localStorage.removeItem('username');

            localStorage.setItem('flashToast', JSON.stringify({
                message: 'Kamu sudah keluar dari akun.',
                type: 'info'
            }));

            window.location.href = getRootPath() + 'index.html';
        });
    });
}

function setupTablePaginationAndSearch() {
    const table = document.querySelector(".data-table");
    if (!table) return;

    const tbody = table.querySelector("tbody");
    if (!tbody) return;

    const rowsPerPage = 5;
    let currentPage = 1;
    const allRows = Array.from(tbody.querySelectorAll("tr"));
    const emptyState = table.closest('.table-card')?.querySelector('.table-empty-state');
    const searchInput = document.querySelector('.search-box input');
    const pageInfo = document.getElementById("pagination-info");
    const pageControls = document.getElementById("pagination-controls");

    let filteredRows = [...allRows];

    function renderTable() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        allRows.forEach(row => row.style.display = "none");

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        const rowsToDisplay = filteredRows.slice(start, end);
        rowsToDisplay.forEach(row => row.style.display = "");

        if (emptyState) {
            emptyState.style.display = totalRows === 0 ? "block" : "none";
        }

        if (pageInfo) {
            if (totalRows === 0) {
                pageInfo.textContent = "Menampilkan 0 data";
            } else {
                const currentStart = start + 1;
                const currentEnd = Math.min(end, totalRows);
                pageInfo.textContent = `Menampilkan ${currentStart}-${currentEnd} dari ${totalRows} data`;
            }
        }

        if (pageControls) {
            renderControls(totalPages);
        }
    }

    function renderControls(totalPages) {
        pageControls.innerHTML = "";

        const prevBtn = document.createElement("button");
        prevBtn.className = "pagination-btn";
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        prevBtn.disabled = currentPage === 1 || filteredRows.length === 0;
        prevBtn.addEventListener("click", () => {
            currentPage--;
            renderTable();
        });
        pageControls.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.addEventListener("click", () => {
                currentPage = i;
                renderTable();
            });
            pageControls.appendChild(btn);
        }

        const nextBtn = document.createElement("button");
        nextBtn.className = "pagination-btn";
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        nextBtn.disabled = currentPage === totalPages || filteredRows.length === 0;
        nextBtn.addEventListener("click", () => {
            currentPage++;
            renderTable();
        });
        pageControls.appendChild(nextBtn);
    }

    if (searchInput) {
        searchInput.addEventListener("input", function (e) {
            const term = e.target.value.toLowerCase().trim();
            filteredRows = allRows.filter(row => row.textContent.toLowerCase().includes(term));
            currentPage = 1;
            renderTable();
        });
    }

    renderTable();
}

function showToast(message, type = 'info', timeout = 4000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const icons = {
        error: 'fa-solid fa-circle-exclamation',
        success: 'fa-solid fa-circle-check',
        info: 'fa-solid fa-circle-info'
    };

    toast.innerHTML = `
        <i class="toast-icon ${icons[type] || icons.info}"></i>
        <span class="toast-msg"></span>
        <button class="toast-close" aria-label="Tutup notifikasi">&times;</button>
    `;
    toast.querySelector('.toast-msg').innerText = message;

    const remove = () => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 250);
    };

    toast.querySelector('.toast-close').addEventListener('click', remove);
    container.appendChild(toast);

    if (timeout) setTimeout(remove, timeout);
}

function setFieldError(groupId, hasError) {
    const group = document.getElementById(groupId);
    if (group) group.classList.toggle('has-error', hasError);
}

function setupLiveValidation() {
    document.getElementById('username')?.addEventListener('input', (e) => {
        if (e.target.value.trim() !== '') setFieldError('group-username', false);
    });
    document.getElementById('password')?.addEventListener('input', (e) => {
        if (e.target.value.trim() !== '') setFieldError('group-password', false);
    });
}

function prosesLogin() {
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const user = usernameInput.value.trim().toLowerCase();
    const pass = passwordInput.value.trim();

    const usernameEmpty = user === '';
    const passwordEmpty = pass === '';

    setFieldError('group-username', usernameEmpty);
    setFieldError('group-password', passwordEmpty);

    if (usernameEmpty || passwordEmpty) {
        shakeLoginCard();
        showToast('Username dan password wajib diisi dulu ya.', 'error');
        return;
    }

    if (user === 'admin') {
        localStorage.setItem('role', 'admin');
        localStorage.setItem('username', 'Administrator');
        showToast('Berhasil masuk sebagai Administrator.', 'success', 2500);
        animateTransitionToDashboard();
    } else if (user === 'siswa' || user === 'anggota') {
        localStorage.setItem('role', 'anggota');
        localStorage.setItem('username', 'Siswa / Anggota');
        showToast('Berhasil masuk sebagai Anggota.', 'success', 2500);
        animateTransitionToDashboard();
    } else {
        shakeLoginCard();
        showToast('Username tidak ditemukan. Coba "admin" atau "siswa".', 'error');
    }
}

function checkLoginStatus() {
    const role = localStorage.getItem('role');
    const username = localStorage.getItem('username');

    const loginSection = document.getElementById('login-section');
    const dashboardSection = document.getElementById('dashboard-section');

    const navAdmin = document.getElementById('nav-admin');
    const navAnggota = document.getElementById('nav-anggota');

    const contentAdmin = document.getElementById('content-admin');
    const contentAnggota = document.getElementById('content-anggota');

    if (role) {
        loginSection?.classList.add('hidden');
        dashboardSection?.classList.remove('hidden');

        const userDisplay = document.getElementById('user-display');
        if (userDisplay) userDisplay.innerText = `Halo, ${username}!`;

        if (role === 'admin') {
            navAdmin?.classList.remove('hidden');
            contentAdmin?.classList.remove('hidden');

            navAnggota?.classList.add('hidden');
            contentAnggota?.classList.add('hidden');

            document.getElementById('hero-title').innerText = "Panel Kontrol Admin";
            document.getElementById('hero-desc').innerText = "Kelola katalog buku, data anggota, dan peminjaman dengan lebih mudah.";

            if (contentAdmin) replayCardAnimation(contentAdmin);

        } else if (role === 'anggota') {
            navAnggota?.classList.remove('hidden');
            contentAnggota?.classList.remove('hidden');

            navAdmin?.classList.add('hidden');
            contentAdmin?.classList.add('hidden');

            document.getElementById('hero-title').innerText = "Selamat Datang Kembali!";
            document.getElementById('hero-desc').innerText = "Cari buku favoritmu dan pantau batas waktu peminjaman di sini.";

            if (contentAnggota) replayCardAnimation(contentAnggota);
        }
    } else {
        loginSection?.classList.remove('hidden');
        dashboardSection?.classList.add('hidden');
    }
}

function prosesLogout() {
    if (!confirm('Yakin ingin keluar dari akun?')) return;

    const dashboardSection = document.getElementById('dashboard-section');
    dashboardSection.style.transition = 'opacity 0.25s ease';
    dashboardSection.style.opacity = '0';

    setTimeout(() => {
        localStorage.removeItem('role');
        localStorage.removeItem('username');
        checkLoginStatus();
        dashboardSection.style.opacity = '';
        dashboardSection.style.transition = '';

        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        if (usernameInput) usernameInput.value = '';
        if (passwordInput) passwordInput.value = '';

        setFieldError('group-username', false);
        setFieldError('group-password', false);

        showToast('Kamu sudah keluar dari akun.', 'info', 2500);
    }, 220);
}

function shakeLoginCard() {
    const wrapper = document.getElementById('login-wrapper');
    if (!wrapper) return;
    wrapper.classList.remove('shake');
    void wrapper.offsetWidth;
    wrapper.classList.add('shake');
}

function animateTransitionToDashboard() {
    const loginSection = document.getElementById('login-section');
    if (!loginSection) return;
    loginSection.style.transition = 'opacity 0.3s ease';
    loginSection.style.opacity = '0';
    setTimeout(() => {
        checkLoginStatus();
        loginSection.style.opacity = '';
        loginSection.style.transition = '';
    }, 280);
}

function replayCardAnimation(gridEl) {
    const cards = gridEl.querySelectorAll('.card');
    cards.forEach((card) => {
        card.style.animation = 'none';
        void card.offsetWidth;
        card.style.animation = '';
    });
}

function setupNavToggle() {
    const toggle = document.getElementById('nav-toggle');
    const nav = document.getElementById('main-nav');

    if (!toggle || !nav) return;

    toggle.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('nav-open');
        toggle.classList.toggle('open', isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    nav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            nav.classList.remove('nav-open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
}

function goHome() {
    document.getElementById('notfound-section')?.classList.add('hidden');
    checkLoginStatus();
}
