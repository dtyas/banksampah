    const pageMeta = {
      dashboard: {
        title: "Dashboard",
        subtitle: "Ringkasan operasional bank sampah hari ini.",
      },
      nasabah: {
        title: "Data Nasabah",
        subtitle: "Kelola data nasabah bank sampah dengan rapi dan terstruktur.",
      },
      nasabahForm: {
        title: "Data Nasabah",
        subtitle: "Tambahkan data nasabah baru melalui form input berikut.",
      },
      jenisSampah: {
        title: "Kategori Sampah",
        subtitle: "Kelola daftar kategori sampah yang tersedia di bank sampah.",
      },
      jenisSampahForm: {
        title: "Kategori Sampah",
        subtitle: "Tambahkan data kategori sampah baru melalui form input berikut.",
      },
      sampah: {
        title: "Data Sampah",
        subtitle: "Kelola data sampah dan harga per kilogram di bank sampah.",
      },
      sampahForm: {
        title: "Data Sampah",
        subtitle: "Tambahkan data sampah baru melalui form input berikut.",
      },
      transaksi: {
        title: "Transaksi",
        subtitle: "Kelola daftar transaksi setoran sampah nasabah.",
      },
      transaksiForm: {
        title: "Transaksi",
        subtitle: "Tambahkan data transaksi baru melalui form input berikut.",
      },
      pembayaran: {
        title: "Pembayaran",
        subtitle: "Kelola pencairan saldo nasabah melalui cash atau e-wallet.",
      },
      pembayaranForm: {
        title: "Pembayaran",
        subtitle: "Proses pencairan saldo nasabah melalui cash atau e-wallet.",
      },
      pencairanSaldo: {
        title: "Pencairan Saldo",
        subtitle: "Ajukan pencairan saldo nasabah dan pantau riwayat pengajuannya.",
      },
      user: {
        title: "User",
        subtitle: "Kelola akun super admin, petugas bank sampah, dan akses nasabah.",
      },
      userForm: {
        title: "Create User",
        subtitle: "Tambahkan user baru dan atur akses menu serta fitur secara manual.",
      },
      laporan: {
        title: "Laporan",
        subtitle: "Rekap transaksi, setoran sampah, saldo, dan pencairan nasabah.",
      },
    };

    const navLinks = document.querySelectorAll(".nav-link");
    const pageSections = document.querySelectorAll(".page-section");
    const loginPage = document.getElementById("loginPage");
    const appShell = document.getElementById("appShell");
    const loginForm = document.getElementById("loginForm");
    const loginEmail = document.getElementById("loginEmail");
    const loginPassword = document.getElementById("loginPassword");
    const loginError = document.getElementById("loginError");
    const logoutButton = document.getElementById("logoutButton");
    const openProfilePanel = document.getElementById("openProfilePanel");
    const closeProfilePanel = document.getElementById("closeProfilePanel");
    const profilePanelOverlay = document.getElementById("profilePanelOverlay");
    const profileForm = document.getElementById("profileForm");
    const profileName = document.getElementById("profileName");
    const profileRole = document.getElementById("profileRole");
    const profileAvatar = document.getElementById("profileAvatar");
    const profilePreview = document.getElementById("profilePreview");
    const profileNameInput = document.getElementById("profileNameInput");
    const profileRoleInput = document.getElementById("profileRoleInput");
    const profilePhotoInput = document.getElementById("profilePhotoInput");
    const resetProfilePhoto = document.getElementById("resetProfilePhoto");
    const pageTitle = document.getElementById("pageTitle");
    const pageSubtitle = document.getElementById("pageSubtitle");
    const openNasabahForm = document.getElementById("openNasabahForm");
    const backToNasabahList = document.getElementById("backToNasabahList");
    const saveNasabah = document.getElementById("saveNasabah");
    const idNasabah = document.getElementById("idNasabah");
    const namaNasabah = document.getElementById("namaNasabah");
    const alamatNasabah = document.getElementById("alamatNasabah");
    const noHpNasabah = document.getElementById("noHpNasabah");
    const tanggalDaftar = document.getElementById("tanggalDaftar");
    const nasabahRows = document.querySelectorAll(".nasabah-row");
    const openJenisSampahForm = document.getElementById("openJenisSampahForm");
    const backToJenisSampahList = document.getElementById("backToJenisSampahList");
    const saveJenisSampah = document.getElementById("saveJenisSampah");
    const idJenis = document.getElementById("idJenis");
    const namaJenis = document.getElementById("namaJenis");
    const kategoriRows = document.querySelectorAll(".kategori-row");
    const openSampahForm = document.getElementById("openSampahForm");
    const backToSampah = document.getElementById("backToSampah");
    const saveSampah = document.getElementById("saveSampah");
    const idSampah = document.getElementById("idSampah");
    const namaSampah = document.getElementById("namaSampah");
    const hargaSampah = document.getElementById("hargaSampah");
    const jenisSampahSelect = document.getElementById("jenisSampahSelect");
    const sampahRows = document.querySelectorAll(".sampah-row");
    const openTransaksiForm = document.getElementById("openTransaksiForm");
    const backToTransaksiList = document.getElementById("backToTransaksiList");
    const saveTransaksi = document.getElementById("saveTransaksi");
    const addTransactionItem = document.getElementById("addTransactionItem");
    const transactionItems = document.getElementById("transactionItems");
    const searchNasabahTransaksi = document.getElementById("searchNasabahTransaksi");
    const nasabahTransaksiResults = document.getElementById("nasabahTransaksiResults");
    const transaksiNasabahOptions = document.querySelectorAll(".transaksi-nasabah-option");
    const detailToggles = document.querySelectorAll(".detail-toggle");
    const transactionDetails = document.querySelectorAll(".transaction-detail");
    const filterTanggalMulai = document.getElementById("filterTanggalMulai");
    const filterTanggalSelesai = document.getElementById("filterTanggalSelesai");
    const applyTransactionFilter = document.getElementById("applyTransactionFilter");
    const resetTransactionFilter = document.getElementById("resetTransactionFilter");
    const transactionRows = document.querySelectorAll(".transaction-row");
    const transactionEmptyState = document.getElementById("transactionEmptyState");
    const metodePembayaran = document.getElementById("metodePembayaran");
    const ewalletFields = document.getElementById("ewalletFields");
    const searchNasabahPembayaran = document.getElementById("searchNasabahPembayaran");
    const nasabahSearchResults = document.getElementById("nasabahSearchResults");
    const nasabahOptions = document.querySelectorAll(".nasabah-option");
    const openPembayaranForm = document.getElementById("openPembayaranForm");
    const savePembayaran = document.getElementById("savePembayaran");
    const backToPembayaran = document.getElementById("backToPembayaran");
    const filterPaymentId = document.getElementById("filterPaymentId");
    const filterPaymentNasabah = document.getElementById("filterPaymentNasabah");
    const filterPaymentMethod = document.getElementById("filterPaymentMethod");
    const filterPaymentStatus = document.getElementById("filterPaymentStatus");
    const filterPaymentDate = document.getElementById("filterPaymentDate");
    const paymentRows = document.querySelectorAll(".payment-row");
    const paymentEmptyState = document.getElementById("paymentEmptyState");
    const paymentStatusTabs = document.querySelectorAll(".payment-status-tab");
    const filterUserName = document.getElementById("filterUserName");
    const filterUserEmail = document.getElementById("filterUserEmail");
    const filterUserRole = document.getElementById("filterUserRole");
    const filterUserStatus = document.getElementById("filterUserStatus");
    const searchUserButton = document.getElementById("searchUserButton");
    const clearUserFilter = document.getElementById("clearUserFilter");
    const userRows = document.querySelectorAll(".user-row");
    const userEmptyState = document.getElementById("userEmptyState");
    const withdrawMethod = document.getElementById("withdrawMethod");
    const nasabahEwalletFields = document.getElementById("nasabahEwalletFields");
    const openUserForm = document.getElementById("openUserForm");
    const backToUserList = document.getElementById("backToUserList");
    const saveUserButton = document.getElementById("saveUserButton");
    const userFormBreadcrumb = document.getElementById("userFormBreadcrumb");
    const userFormHeading = document.getElementById("userFormHeading");
    const newUserName = document.getElementById("newUserName");
    const newUserEmail = document.getElementById("newUserEmail");
    const newUserPassword = document.getElementById("newUserPassword");
    const newUserRole = document.getElementById("newUserRole");
    const newUserStatus = document.getElementById("newUserStatus");
    const checkAllUserAccess = document.getElementById("checkAllUserAccess");
    const uncheckAllUserAccess = document.getElementById("uncheckAllUserAccess");
    const userCustomAccessSection = document.getElementById("userCustomAccessSection");
    const checkAllOperationalAccess = document.getElementById("checkAllOperationalAccess");
    const uncheckAllOperationalAccess = document.getElementById("uncheckAllOperationalAccess");
    const userOperationalAccessSection = document.getElementById("userOperationalAccessSection");
    const userEditButtons = document.querySelectorAll(".user-edit-button");
    let currentProfilePhoto = "";
    let pendingProfilePhoto = "";
    let activePaymentTab = "all";
    let editingUserRow = null;
    let editingNasabahRow = null;
    let editingKategoriRow = null;
    let editingSampahRow = null;

    function resetUserFormState() {
      editingUserRow = null;
      if (userFormBreadcrumb) userFormBreadcrumb.innerHTML = "User &rsaquo; Create";
      if (userFormHeading) userFormHeading.textContent = "Create User";
      if (newUserName) newUserName.value = "";
      if (newUserEmail) newUserEmail.value = "";
      if (newUserPassword) newUserPassword.value = "";
      if (newUserRole) newUserRole.value = "Super Admin";
      if (newUserStatus) newUserStatus.value = "Aktif";
    }

    function resetNasabahFormState() {
      editingNasabahRow = null;
      if (idNasabah) idNasabah.value = "";
      if (namaNasabah) namaNasabah.value = "";
      if (alamatNasabah) alamatNasabah.value = "";
      if (noHpNasabah) noHpNasabah.value = "";
      if (tanggalDaftar) tanggalDaftar.value = "";
    }

    function resetKategoriFormState() {
      editingKategoriRow = null;
      if (idJenis) idJenis.value = "";
      if (namaJenis) namaJenis.value = "";
    }

    function openEditKategoriForm(row) {
      if (!row) return;
      editingKategoriRow = row;
      if (idJenis) idJenis.value = row.dataset.kategoriId || "";
      if (namaJenis) namaJenis.value = row.dataset.kategoriName || "";
      setActivePage("jenisSampahForm");
    }

    function openEditNasabahForm(row) {
      if (!row) return;
      editingNasabahRow = row;
      if (idNasabah) idNasabah.value = row.dataset.nasabahId || "";
      if (namaNasabah) namaNasabah.value = row.dataset.nasabahName || "";
      if (alamatNasabah) alamatNasabah.value = row.dataset.nasabahAddress || "";
      if (noHpNasabah) noHpNasabah.value = row.dataset.nasabahPhone || "";
      if (tanggalDaftar) tanggalDaftar.value = row.dataset.nasabahDate || "";
      setActivePage("nasabahForm");
    }

    function resetSampahFormState() {
      editingSampahRow = null;
      if (idSampah) idSampah.value = "";
      if (namaSampah) namaSampah.value = "";
      if (hargaSampah) hargaSampah.value = "";
      if (jenisSampahSelect) jenisSampahSelect.value = "-- Pilih Jenis --";
    }

    function openEditSampahForm(row) {
      if (!row) return;
      editingSampahRow = row;
      if (idSampah) idSampah.value = row.dataset.sampahId || "";
      if (namaSampah) namaSampah.value = row.dataset.sampahName || "";
      if (hargaSampah) hargaSampah.value = row.dataset.sampahPrice || "";
      if (jenisSampahSelect) jenisSampahSelect.value = row.dataset.sampahCategory || "-- Pilih Jenis --";
      setActivePage("sampahForm");
    }

    function openEditUserForm(row) {
      if (!row) return;
      editingUserRow = row;
      if (userFormBreadcrumb) userFormBreadcrumb.innerHTML = "User &rsaquo; Edit";
      if (userFormHeading) userFormHeading.textContent = "Edit User";
      if (newUserName) newUserName.value = row.dataset.userName || "";
      if (newUserEmail) newUserEmail.value = row.dataset.userEmail || "";
      if (newUserPassword) newUserPassword.value = "";
      if (newUserRole) newUserRole.value = row.dataset.userRole || "Petugas";
      if (newUserStatus) newUserStatus.value = row.dataset.userStatus || "Aktif";
      setActivePage("userForm");
    }

    function toggleAllUserAccess(checked) {
      if (!userCustomAccessSection) return;
      const checkboxes = userCustomAccessSection.querySelectorAll('input[type="checkbox"]');
      checkboxes.forEach((checkbox) => {
        checkbox.checked = checked;
      });
    }

    function toggleOperationalAccess(checked) {
      if (!userOperationalAccessSection) return;
      const checkboxes = userOperationalAccessSection.querySelectorAll('input[type="checkbox"]');
      checkboxes.forEach((checkbox) => {
        checkbox.checked = checked;
      });
    }

    function bindRemoveTransactionItems() {
      const removeButtons = document.querySelectorAll(".remove-transaction-item");
      removeButtons.forEach((button) => {
        if (button.dataset.bound === "true") return;
        button.dataset.bound = "true";
        button.addEventListener("click", () => {
          const items = document.querySelectorAll(".transaction-item");
          if (items.length <= 1) return;
          const item = button.closest(".transaction-item");
          if (item) {
            item.remove();
          }
        });
      });
    }

    function filterTransactionSampahOptions(container) {
      if (!container) return;
      const input = container.querySelector(".transaction-sampah-input");
      const results = container.querySelector(".transaction-sampah-results");
      const options = container.querySelectorAll(".transaction-sampah-option");
      if (!input || !results) return;

      const keyword = input.value.trim().toLowerCase();
      let visibleCount = 0;

      options.forEach((option) => {
        const isVisible = !keyword || option.dataset.value.toLowerCase().includes(keyword);
        option.classList.toggle("hidden", !isVisible);
        if (isVisible) visibleCount += 1;
      });

      results.classList.toggle("hidden", visibleCount === 0);
    }

    function showLoginPage() {
      if (loginPage) loginPage.classList.remove("hidden");
      if (appShell) appShell.classList.add("hidden");
    }

    function showDashboardApp() {
      if (loginPage) loginPage.classList.add("hidden");
      if (appShell) appShell.classList.remove("hidden");
    }

    function renderProfileAvatar(target, nameValue, photoUrl) {
      if (!target) return;
      const safeName = (nameValue || "AD").trim();
      const initials = safeName
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join("") || "AD";

      if (photoUrl) {
        target.innerHTML = `<img src="${photoUrl}" alt="Foto profil admin" class="h-full w-full object-cover" />`;
      } else {
        target.textContent = initials;
      }
    }

    function openProfileEditor() {
      if (profileNameInput && profileName) profileNameInput.value = profileName.textContent.trim();
      if (profileRoleInput && profileRole) profileRoleInput.value = profileRole.textContent.trim();
      pendingProfilePhoto = currentProfilePhoto;
      if (profilePhotoInput) profilePhotoInput.value = "";
      renderProfileAvatar(profilePreview, profileNameInput ? profileNameInput.value : "Admin Utama", pendingProfilePhoto);
      if (profilePanelOverlay) profilePanelOverlay.classList.remove("hidden");
    }

    function closeProfileEditor() {
      if (profilePanelOverlay) profilePanelOverlay.classList.add("hidden");
    }

    function setActivePage(page) {
      const navActiveMap = {
        nasabahForm: "nasabah",
        jenisSampahForm: "jenisSampah",
        sampahForm: "sampah",
        transaksiForm: "transaksi",
        pembayaranForm: "pembayaran",
        userForm: "user",
      };
      const activeNavPage = navActiveMap[page] || page;

      pageSections.forEach((section) => {
        section.classList.toggle("hidden", section.id !== page + "Page");
      });

      navLinks.forEach((link) => {
        const isActive = link.dataset.page === activeNavPage;
        link.classList.toggle("bg-sky-50", isActive);
        link.classList.toggle("text-sky-700", isActive);
        link.classList.toggle("text-slate-700", !isActive);
        link.classList.toggle("hover:bg-slate-100", !isActive);

        const iconBox = link.querySelector("span");
        if (iconBox) {
          iconBox.classList.toggle("bg-sky-100", isActive);
          iconBox.classList.toggle("bg-slate-100", !isActive);
        }

        const icon = link.querySelector("svg");
        if (icon) {
          icon.classList.toggle("text-slate-500", !isActive);
        }
      });

      pageTitle.textContent = pageMeta[page].title;
      pageSubtitle.textContent = pageMeta[page].subtitle;
    }

    navLinks.forEach((link) => {
      link.addEventListener("click", (event) => {
        const page = link.dataset.page;
        if (!pageMeta[page]) return;
        event.preventDefault();
        setActivePage(page);
      });
    });

    if (loginForm) {
      loginForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const emailValue = loginEmail ? loginEmail.value.trim() : "";
        const passwordValue = loginPassword ? loginPassword.value.trim() : "";

        if (!emailValue || !passwordValue) {
          if (loginError) loginError.classList.remove("hidden");
          return;
        }

        if (loginError) loginError.classList.add("hidden");
        showDashboardApp();
        setActivePage("dashboard");
      });
    }

    if (logoutButton) {
      logoutButton.addEventListener("click", () => {
        showLoginPage();
        if (loginForm) loginForm.reset();
        if (loginError) loginError.classList.add("hidden");
      });
    }

    if (openProfilePanel) {
      openProfilePanel.addEventListener("click", openProfileEditor);
    }

    if (closeProfilePanel) {
      closeProfilePanel.addEventListener("click", closeProfileEditor);
    }

    if (profilePanelOverlay) {
      profilePanelOverlay.addEventListener("click", (event) => {
        if (event.target === profilePanelOverlay) {
          closeProfileEditor();
        }
      });
    }

    if (profileNameInput) {
      profileNameInput.addEventListener("input", () => {
        renderProfileAvatar(profilePreview, profileNameInput.value, pendingProfilePhoto);
      });
    }

    if (profilePhotoInput) {
      profilePhotoInput.addEventListener("change", () => {
        const file = profilePhotoInput.files && profilePhotoInput.files[0];
        if (!file) {
          pendingProfilePhoto = currentProfilePhoto;
          renderProfileAvatar(profilePreview, profileNameInput ? profileNameInput.value : "Admin Utama", pendingProfilePhoto);
          return;
        }

        const reader = new FileReader();
        reader.onload = () => {
          pendingProfilePhoto = typeof reader.result === "string" ? reader.result : "";
          renderProfileAvatar(profilePreview, profileNameInput ? profileNameInput.value : "Admin Utama", pendingProfilePhoto);
        };
        reader.readAsDataURL(file);
      });
    }

    if (resetProfilePhoto) {
      resetProfilePhoto.addEventListener("click", () => {
        pendingProfilePhoto = "";
        if (profilePhotoInput) profilePhotoInput.value = "";
        renderProfileAvatar(profilePreview, profileNameInput ? profileNameInput.value : "Admin Utama", "");
      });
    }

    if (profileForm) {
      profileForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const nextName = profileNameInput ? profileNameInput.value.trim() || "Admin Utama" : "Admin Utama";
        const nextRole = profileRoleInput ? profileRoleInput.value.trim() || "Super Admin" : "Super Admin";
        const nextPhoto = pendingProfilePhoto;

        if (profileName) profileName.textContent = nextName;
        if (profileRole) profileRole.textContent = nextRole;
        currentProfilePhoto = nextPhoto;
        renderProfileAvatar(profileAvatar, nextName, nextPhoto);
        renderProfileAvatar(profilePreview, nextName, nextPhoto);
        closeProfileEditor();
      });
    }

    if (openNasabahForm) {
      openNasabahForm.addEventListener("click", () => {
        resetNasabahFormState();
        setActivePage("nasabahForm");
      });
    }

    nasabahRows.forEach((row) => {
      const editButton = row.querySelector(".edit-nasabah-button");
      const deleteButton = row.querySelector(".delete-nasabah-button");

      if (editButton) {
        editButton.addEventListener("click", () => {
          openEditNasabahForm(row);
        });
      }

      if (deleteButton) {
        deleteButton.addEventListener("click", () => {
          row.remove();
        });
      }
    });

    if (backToNasabahList) {
      backToNasabahList.addEventListener("click", () => {
        resetNasabahFormState();
        setActivePage("nasabah");
      });
    }

    if (saveNasabah) {
      saveNasabah.addEventListener("click", () => {
        if (editingNasabahRow) {
          const cells = editingNasabahRow.querySelectorAll("td");
          const nextId = idNasabah ? idNasabah.value.trim() || editingNasabahRow.dataset.nasabahId : editingNasabahRow.dataset.nasabahId;
          const nextName = namaNasabah ? namaNasabah.value.trim() || editingNasabahRow.dataset.nasabahName : editingNasabahRow.dataset.nasabahName;
          const nextAddress = alamatNasabah ? alamatNasabah.value.trim() || editingNasabahRow.dataset.nasabahAddress : editingNasabahRow.dataset.nasabahAddress;
          const nextPhone = noHpNasabah ? noHpNasabah.value.trim() || editingNasabahRow.dataset.nasabahPhone : editingNasabahRow.dataset.nasabahPhone;
          const nextDate = tanggalDaftar ? tanggalDaftar.value || editingNasabahRow.dataset.nasabahDate : editingNasabahRow.dataset.nasabahDate;

          editingNasabahRow.dataset.nasabahId = nextId;
          editingNasabahRow.dataset.nasabahName = nextName;
          editingNasabahRow.dataset.nasabahAddress = nextAddress;
          editingNasabahRow.dataset.nasabahPhone = nextPhone;
          editingNasabahRow.dataset.nasabahDate = nextDate;

          if (cells[1]) cells[1].textContent = nextName;
          if (cells[2]) cells[2].textContent = nextAddress;
          if (cells[3]) cells[3].textContent = nextPhone;
          if (cells[4]) cells[4].textContent = nextDate;
        }

        resetNasabahFormState();
        setActivePage("nasabah");
      });
    }

    if (openJenisSampahForm) {
      openJenisSampahForm.addEventListener("click", () => {
        resetKategoriFormState();
        setActivePage("jenisSampahForm");
      });
    }

    kategoriRows.forEach((row) => {
      const editButton = row.querySelector(".edit-kategori-button");
      const deleteButton = row.querySelector(".delete-kategori-button");

      if (editButton) {
        editButton.addEventListener("click", () => {
          openEditKategoriForm(row);
        });
      }

      if (deleteButton) {
        deleteButton.addEventListener("click", () => {
          row.remove();
        });
      }
    });

    if (backToJenisSampahList) {
      backToJenisSampahList.addEventListener("click", () => {
        resetKategoriFormState();
        setActivePage("jenisSampah");
      });
    }

    if (saveJenisSampah) {
      saveJenisSampah.addEventListener("click", () => {
        if (editingKategoriRow) {
          const cells = editingKategoriRow.querySelectorAll("td");
          const nextId = idJenis ? idJenis.value.trim() || editingKategoriRow.dataset.kategoriId : editingKategoriRow.dataset.kategoriId;
          const nextName = namaJenis ? namaJenis.value.trim() || editingKategoriRow.dataset.kategoriName : editingKategoriRow.dataset.kategoriName;

          editingKategoriRow.dataset.kategoriId = nextId;
          editingKategoriRow.dataset.kategoriName = nextName;

          if (cells[1]) cells[1].textContent = nextId;
          if (cells[2]) cells[2].textContent = nextName;
        }

        resetKategoriFormState();
        setActivePage("jenisSampah");
      });
    }

    if (openSampahForm) {
      openSampahForm.addEventListener("click", () => {
        resetSampahFormState();
        setActivePage("sampahForm");
      });
    }

    sampahRows.forEach((row) => {
      const editButton = row.querySelector(".edit-sampah-button");
      const deleteButton = row.querySelector(".delete-sampah-button");

      if (editButton) {
        editButton.addEventListener("click", () => {
          openEditSampahForm(row);
        });
      }

      if (deleteButton) {
        deleteButton.addEventListener("click", () => {
          row.remove();
        });
      }
    });

    if (backToSampah) {
      backToSampah.addEventListener("click", () => {
        resetSampahFormState();
        setActivePage("sampah");
      });
    }

    if (saveSampah) {
      saveSampah.addEventListener("click", () => {
        if (editingSampahRow) {
          const cells = editingSampahRow.querySelectorAll("td");
          const nextId = idSampah ? idSampah.value.trim() || editingSampahRow.dataset.sampahId : editingSampahRow.dataset.sampahId;
          const nextName = namaSampah ? namaSampah.value.trim() || editingSampahRow.dataset.sampahName : editingSampahRow.dataset.sampahName;
          const nextPrice = hargaSampah ? hargaSampah.value.trim() || editingSampahRow.dataset.sampahPrice : editingSampahRow.dataset.sampahPrice;
          const nextCategory = jenisSampahSelect ? jenisSampahSelect.value || editingSampahRow.dataset.sampahCategory : editingSampahRow.dataset.sampahCategory;

          editingSampahRow.dataset.sampahId = nextId;
          editingSampahRow.dataset.sampahName = nextName;
          editingSampahRow.dataset.sampahPrice = nextPrice;
          editingSampahRow.dataset.sampahCategory = nextCategory;

          if (cells[1]) cells[1].textContent = nextId;
          if (cells[2]) cells[2].textContent = nextName;
          if (cells[3]) cells[3].textContent = nextPrice;
          if (cells[4]) cells[4].textContent = nextCategory;
        }

        resetSampahFormState();
        setActivePage("sampah");
      });
    }

    if (openTransaksiForm) {
      openTransaksiForm.addEventListener("click", () => {
        setActivePage("transaksiForm");
      });
    }

    if (backToTransaksiList) {
      backToTransaksiList.addEventListener("click", () => {
        setActivePage("transaksi");
      });
    }

    if (saveTransaksi) {
      saveTransaksi.addEventListener("click", () => {
        setActivePage("transaksi");
      });
    }

    if (searchNasabahTransaksi && nasabahTransaksiResults) {
      const filterTransaksiNasabahOptions = () => {
        const keyword = searchNasabahTransaksi.value.trim().toLowerCase();
        let visibleCount = 0;

        transaksiNasabahOptions.forEach((option) => {
          const isVisible = !keyword || option.dataset.value.toLowerCase().includes(keyword);
          option.classList.toggle("hidden", !isVisible);
          if (isVisible) visibleCount += 1;
        });

        nasabahTransaksiResults.classList.toggle("hidden", visibleCount === 0);
      };

      searchNasabahTransaksi.addEventListener("focus", filterTransaksiNasabahOptions);
      searchNasabahTransaksi.addEventListener("input", filterTransaksiNasabahOptions);

      transaksiNasabahOptions.forEach((option) => {
        option.addEventListener("click", () => {
          searchNasabahTransaksi.value = option.dataset.value;
          nasabahTransaksiResults.classList.add("hidden");
        });
      });
    }

    if (metodePembayaran && ewalletFields) {
      const toggleEwalletFields = () => {
        const isEwallet = metodePembayaran.value.includes("E-Wallet");
        ewalletFields.classList.toggle("hidden", !isEwallet);
      };

      metodePembayaran.addEventListener("change", toggleEwalletFields);
      toggleEwalletFields();
    }

    if (searchNasabahPembayaran && nasabahSearchResults) {
      const filterNasabahOptions = () => {
        const keyword = searchNasabahPembayaran.value.trim().toLowerCase();
        let visibleCount = 0;

        nasabahOptions.forEach((option) => {
          const isVisible = !keyword || option.dataset.value.toLowerCase().includes(keyword);
          option.classList.toggle("hidden", !isVisible);
          if (isVisible) visibleCount += 1;
        });

        nasabahSearchResults.classList.toggle("hidden", visibleCount === 0);
      };

      searchNasabahPembayaran.addEventListener("focus", filterNasabahOptions);
      searchNasabahPembayaran.addEventListener("input", filterNasabahOptions);

      nasabahOptions.forEach((option) => {
        option.addEventListener("click", () => {
          searchNasabahPembayaran.value = option.dataset.value;
          nasabahSearchResults.classList.add("hidden");
        });
      });

      document.addEventListener("click", (event) => {
        const target = event.target;
        const clickedInsideSearch = target === searchNasabahPembayaran || nasabahSearchResults.contains(target);
        if (!clickedInsideSearch) {
          nasabahSearchResults.classList.add("hidden");
        }
      });
    }

    document.addEventListener("input", (event) => {
      const target = event.target;
      if (!(target instanceof HTMLInputElement)) return;

      if (target.classList.contains("transaction-sampah-input")) {
        const container = target.closest(".transaction-sampah-search");
        filterTransactionSampahOptions(container);
      }
    });

    document.addEventListener("focusin", (event) => {
      const target = event.target;
      if (!(target instanceof HTMLInputElement)) return;

      if (target.classList.contains("transaction-sampah-input")) {
        const container = target.closest(".transaction-sampah-search");
        filterTransactionSampahOptions(container);
      }
    });

    document.addEventListener("click", (event) => {
      const target = event.target;
      const option = target instanceof Element ? target.closest(".transaction-sampah-option") : null;

      if (option) {
        const container = option.closest(".transaction-sampah-search");
        const input = container ? container.querySelector(".transaction-sampah-input") : null;
        const results = container ? container.querySelector(".transaction-sampah-results") : null;
        if (input) input.value = option.dataset.value || "";
        if (results) results.classList.add("hidden");
        return;
      }

      document.querySelectorAll(".transaction-sampah-results").forEach((results) => {
        if (!(results instanceof HTMLElement)) return;
        const container = results.closest(".transaction-sampah-search");
        const clickedInside = target instanceof Node && container ? container.contains(target) : false;
        if (!clickedInside) {
          results.classList.add("hidden");
        }
      });

      if (searchNasabahTransaksi && nasabahTransaksiResults) {
        const clickedInsideTransaksiNasabah =
          target === searchNasabahTransaksi ||
          (target instanceof Node && nasabahTransaksiResults.contains(target));
        if (!clickedInsideTransaksiNasabah) {
          nasabahTransaksiResults.classList.add("hidden");
        }
      }
    });

    if (openPembayaranForm) {
      openPembayaranForm.addEventListener("click", () => {
        setActivePage("pembayaranForm");
      });
    }

    if (backToPembayaran) {
      backToPembayaran.addEventListener("click", () => {
        setActivePage("pembayaran");
      });
    }

    if (savePembayaran) {
      savePembayaran.addEventListener("click", () => {
        setActivePage("pembayaran");
      });
    }

    function filterPayments() {
      const idValue = filterPaymentId ? filterPaymentId.value.trim().toLowerCase() : "";
      const nasabahValue = filterPaymentNasabah ? filterPaymentNasabah.value.trim().toLowerCase() : "";
      const methodValue = filterPaymentMethod ? filterPaymentMethod.value : "";
      const statusValue = filterPaymentStatus ? filterPaymentStatus.value : "";
      const dateValue = filterPaymentDate ? filterPaymentDate.value : "";
      let visibleRows = 0;

      paymentRows.forEach((row) => {
        const matchId = !idValue || row.dataset.paymentId.toLowerCase().includes(idValue);
        const matchNasabah = !nasabahValue || row.dataset.paymentNasabah.toLowerCase().includes(nasabahValue);
        const matchMethod = !methodValue || row.dataset.paymentMethod === methodValue;
        const matchStatus = !statusValue || row.dataset.paymentStatus === statusValue;
        const matchDate = !dateValue || row.dataset.paymentDate === dateValue;
        const matchTab = activePaymentTab === "all" || row.dataset.paymentStatus === activePaymentTab;
        const isVisible = matchId && matchNasabah && matchMethod && matchStatus && matchDate && matchTab;

        row.classList.toggle("hidden", !isVisible);
        if (isVisible) visibleRows += 1;
      });

      if (paymentEmptyState) {
        paymentEmptyState.classList.toggle("hidden", visibleRows > 0);
      }
    }

    function filterUsers() {
      const nameValue = filterUserName ? filterUserName.value.trim().toLowerCase() : "";
      const emailValue = filterUserEmail ? filterUserEmail.value.trim().toLowerCase() : "";
      const roleValue = filterUserRole ? filterUserRole.value : "";
      const statusValue = filterUserStatus ? filterUserStatus.value : "";
      let visibleRows = 0;

      userRows.forEach((row) => {
        const matchName = !nameValue || row.dataset.userName.toLowerCase().includes(nameValue);
        const matchEmail = !emailValue || row.dataset.userEmail.toLowerCase().includes(emailValue);
        const matchRole = !roleValue || row.dataset.userRole === roleValue;
        const matchStatus = !statusValue || row.dataset.userStatus === statusValue;
        const isVisible = matchName && matchEmail && matchRole && matchStatus;

        row.classList.toggle("hidden", !isVisible);
        if (isVisible) visibleRows += 1;
      });

      if (userEmptyState) {
        userEmptyState.classList.toggle("hidden", visibleRows > 0);
      }
    }

    [filterPaymentId, filterPaymentNasabah, filterPaymentMethod, filterPaymentStatus, filterPaymentDate].forEach((element) => {
      if (!element) return;
      element.addEventListener("input", filterPayments);
      element.addEventListener("change", filterPayments);
    });

    paymentStatusTabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        activePaymentTab = tab.dataset.paymentTab || "all";
        paymentStatusTabs.forEach((item) => {
          const isActive = item === tab;
          item.classList.toggle("bg-sky-100", isActive);
          item.classList.toggle("text-sky-700", isActive);
          item.classList.toggle("bg-slate-100", !isActive);
          item.classList.toggle("text-slate-600", !isActive);
        });
        filterPayments();
      });
    });

    if (searchUserButton) {
      searchUserButton.addEventListener("click", filterUsers);
    }

    if (clearUserFilter) {
      clearUserFilter.addEventListener("click", () => {
        if (filterUserName) filterUserName.value = "";
        if (filterUserEmail) filterUserEmail.value = "";
        if (filterUserRole) filterUserRole.value = "";
        if (filterUserStatus) filterUserStatus.value = "";
        filterUsers();
      });
    }

    if (withdrawMethod && nasabahEwalletFields) {
      const toggleNasabahEwalletFields = () => {
        nasabahEwalletFields.classList.toggle("hidden", withdrawMethod.value !== "E-Wallet");
      };
      withdrawMethod.addEventListener("change", toggleNasabahEwalletFields);
      toggleNasabahEwalletFields();
    }

    if (openUserForm) {
      openUserForm.addEventListener("click", () => {
        resetUserFormState();
        setActivePage("userForm");
      });
    }

    if (checkAllUserAccess) {
      checkAllUserAccess.addEventListener("click", () => {
        toggleAllUserAccess(true);
      });
    }

    if (uncheckAllUserAccess) {
      uncheckAllUserAccess.addEventListener("click", () => {
        toggleAllUserAccess(false);
      });
    }

    if (checkAllOperationalAccess) {
      checkAllOperationalAccess.addEventListener("click", () => {
        toggleOperationalAccess(true);
      });
    }

    if (uncheckAllOperationalAccess) {
      uncheckAllOperationalAccess.addEventListener("click", () => {
        toggleOperationalAccess(false);
      });
    }

    userEditButtons.forEach((button) => {
      button.addEventListener("click", () => {
        const row = button.closest(".user-row");
        openEditUserForm(row);
      });
    });

    if (backToUserList) {
      backToUserList.addEventListener("click", () => {
        resetUserFormState();
        setActivePage("user");
      });
    }

    if (saveUserButton) {
      saveUserButton.addEventListener("click", () => {
        if (editingUserRow) {
          const nextName = newUserName ? newUserName.value.trim() || editingUserRow.dataset.userName : editingUserRow.dataset.userName;
          const nextEmail = newUserEmail ? newUserEmail.value.trim() || editingUserRow.dataset.userEmail : editingUserRow.dataset.userEmail;
          const nextRole = newUserRole ? newUserRole.value : editingUserRow.dataset.userRole;
          const nextStatus = newUserStatus ? newUserStatus.value : editingUserRow.dataset.userStatus;
          const cells = editingUserRow.querySelectorAll("td");

          editingUserRow.dataset.userName = nextName;
          editingUserRow.dataset.userEmail = nextEmail;
          editingUserRow.dataset.userRole = nextRole;
          editingUserRow.dataset.userStatus = nextStatus;

          if (cells[1]) cells[1].textContent = nextName;
          if (cells[2]) cells[2].textContent = nextEmail;
          if (cells[3]) cells[3].textContent = nextRole;
          if (cells[4]) cells[4].textContent = nextStatus;
          filterUsers();
        }

        resetUserFormState();
        setActivePage("user");
      });
    }

    if (addTransactionItem && transactionItems) {
      addTransactionItem.addEventListener("click", () => {
        const item = document.createElement("div");
        item.className = "transaction-item grid gap-4 rounded-2xl border border-slate-200 p-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end";
        item.innerHTML = `
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-800">Sampah</label>
            <div class="relative transaction-sampah-search">
              <input type="text" placeholder="Cari sampah" autocomplete="off" class="transaction-sampah-input w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500" />
              <div class="transaction-sampah-results absolute left-0 right-0 top-[calc(100%+8px)] z-20 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                <button type="button" class="transaction-sampah-option block w-full border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition hover:bg-sky-50" data-value="Botol Plastik">Botol Plastik</button>
                <button type="button" class="transaction-sampah-option block w-full border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition hover:bg-sky-50" data-value="Kardus">Kardus</button>
                <button type="button" class="transaction-sampah-option block w-full border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition hover:bg-sky-50" data-value="Kaleng">Kaleng</button>
                <button type="button" class="transaction-sampah-option block w-full px-4 py-3 text-left text-sm text-slate-700 transition hover:bg-sky-50" data-value="Kertas HVS">Kertas HVS</button>
              </div>
            </div>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-800">Harga per Kg</label>
            <input type="text" class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500" />
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-800">Berat (Kg)</label>
            <input type="text" class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500" />
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-800">Subtotal</label>
            <input type="text" class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500" />
          </div>
          <div class="md:pb-[2px]">
            <button type="button" class="remove-transaction-item rounded-xl bg-rose-100 px-4 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-200">
              Hapus
            </button>
          </div>
        `;
        transactionItems.appendChild(item);
        bindRemoveTransactionItems();
      });
    }

    detailToggles.forEach((button) => {
      button.addEventListener("click", () => {
        const targetId = button.dataset.detailTarget;
        const targetDetail = document.getElementById(targetId);
        if (!targetDetail) return;

        const willOpen = targetDetail.classList.contains("hidden");
        transactionDetails.forEach((detail) => {
          detail.classList.add("hidden");
        });

        if (willOpen) {
          targetDetail.classList.remove("hidden");
        }
      });
    });

    function filterTransactionsByDate() {
      const startDate = filterTanggalMulai ? filterTanggalMulai.value : "";
      const endDate = filterTanggalSelesai ? filterTanggalSelesai.value : "";
      let visibleRows = 0;

      transactionRows.forEach((row) => {
        const rowDate = row.dataset.transactionDate;
        const matchStart = !startDate || rowDate >= startDate;
        const matchEnd = !endDate || rowDate <= endDate;
        const isVisible = matchStart && matchEnd;

        row.classList.toggle("hidden", !isVisible);
        if (isVisible) visibleRows += 1;
      });

      transactionDetails.forEach((detail) => {
        detail.classList.add("hidden");
      });

      if (transactionEmptyState) {
        transactionEmptyState.classList.toggle("hidden", visibleRows > 0);
      }
    }

    if (applyTransactionFilter) {
      applyTransactionFilter.addEventListener("click", filterTransactionsByDate);
    }

    if (resetTransactionFilter) {
      resetTransactionFilter.addEventListener("click", () => {
        if (filterTanggalMulai) filterTanggalMulai.value = "";
        if (filterTanggalSelesai) filterTanggalSelesai.value = "";
        filterTransactionsByDate();
      });
    }

    bindRemoveTransactionItems();
    renderProfileAvatar(profileAvatar, "Admin Utama", currentProfilePhoto);
    renderProfileAvatar(profilePreview, "Admin Utama", currentProfilePhoto);

    showLoginPage();
    setActivePage("dashboard");

    const ctx = document.getElementById("transactionChart");

    new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Minggu 1", "Minggu 2", "Minggu 3", "Minggu 4"],
        datasets: [
          {
            label: "Transaksi",
            data: [120, 190, 170, 230],
            borderColor: "#0ea5e9",
            backgroundColor: "rgba(14, 165, 233, 0.16)",
            fill: true,
            tension: 0.38,
            borderWidth: 4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: "#ffffff",
            pointBorderColor: "#0ea5e9",
            pointBorderWidth: 3,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            backgroundColor: "#0f172a",
            titleColor: "#ffffff",
            bodyColor: "#e2e8f0",
            padding: 12,
            displayColors: false,
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "#64748b",
            },
          },
          y: {
            beginAtZero: true,
            border: {
              display: false,
            },
            ticks: {
              color: "#64748b",
            },
            grid: {
              color: "rgba(148, 163, 184, 0.15)",
            },
          },
        },
      },
    });
