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
const nasabahTableBody = document.getElementById("nasabahTableBody");
const openJenisSampahForm = document.getElementById("openJenisSampahForm");
const backToJenisSampahList = document.getElementById("backToJenisSampahList");
const saveJenisSampah = document.getElementById("saveJenisSampah");
const idJenis = document.getElementById("idJenis");
const namaJenis = document.getElementById("namaJenis");
const kategoriRows = document.querySelectorAll(".kategori-row");
const kategoriTableBody = document.getElementById("kategoriTableBody");
const openSampahForm = document.getElementById("openSampahForm");
const backToSampah = document.getElementById("backToSampah");
const saveSampah = document.getElementById("saveSampah");
const idSampah = document.getElementById("idSampah");
const namaSampah = document.getElementById("namaSampah");
const hargaPerKg = document.getElementById("hargaPerKg");
const jenisSampahSelect = document.getElementById("jenisSampahSelect");
const sampahRows = document.querySelectorAll(".sampah-row");
const sampahTableBody = document.getElementById("sampahTableBody");
const openTransaksiForm = document.getElementById("openTransaksiForm");
const backToTransaksiList = document.getElementById("backToTransaksiList");
const saveTransaksi = document.getElementById("saveTransaksi");
const addTransactionItem = document.getElementById("addTransactionItem");
const transactionItems = document.getElementById("transactionItems");
const totalBeratTransaksi = document.getElementById("totalBeratTransaksi");
const totalHargaTransaksi = document.getElementById("totalHargaTransaksi");
const tanggalTransaksi = document.getElementById("tanggalTransaksi");
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
const transaksiTableBody = document.querySelector("#transaksiPage table tbody");
const transaksiWrapper = document.querySelector("#transaksiPage .overflow-hidden");
const metodePembayaran = document.getElementById("metodePembayaran");
const ewalletFields = document.getElementById("ewalletFields");
const searchNasabahPembayaran = document.getElementById("searchNasabahPembayaran");
const nasabahSearchResults = document.getElementById("nasabahSearchResults");
const nasabahOptions = document.querySelectorAll(".nasabah-option");
const idTransaksiPembayaran = document.getElementById("idTransaksiPembayaran");
const jumlahBayar = document.getElementById("jumlahBayar");
const statusPembayaran = document.getElementById("statusPembayaran");
const tanggalPembayaran = document.getElementById("tanggalPembayaran");
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
const paymentTableBody = document.getElementById("paymentTableBody");
const filterUserName = document.getElementById("filterUserName");
const filterUserEmail = document.getElementById("filterUserEmail");
const filterUserRole = document.getElementById("filterUserRole");
const filterUserStatus = document.getElementById("filterUserStatus");
const searchUserButton = document.getElementById("searchUserButton");
const clearUserFilter = document.getElementById("clearUserFilter");
const userTableBody = document.querySelector("#userPage table tbody");
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
let currentProfilePhoto = "";
let pendingProfilePhoto = "";
let activePaymentTab = "all";
let editingUserRow = null;
let editingNasabahRow = null;
let editingKategoriRow = null;
let editingSampahRow = null;
let cachedNasabah = [];
let cachedKategori = [];
let cachedSampah = [];
let cachedTransaksi = [];
let cachedUsers = [];
let currentUserId = null;
let transactionChartInstance = null;

const API_BASE_URL = window.APP_API_BASE_URL || "http://localhost:8000/api/v1";
const TOKEN_KEY = "banksampah_token";

function clearStaticPlaceholders() {
  if (nasabahTableBody) nasabahTableBody.innerHTML = "";
  if (kategoriTableBody) kategoriTableBody.innerHTML = "";
  if (sampahTableBody) sampahTableBody.innerHTML = "";
  if (transaksiTableBody) transaksiTableBody.innerHTML = "";
  if (paymentTableBody) paymentTableBody.innerHTML = "";

  if (nasabahTransaksiResults) nasabahTransaksiResults.innerHTML = "";
  if (nasabahSearchResults) nasabahSearchResults.innerHTML = "";

  if (idTransaksiPembayaran) {
    idTransaksiPembayaran.innerHTML = '<option value="">Cari Transaksi</option>';
  }

  document.querySelectorAll(".transaction-sampah-results").forEach((results) => {
    if (results instanceof HTMLElement) {
      results.innerHTML = "";
    }
  });

  if (transaksiWrapper) {
    transaksiWrapper.querySelectorAll(".transaction-detail").forEach((detail) => {
      detail.remove();
    });
  }
}

function getAuthToken() {
  return localStorage.getItem(TOKEN_KEY);
}

function setAuthToken(token) {
  if (token) {
    localStorage.setItem(TOKEN_KEY, token);
  } else {
    localStorage.removeItem(TOKEN_KEY);
  }
}

async function apiRequest(path, options = {}) {
  const headers = Object.assign(
    {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    options.headers || {}
  );

  const token = getAuthToken();
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...options,
    headers,
  });

  const payload = await response.json().catch(() => null);
  if (!response.ok) {
    const message = payload?.message || "Request gagal";
    const error = new Error(message);
    error.payload = payload;
    throw error;
  }

  return payload;
}

function notify(type, message) {
  const toast = window.toast;
  const text = message || "";
  if (toast && typeof toast[type] === "function") {
    toast[type](text);
    return;
  }
  if (toast && typeof toast === "function") {
    toast(text);
    return;
  }
  if (type === "error" || type === "warning") {
    alert(text || "Terjadi kesalahan.");
  }
}

function normalizeCollection(payload) {
  if (!payload) return [];
  const data = payload.data ?? payload;
  if (Array.isArray(data)) return data;
  if (Array.isArray(data?.data)) return data.data;
  return [];
}

async function loadCurrentUser() {
  try {
    const payload = await apiRequest("/auth/me");
    const user = payload?.data || payload;
    currentUserId = user?.id || null;
    if (profileName && user?.nama) profileName.textContent = user.nama;
    if (profileRole && user?.role) profileRole.textContent = user.role;
    renderProfileAvatar(profileAvatar, user?.nama || "Admin", currentProfilePhoto);
  } catch (error) {
    currentUserId = null;
  }
}

function refreshNasabahSearch(resultsContainer, inputElement) {
  if (!resultsContainer || !inputElement) return;
  resultsContainer.innerHTML = "";

  cachedNasabah.forEach((nasabah) => {
    const option = document.createElement("button");
    option.type = "button";
    option.className = "nasabah-option block w-full border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition hover:bg-sky-50";
    option.dataset.value = nasabah.nama || "";
    option.dataset.id = nasabah.id;
    option.textContent = nasabah.nama || "-";
    option.addEventListener("click", () => {
      inputElement.value = nasabah.nama || "";
      inputElement.dataset.nasabahId = nasabah.id;
      resultsContainer.classList.add("hidden");
    });
    resultsContainer.appendChild(option);
  });
}

function refreshKategoriSelect() {
  if (!jenisSampahSelect) return;
  jenisSampahSelect.innerHTML = '<option value="">-- Pilih Jenis --</option>';
  cachedKategori.forEach((kategori) => {
    const option = document.createElement("option");
    option.value = kategori.id;
    option.textContent = kategori.nama_kategori || "-";
    jenisSampahSelect.appendChild(option);
  });
}

function refreshTransaksiSampahOptions(container) {
  if (!container) return;
  const results = container.querySelector(".transaction-sampah-results");
  if (!results) return;
  results.innerHTML = "";

  cachedSampah.forEach((sampah) => {
    const option = document.createElement("button");
    option.type = "button";
    option.className = "transaction-sampah-option block w-full border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition hover:bg-sky-50";
    option.dataset.value = sampah.nama_sampah || "";
    option.dataset.id = sampah.id;
    option.dataset.price = sampah.harga_per_kg || 0;
    option.textContent = sampah.nama_sampah || "-";
    results.appendChild(option);
  });
}

function refreshPembayaranTransaksiOptions() {
  if (!idTransaksiPembayaran) return;
  idTransaksiPembayaran.innerHTML = '<option value="">Cari Transaksi</option>';
  cachedTransaksi.forEach((transaksi) => {
    const option = document.createElement("option");
    option.value = transaksi.id;
    option.textContent = `TRX-${transaksi.id} | ${transaksi.nasabah?.nama || "-"} | Rp ${Number(transaksi.total_harga || 0).toLocaleString("id-ID")}`;
    idTransaksiPembayaran.appendChild(option);
  });
}

function formatPaymentStatus(status) {
  const normalized = (status || "").toLowerCase();
  const map = {
    menunggu: "Menunggu",
    diverifikasi: "Diverifikasi",
    diproses: "Diproses",
    berhasil: "Berhasil",
    ditolak: "Ditolak",
  };
  return map[normalized] || (status ? status.charAt(0).toUpperCase() + status.slice(1) : "-");
}

function normalizePaymentStatus(status) {
  const normalized = (status || "").toLowerCase();
  const map = {
    menunggu: "menunggu",
    diverifikasi: "diverifikasi",
    diproses: "diproses",
    berhasil: "berhasil",
    ditolak: "ditolak",
    lunas: "berhasil",
    gagal: "ditolak",
  };
  return map[normalized] || normalized;
}

function parseCurrencyInput(value) {
  const digits = String(value || "").replace(/[^\d]/g, "");
  return digits ? Number(digits) : 0;
}

function getTransactionItemInputs(item) {
  const dataInputs = item ? item.querySelectorAll("input:not(.transaction-sampah-input)") : [];
  return {
    priceInput: dataInputs[0] || null,
    weightInput: dataInputs[1] || null,
    subtotalInput: dataInputs[2] || null,
  };
}

function updateTransactionItemSubtotal(item) {
  if (!item) return;
  const { priceInput, weightInput, subtotalInput } = getTransactionItemInputs(item);
  if (!priceInput || !weightInput || !subtotalInput) return;
  const priceValue = Number(priceInput.value || 0);
  const weightValue = Number(weightInput.value || 0);
  subtotalInput.value = priceValue && weightValue ? priceValue * weightValue : "";
}

function updateTransaksiTotals() {
  if (!transactionItems) return;
  let totalBerat = 0;
  let totalHarga = 0;

  transactionItems.querySelectorAll(".transaction-item").forEach((item) => {
    const { weightInput, subtotalInput } = getTransactionItemInputs(item);
    const berat = Number(weightInput?.value || 0);
    const subtotal = Number(subtotalInput?.value || 0);
    totalBerat += berat;
    totalHarga += subtotal;
  });

  if (totalBeratTransaksi) totalBeratTransaksi.value = totalBerat ? totalBerat : "";
  if (totalHargaTransaksi) totalHargaTransaksi.value = totalHarga ? totalHarga : "";
}

function renderNasabahRows(items) {
  if (!nasabahTableBody) return;
  nasabahTableBody.innerHTML = "";

  if (!items.length) {
    nasabahTableBody.innerHTML =
      '<tr class="bg-white text-slate-700"><td colspan="6" class="border border-slate-200 px-4 py-6 text-center">Data nasabah belum tersedia.</td></tr>';
    return;
  }

  items.forEach((item, index) => {
    const row = document.createElement("tr");
    row.className = "nasabah-row bg-white text-slate-700";
    row.dataset.nasabahId = item.id;
    row.dataset.nasabahName = item.nama || "";
    row.dataset.nasabahAddress = item.alamat || "";
    row.dataset.nasabahPhone = item.no_hp || "";
    row.dataset.nasabahDate = item.created_at || "";
    row.innerHTML = `
          <td class="border border-slate-200 px-4 py-3">${index + 1}</td>
          <td class="border border-slate-200 px-4 py-3">${item.nama || "-"}</td>
          <td class="border border-slate-200 px-4 py-3">${item.alamat || "-"}</td>
          <td class="border border-slate-200 px-4 py-3">${item.no_hp || "-"}</td>
          <td class="border border-slate-200 px-4 py-3">${item.created_at ? new Date(item.created_at).toLocaleDateString("id-ID") : "-"}</td>
          <td class="border border-slate-200 px-4 py-3">
            <div class="flex gap-2">
              <button type="button" class="edit-nasabah-button rounded-xl bg-amber-500 px-3 py-2 text-xs font-semibold text-white">Edit</button>
              <button type="button" class="delete-nasabah-button rounded-xl bg-rose-500 px-3 py-2 text-xs font-semibold text-white">Hapus</button>
            </div>
          </td>
        `;
    nasabahTableBody.appendChild(row);
  });
}

function renderKategoriRows(items) {
  if (!kategoriTableBody) return;
  kategoriTableBody.innerHTML = "";

  if (!items.length) {
    kategoriTableBody.innerHTML =
      '<tr class="bg-white text-slate-700"><td colspan="4" class="border border-slate-200 px-4 py-6 text-center">Data kategori sampah belum tersedia.</td></tr>';
    return;
  }

  items.forEach((item, index) => {
    const row = document.createElement("tr");
    row.className = "kategori-row bg-white text-slate-700";
    row.dataset.kategoriId = item.id;
    row.dataset.kategoriName = item.nama_kategori || "";
    row.innerHTML = `
      <td class="border border-slate-200 px-4 py-3">${index + 1}</td>
      <td class="border border-slate-200 px-4 py-3">${item.id}</td>
      <td class="border border-slate-200 px-4 py-3">${item.nama_kategori || "-"}</td>
      <td class="border border-slate-200 px-4 py-3">
        <div class="flex gap-2">
          <button type="button" class="edit-kategori-button rounded-lg bg-amber-400 px-4 py-2 font-medium text-slate-900">Edit</button>
          <button type="button" class="delete-kategori-button rounded-lg bg-rose-500 px-4 py-2 font-medium text-white">Hapus</button>
        </div>
      </td>
    `;
    kategoriTableBody.appendChild(row);
  });
}

async function loadKategoriList() {
  try {
    const payload = await apiRequest("/kategori-sampah");
    cachedKategori = normalizeCollection(payload);
    renderKategoriRows(cachedKategori);
    refreshKategoriSelect();
  } catch (error) {
    if (kategoriTableBody) {
      kategoriTableBody.innerHTML =
        '<tr class="bg-white text-rose-600"><td colspan="4" class="border border-slate-200 px-4 py-6 text-center">Gagal memuat data kategori sampah.</td></tr>';
    }
    notify("error", error?.message || "Gagal memuat data kategori sampah.");
  }
}

function renderSampahRows(items) {
  if (!sampahTableBody) return;
  sampahTableBody.innerHTML = "";

  if (!items.length) {
    sampahTableBody.innerHTML =
      '<tr class="bg-white text-slate-700"><td colspan="6" class="border border-slate-200 px-4 py-6 text-center">Data sampah belum tersedia.</td></tr>';
    return;
  }

  items.forEach((item, index) => {
    const kategoriName = item.kategori_sampah?.nama_kategori || "-";
    const row = document.createElement("tr");
    row.className = "sampah-row bg-white text-slate-700";
    row.dataset.sampahId = item.id;
    row.dataset.sampahName = item.nama_sampah || "";
    row.dataset.sampahPrice = item.harga_per_kg || "";
    row.dataset.sampahCategory = item.kategori_sampah_id || "";
    row.innerHTML = `
      <td class="border border-slate-200 px-4 py-3">${index + 1}</td>
      <td class="border border-slate-200 px-4 py-3">${item.id}</td>
      <td class="border border-slate-200 px-4 py-3">${item.nama_sampah || "-"}</td>
      <td class="border border-slate-200 px-4 py-3">Rp ${Number(item.harga_per_kg || 0).toLocaleString("id-ID")}</td>
      <td class="border border-slate-200 px-4 py-3">${kategoriName}</td>
      <td class="border border-slate-200 px-4 py-3">
        <div class="flex gap-2">
          <button type="button" class="edit-sampah-button rounded-lg bg-amber-400 px-4 py-2 font-medium text-slate-900">Edit</button>
          <button type="button" class="delete-sampah-button rounded-lg bg-rose-500 px-4 py-2 font-medium text-white">Hapus</button>
        </div>
      </td>
    `;
    sampahTableBody.appendChild(row);
  });
}

async function loadSampahList() {
  try {
    const payload = await apiRequest("/sampah");
    cachedSampah = normalizeCollection(payload);
    renderSampahRows(cachedSampah);
    document.querySelectorAll(".transaction-sampah-search").forEach((container) => {
      refreshTransaksiSampahOptions(container);
    });
  } catch (error) {
    if (sampahTableBody) {
      sampahTableBody.innerHTML =
        '<tr class="bg-white text-rose-600"><td colspan="6" class="border border-slate-200 px-4 py-6 text-center">Gagal memuat data sampah.</td></tr>';
    }
    notify("error", error?.message || "Gagal memuat data sampah.");
  }
}

function clearTransaksiDetails() {
  const existingDetails = document.querySelectorAll("#transaksiPage .transaction-detail");
  existingDetails.forEach((detail) => detail.remove());
}

function renderTransaksiRows(items) {
  if (!transaksiTableBody) return;
  transaksiTableBody.innerHTML = "";
  clearTransaksiDetails();

  if (!items.length) {
    transaksiTableBody.innerHTML =
      '<tr class="bg-white text-slate-700"><td colspan="7" class="border border-slate-200 px-4 py-6 text-center">Data transaksi belum tersedia.</td></tr>';
    if (transactionEmptyState) transactionEmptyState.classList.remove("hidden");
    return;
  }

  if (transactionEmptyState) transactionEmptyState.classList.add("hidden");

  items.forEach((item, index) => {
    const detailId = `detail-${item.id}`;
    const row = document.createElement("tr");
    row.className = "transaction-row bg-white text-slate-700";
    row.dataset.transactionDate = item.tanggal || "";
    row.innerHTML = `
      <td class="border border-slate-200 px-4 py-3">${index + 1}</td>
      <td class="border border-slate-200 px-4 py-3">${item.nasabah?.nama || "-"}</td>
      <td class="border border-slate-200 px-4 py-3">${item.tanggal || "-"}</td>
      <td class="border border-slate-200 px-4 py-3">${item.total_berat ? `${item.total_berat} Kg` : "-"}</td>
      <td class="border border-slate-200 px-4 py-3">Rp ${Number(item.total_harga || 0).toLocaleString("id-ID")}</td>
      <td class="border border-slate-200 px-4 py-3">${item.detail_transaksi?.length || 0} item</td>
      <td class="border border-slate-200 px-4 py-3">
        <button type="button" data-detail-target="${detailId}" class="detail-toggle rounded-lg bg-sky-100 px-3 py-2 font-medium text-sky-700">Lihat Item</button>
      </td>
    `;
    transaksiTableBody.appendChild(row);

    const detail = document.createElement("div");
    detail.id = detailId;
    detail.className = "transaction-detail hidden border-t border-slate-200 px-5 py-5";
    const detailRows = (item.detail_transaksi || [])
      .map((detailItem) => {
        const sampahName = detailItem.sampah?.nama_sampah || "-";
        const harga = detailItem.sampah?.harga_per_kg || 0;
        return `
          <tr class="bg-white text-slate-700">
            <td class="border border-slate-200 px-4 py-3">${sampahName}</td>
            <td class="border border-slate-200 px-4 py-3">${detailItem.berat} Kg</td>
            <td class="border border-slate-200 px-4 py-3">Rp ${Number(harga).toLocaleString("id-ID")}</td>
            <td class="border border-slate-200 px-4 py-3">Rp ${Number(detailItem.subtotal || 0).toLocaleString("id-ID")}</td>
          </tr>
        `;
      })
      .join("");

    detail.innerHTML = `
      <div class="rounded-2xl bg-slate-50 p-5">
        <h4 class="text-base font-semibold text-slate-900">Detail Transaksi</h4>
        <div class="mt-4 overflow-x-auto">
          <table class="min-w-full border border-slate-200 text-left text-sm">
            <thead class="bg-white">
              <tr class="text-slate-900">
                <th class="border border-slate-200 px-4 py-3 font-semibold">Jenis Sampah</th>
                <th class="border border-slate-200 px-4 py-3 font-semibold">Berat</th>
                <th class="border border-slate-200 px-4 py-3 font-semibold">Harga/Kg</th>
                <th class="border border-slate-200 px-4 py-3 font-semibold">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              ${detailRows || '<tr class="bg-white text-slate-700"><td colspan="4" class="border border-slate-200 px-4 py-3">Detail kosong.</td></tr>'}
            </tbody>
          </table>
        </div>
      </div>
    `;

    if (transaksiWrapper) transaksiWrapper.appendChild(detail);
  });
}

async function loadTransaksiList() {
  try {
    const payload = await apiRequest("/transaksi");
    cachedTransaksi = normalizeCollection(payload);
    renderTransaksiRows(cachedTransaksi);
    refreshPembayaranTransaksiOptions();
    filterTransactionsByDate();
  } catch (error) {
    if (transaksiTableBody) {
      transaksiTableBody.innerHTML =
        '<tr class="bg-white text-rose-600"><td colspan="7" class="border border-slate-200 px-4 py-6 text-center">Gagal memuat data transaksi.</td></tr>';
    }
    if (transactionEmptyState) transactionEmptyState.classList.remove("hidden");
    notify("error", error?.message || "Gagal memuat data transaksi.");
  }
}

function paymentStatusBadge(status) {
  const normalized = (status || "").toLowerCase();
  if (normalized === "berhasil") return "bg-emerald-100 text-emerald-700";
  if (normalized === "diproses") return "bg-amber-100 text-amber-700";
  if (normalized === "diverifikasi") return "bg-violet-100 text-violet-700";
  if (normalized === "ditolak") return "bg-rose-100 text-rose-700";
  return "bg-sky-100 text-sky-700";
}

function paymentActionButtons(statusNormalized) {
  if (statusNormalized === "berhasil") {
    return '<button type="button" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500" disabled>Sudah Selesai</button>';
  }
  if (statusNormalized === "ditolak") {
    return '<button type="button" class="rounded-xl bg-rose-100 px-3 py-2 text-xs font-semibold text-rose-600" disabled>Ditolak</button>';
  }

  let primaryLabel = "Tandai Selesai";
  let primaryStatus = "berhasil";

  if (statusNormalized === "menunggu") {
    primaryLabel = "Verifikasi";
    primaryStatus = "diverifikasi";
  } else if (statusNormalized === "diverifikasi") {
    primaryLabel = "Proses Payout";
    primaryStatus = "diproses";
  } else if (statusNormalized === "diproses") {
    primaryLabel = "Tandai Selesai";
    primaryStatus = "berhasil";
  }

  return `
      <button type="button" data-next-status="${primaryStatus}" class="payment-action rounded-xl bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700">
        ${primaryLabel}
      </button>
      <button type="button" data-next-status="ditolak" class="payment-action rounded-xl bg-rose-100 px-3 py-2 text-xs font-semibold text-rose-600">
        Tolak
      </button>
    `;
}

function renderPembayaranRows(items) {
  if (!paymentTableBody) return;
  paymentTableBody.innerHTML = "";

  if (!items.length) {
    paymentTableBody.innerHTML =
      '<tr class="bg-white text-slate-700"><td colspan="8" class="border border-slate-200 px-4 py-6 text-center">Data pembayaran belum tersedia.</td></tr>';
    if (paymentEmptyState) paymentEmptyState.classList.remove("hidden");
    return;
  }

  if (paymentEmptyState) paymentEmptyState.classList.add("hidden");

  items.forEach((item, index) => {
    const statusNormalized = normalizePaymentStatus(item.status);
    const statusLabel = formatPaymentStatus(statusNormalized);
    const transaksiInfo = cachedTransaksi.find((transaksi) => String(transaksi.id) === String(item.transaksi_id));
    const fallbackNasabah = item.transaksi?.nasabah_id ? String(item.transaksi.nasabah_id) : "-";
    const nasabahName = transaksiInfo?.nasabah?.nama || fallbackNasabah;
    const row = document.createElement("tr");
    row.className = "payment-row bg-white text-slate-700";
    row.dataset.paymentId = item.transaksi_id || "";
    row.dataset.paymentRecordId = item.id || "";
    row.dataset.paymentTransaksiId = item.transaksi_id || "";
    row.dataset.paymentJumlah = item.jumlah || "";
    row.dataset.paymentMetode = item.metode || "";
    row.dataset.paymentTanggal = item.tanggal || "";
    row.dataset.paymentNasabah = nasabahName || "";
    row.dataset.paymentMethod = item.metode || "";
    row.dataset.paymentStatus = statusLabel;
    row.dataset.paymentDate = item.tanggal || "";
    row.innerHTML = `
      <td class="border border-slate-200 px-4 py-3">${index + 1}</td>
      <td class="border border-slate-200 px-4 py-3">${item.transaksi_id || "-"}</td>
      <td class="border border-slate-200 px-4 py-3">${nasabahName}</td>
      <td class="border border-slate-200 px-4 py-3">Rp ${Number(item.jumlah || 0).toLocaleString("id-ID")}</td>
      <td class="border border-slate-200 px-4 py-3">${item.metode || "-"}</td>
      <td class="border border-slate-200 px-4 py-3">${item.tanggal || "-"}</td>
      <td class="border border-slate-200 px-4 py-3">
        <div class="flex flex-wrap gap-2">
          ${paymentActionButtons(statusNormalized)}
        </div>
      </td>
      <td class="border border-slate-200 px-4 py-3">
        <span class="rounded-full px-3 py-1 text-xs font-semibold ${paymentStatusBadge(statusLabel)}">${statusLabel}</span>
      </td>
    `;
    paymentTableBody.appendChild(row);
  });
}

async function loadPembayaranList() {
  try {
    if (!cachedTransaksi.length) {
      try {
        const transaksiPayload = await apiRequest("/transaksi");
        cachedTransaksi = normalizeCollection(transaksiPayload);
        refreshPembayaranTransaksiOptions();
      } catch (error) {
        // Ignore transaksi cache failure; pembayaran can still render.
      }
    }
    const payload = await apiRequest("/pembayaran");
    renderPembayaranRows(normalizeCollection(payload));
    filterPayments();
  } catch (error) {
    if (paymentTableBody) {
      paymentTableBody.innerHTML =
        '<tr class="bg-white text-rose-600"><td colspan="8" class="border border-slate-200 px-4 py-6 text-center">Gagal memuat data pembayaran.</td></tr>';
    }
    if (paymentEmptyState) paymentEmptyState.classList.remove("hidden");
    notify("error", error?.message || "Gagal memuat data pembayaran.");
  }
}

async function loadNasabahList() {
  try {
    const payload = await apiRequest("/nasabah");
    cachedNasabah = normalizeCollection(payload);
    renderNasabahRows(cachedNasabah);
    refreshNasabahSearch(nasabahTransaksiResults, searchNasabahTransaksi);
    refreshNasabahSearch(nasabahSearchResults, searchNasabahPembayaran);
  } catch (error) {
    if (nasabahTableBody) {
      nasabahTableBody.innerHTML =
        '<tr class="bg-white text-rose-600"><td colspan="6" class="border border-slate-200 px-4 py-6 text-center">Gagal memuat data nasabah.</td></tr>';
    }
    notify("error", error?.message || "Gagal memuat data nasabah.");
  }
}

function getCheckedValues(section) {
  if (!section) return [];
  return Array.from(section.querySelectorAll('input[type="checkbox"]:checked')).map((input) => {
    const wrapper = input.closest("label");
    return wrapper ? wrapper.textContent.replace(/\s+/g, " ").trim() : "";
  }).filter(Boolean);
}

function roleToApi(value) {
  if (value === "Super Admin" || value === "super_admin") return "super_admin";
  if (value === "Petugas" || value === "petugas") return "petugas";
  return "nasabah";
}

function roleToDisplay(value) {
  if (value === "super_admin") return "Super Admin";
  if (value === "petugas") return "Petugas";
  if (value === "nasabah") return "Nasabah";
  return value || "Petugas";
}

function renderUserRows(items) {
  if (!userTableBody) return;
  userTableBody.innerHTML = "";

  if (!items.length) {
    if (userEmptyState) userEmptyState.classList.remove("hidden");
    return;
  }

  if (userEmptyState) userEmptyState.classList.add("hidden");

  items.forEach((item, index) => {
    const row = document.createElement("tr");
    row.className = "user-row border-t border-slate-200 text-slate-700";
    row.dataset.userId = item.id;
    row.dataset.userName = item.nama || "";
    row.dataset.userEmail = item.email || "";
    row.dataset.userRole = item.role || "";
    row.dataset.userStatus = item.status || "Aktif";
    row.dataset.menuAccess = JSON.stringify(item.menu_access || []);
    row.dataset.operationalAccess = JSON.stringify(item.operational_access || []);
    row.innerHTML = `
      <td class="px-5 py-4">${index + 1}</td>
      <td class="px-5 py-4">${item.nama || "-"}</td>
      <td class="px-5 py-4">${item.email || "-"}</td>
      <td class="px-5 py-4">${roleToDisplay(item.role)}</td>
      <td class="px-5 py-4">${item.status || "Aktif"}</td>
      <td class="px-5 py-4">${item.created_at ? new Date(item.created_at).toLocaleString("id-ID") : "-"}</td>
      <td class="px-5 py-4">
        <div class="flex gap-2">
          <button type="button" class="user-edit-button rounded-lg bg-amber-400 px-4 py-2 text-sm font-medium text-slate-900">Edit</button>
          <button type="button" class="user-delete-button rounded-lg bg-rose-500 px-4 py-2 text-sm font-medium text-white">Hapus</button>
        </div>
      </td>
    `;
    userTableBody.appendChild(row);
  });
}

async function loadUsersList() {
  try {
    const payload = await apiRequest("/users");
    cachedUsers = normalizeCollection(payload);
    renderUserRows(cachedUsers);
    filterUsers();
  } catch (error) {
    if (userTableBody) {
      userTableBody.innerHTML =
        '<tr class="text-rose-600"><td colspan="7" class="px-5 py-4 text-center">Gagal memuat data user.</td></tr>';
    }
    notify("error", error?.message || "Gagal memuat data user.");
  }
}

async function loadLaporanSummary() {
  try {
    const payload = await apiRequest("/laporan/summary");
    const summary = payload?.data || {};
    const statCards = document.querySelectorAll("#dashboardPage article h3");
    if (statCards[0]) statCards[0].textContent = String(summary.total_nasabah ?? "-");
    if (statCards[1]) statCards[1].textContent = `${Number(summary.total_berat || 0).toLocaleString("id-ID")} Kg`;
    if (statCards[2]) statCards[2].textContent = `Rp ${Number(summary.total_harga || 0).toLocaleString("id-ID")}`;
    if (statCards[3]) statCards[3].textContent = String(summary.total_transaksi ?? "-");
  } catch (error) {
    // Keep placeholder data on failure.
  }
}

async function loadTransactionChart() {
  const canvas = document.getElementById("transactionChart");
  if (!canvas || typeof Chart === "undefined") return;

  try {
    const payload = await apiRequest("/laporan/chart");
    const labels = payload?.data?.labels || [];
    const transaksiData = payload?.data?.datasets?.find((dataset) => dataset.key === "total_transaksi")?.data || [];

    if (transactionChartInstance) {
      transactionChartInstance.destroy();
    }

    transactionChartInstance = new Chart(canvas, {
      type: "line",
      data: {
        labels,
        datasets: [
          {
            label: "Transaksi",
            data: transaksiData,
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
          legend: { display: false },
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
            grid: { display: false },
            ticks: { color: "#64748b" },
          },
          y: {
            beginAtZero: true,
            border: { display: false },
            ticks: { color: "#64748b" },
            grid: { color: "rgba(148, 163, 184, 0.15)" },
          },
        },
      },
    });
  } catch (error) {
    // Keep chart placeholders when backend chart data is not ready.
  }
}

function resetUserFormState() {
  editingUserRow = null;
  if (userFormBreadcrumb) userFormBreadcrumb.innerHTML = "User &rsaquo; Create";
  if (userFormHeading) userFormHeading.textContent = "Create User";
  if (newUserName) newUserName.value = "";
  if (newUserEmail) newUserEmail.value = "";
  if (newUserPassword) newUserPassword.value = "";
  if (newUserRole) newUserRole.value = "Super Admin";
  if (newUserStatus) newUserStatus.value = "Aktif";
  toggleAllUserAccess(false);
  toggleOperationalAccess(false);
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
  if (hargaPerKg) hargaPerKg.value = "";
  if (jenisSampahSelect) jenisSampahSelect.value = "";
}

function resetTransaksiFormState() {
  if (searchNasabahTransaksi) {
    searchNasabahTransaksi.value = "";
    delete searchNasabahTransaksi.dataset.nasabahId;
  }
  if (totalBeratTransaksi) totalBeratTransaksi.value = "";
  if (totalHargaTransaksi) totalHargaTransaksi.value = "";
  if (tanggalTransaksi) tanggalTransaksi.value = "";
  if (transactionItems) {
    transactionItems.innerHTML = "";
    const item = createTransactionItem();
    transactionItems.appendChild(item);
    bindRemoveTransactionItems();
  }
}

function resetPembayaranFormState() {
  if (searchNasabahPembayaran) {
    searchNasabahPembayaran.value = "";
    delete searchNasabahPembayaran.dataset.nasabahId;
  }
  if (idTransaksiPembayaran) idTransaksiPembayaran.value = "";
  if (jumlahBayar) jumlahBayar.value = "";
  if (metodePembayaran) metodePembayaran.value = "Cash";
  if (statusPembayaran) statusPembayaran.value = "Menunggu";
  if (tanggalPembayaran) tanggalPembayaran.value = "";
}

function openEditSampahForm(row) {
  if (!row) return;
  editingSampahRow = row;
  if (idSampah) idSampah.value = row.dataset.sampahId || "";
  if (namaSampah) namaSampah.value = row.dataset.sampahName || "";
  if (hargaPerKg) hargaPerKg.value = row.dataset.sampahPrice || "";
  if (jenisSampahSelect) jenisSampahSelect.value = row.dataset.sampahCategory || "";
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
  if (newUserRole) newUserRole.value = roleToDisplay(row.dataset.userRole) || "Petugas";
  if (newUserStatus) newUserStatus.value = row.dataset.userStatus || "Aktif";

  const menuAccess = JSON.parse(row.dataset.menuAccess || "[]");
  const operationalAccess = JSON.parse(row.dataset.operationalAccess || "[]");

  toggleAllUserAccess(false);
  toggleOperationalAccess(false);

  if (userCustomAccessSection) {
    userCustomAccessSection.querySelectorAll('input[type="checkbox"]').forEach((input) => {
      const wrapper = input.closest("label");
      const label = wrapper ? wrapper.textContent.replace(/\s+/g, " ").trim() : "";
      input.checked = menuAccess.includes(label);
    });
  }

  if (userOperationalAccessSection) {
    userOperationalAccessSection.querySelectorAll('input[type="checkbox"]').forEach((input) => {
      const wrapper = input.closest("label");
      const label = wrapper ? wrapper.textContent.replace(/\s+/g, " ").trim() : "";
      input.checked = operationalAccess.includes(label);
    });
  }

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
        updateTransaksiTotals();
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

  if (page === "nasabah") {
    loadNasabahList();
  }
  if (page === "jenisSampah") {
    loadKategoriList();
  }
  if (page === "sampah") {
    loadSampahList();
  }
  if (page === "transaksi") {
    loadTransaksiList();
  }
  if (page === "pembayaran") {
    loadPembayaranList();
  }
  if (page === "user") {
    loadUsersList();
  }
  if (page === "dashboard") {
    loadLaporanSummary();
    loadTransactionChart();
  }
  if (page === "laporan") {
    loadLaporanSummary();
  }
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
      notify("warning", "Email dan password wajib diisi.");
      return;
    }

    if (loginError) {
      loginError.classList.add("hidden");
      loginError.textContent = "Email dan password wajib diisi.";
    }

    apiRequest("/auth/login", {
      method: "POST",
      body: JSON.stringify({
        email: emailValue,
        password: passwordValue,
        device_name: "legacy-ui",
      }),
    })
      .then((payload) => {
        const token = payload?.data?.token;
        if (token) {
          setAuthToken(token);
        }
        notify("success", payload?.message || "Login berhasil");
        loadCurrentUser();
        showDashboardApp();
        setActivePage("dashboard");
      })
      .catch((error) => {
        if (loginError) {
          loginError.textContent = error?.message || "Login gagal";
          loginError.classList.remove("hidden");
        }
        notify("error", error?.message || "Login gagal");
      });
  });
}

if (logoutButton) {
  logoutButton.addEventListener("click", () => {
    setAuthToken(null);
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

if (nasabahTableBody) {
  nasabahTableBody.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const row = target.closest(".nasabah-row");
    if (!row) return;

    if (target.closest(".edit-nasabah-button")) {
      openEditNasabahForm(row);
      return;
    }

    if (target.closest(".delete-nasabah-button")) {
      const nasabahId = row.dataset.nasabahId;
      if (!nasabahId) return;
      if (!confirm("Hapus data nasabah ini?")) return;

      apiRequest(`/nasabah/${nasabahId}`, { method: "DELETE" })
        .then((payload) => {
          notify("success", payload?.message || "Nasabah berhasil dihapus.");
          loadNasabahList();
        })
        .catch((error) => {
          notify("error", error?.message || "Gagal menghapus nasabah.");
        });
    }
  });
}

if (backToNasabahList) {
  backToNasabahList.addEventListener("click", () => {
    resetNasabahFormState();
    setActivePage("nasabah");
  });
}

if (saveNasabah) {
  saveNasabah.addEventListener("click", () => {
    const payload = {
      nama: namaNasabah ? namaNasabah.value.trim() : "",
      alamat: alamatNasabah ? alamatNasabah.value.trim() : "",
      no_hp: noHpNasabah ? noHpNasabah.value.trim() : "",
    };

    const request = editingNasabahRow
      ? apiRequest(`/nasabah/${editingNasabahRow.dataset.nasabahId}`, {
        method: "PUT",
        body: JSON.stringify(payload),
      })
      : apiRequest("/nasabah", {
        method: "POST",
        body: JSON.stringify(payload),
      });

    request
      .then((payload) => {
        notify("success", payload?.message || "Nasabah berhasil disimpan.");
        resetNasabahFormState();
        setActivePage("nasabah");
        loadNasabahList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal menyimpan nasabah.");
      });
  });
}

if (openJenisSampahForm) {
  openJenisSampahForm.addEventListener("click", () => {
    resetKategoriFormState();
    setActivePage("jenisSampahForm");
  });
}

if (kategoriTableBody) {
  kategoriTableBody.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const row = target.closest(".kategori-row");
    if (!row) return;

    if (target.closest(".edit-kategori-button")) {
      openEditKategoriForm(row);
      return;
    }

    if (target.closest(".delete-kategori-button")) {
      const kategoriId = row.dataset.kategoriId;
      if (!kategoriId) return;
      if (!confirm("Hapus kategori sampah ini?")) return;

      apiRequest(`/kategori-sampah/${kategoriId}`, { method: "DELETE" })
        .then((payload) => {
          notify("success", payload?.message || "Kategori berhasil dihapus.");
          loadKategoriList();
        })
        .catch((error) => {
          notify("error", error?.message || "Gagal menghapus kategori sampah.");
        });
    }
  });
}

if (backToJenisSampahList) {
  backToJenisSampahList.addEventListener("click", () => {
    resetKategoriFormState();
    setActivePage("jenisSampah");
  });
}

if (saveJenisSampah) {
  saveJenisSampah.addEventListener("click", () => {
    const payload = {
      nama_kategori: namaJenis ? namaJenis.value.trim() : "",
    };

    const request = editingKategoriRow
      ? apiRequest(`/kategori-sampah/${editingKategoriRow.dataset.kategoriId}`, {
        method: "PUT",
        body: JSON.stringify(payload),
      })
      : apiRequest("/kategori-sampah", {
        method: "POST",
        body: JSON.stringify(payload),
      });

    request
      .then((payload) => {
        notify("success", payload?.message || "Kategori berhasil disimpan.");
        resetKategoriFormState();
        setActivePage("jenisSampah");
        loadKategoriList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal menyimpan kategori sampah.");
      });
  });
}

if (openSampahForm) {
  openSampahForm.addEventListener("click", () => {
    resetSampahFormState();
    setActivePage("sampahForm");
  });
}

if (sampahTableBody) {
  sampahTableBody.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const row = target.closest(".sampah-row");
    if (!row) return;

    if (target.closest(".edit-sampah-button")) {
      openEditSampahForm(row);
      return;
    }

    if (target.closest(".delete-sampah-button")) {
      const sampahId = row.dataset.sampahId;
      if (!sampahId) return;
      if (!confirm("Hapus data sampah ini?")) return;

      apiRequest(`/sampah/${sampahId}`, { method: "DELETE" })
        .then((payload) => {
          notify("success", payload?.message || "Sampah berhasil dihapus.");
          loadSampahList();
        })
        .catch((error) => {
          notify("error", error?.message || "Gagal menghapus data sampah.");
        });
    }
  });
}

if (backToSampah) {
  backToSampah.addEventListener("click", () => {
    resetSampahFormState();
    setActivePage("sampah");
  });
}

if (saveSampah) {
  saveSampah.addEventListener("click", () => {
    const payload = {
      kategori_sampah_id: jenisSampahSelect ? Number(jenisSampahSelect.value || 0) : null,
      nama_sampah: namaSampah ? namaSampah.value.trim() : "",
      harga_per_kg: hargaPerKg ? Number(hargaPerKg.value || 0) : 0,
    };

    const request = editingSampahRow
      ? apiRequest(`/sampah/${editingSampahRow.dataset.sampahId}`, {
        method: "PUT",
        body: JSON.stringify(payload),
      })
      : apiRequest("/sampah", {
        method: "POST",
        body: JSON.stringify(payload),
      });

    request
      .then((payload) => {
        notify("success", payload?.message || "Sampah berhasil disimpan.");
        resetSampahFormState();
        setActivePage("sampah");
        loadSampahList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal menyimpan data sampah.");
      });
  });
}

if (openTransaksiForm) {
  openTransaksiForm.addEventListener("click", () => {
    resetTransaksiFormState();
    if (!cachedNasabah.length) {
      apiRequest("/nasabah")
        .then((payload) => {
          cachedNasabah = normalizeCollection(payload);
          refreshNasabahSearch(nasabahTransaksiResults, searchNasabahTransaksi);
        })
        .catch(() => null);
    }
    if (!cachedSampah.length) {
      apiRequest("/sampah")
        .then((payload) => {
          cachedSampah = normalizeCollection(payload);
          document.querySelectorAll(".transaction-sampah-search").forEach((container) => {
            refreshTransaksiSampahOptions(container);
          });
        })
        .catch(() => null);
    }
    setActivePage("transaksiForm");
  });
}

if (backToTransaksiList) {
  backToTransaksiList.addEventListener("click", () => {
    resetTransaksiFormState();
    setActivePage("transaksi");
  });
}

if (saveTransaksi) {
  saveTransaksi.addEventListener("click", () => {
    const nasabahId = searchNasabahTransaksi?.dataset.nasabahId || "";
    const tanggalValue = tanggalTransaksi ? tanggalTransaksi.value : "";

    if (!currentUserId) {
      notify("warning", "User tidak terdeteksi. Silakan login ulang.");
      return;
    }

    if (!nasabahId || !tanggalValue) {
      notify("warning", "Nama nasabah dan tanggal transaksi wajib diisi.");
      return;
    }

    const items = [];
    if (transactionItems) {
      transactionItems.querySelectorAll(".transaction-item").forEach((item) => {
        const sampahId = Number(item.dataset.sampahId || 0);
        const { priceInput, weightInput, subtotalInput } = getTransactionItemInputs(item);
        const berat = Number(weightInput?.value || 0);
        let subtotal = Number(subtotalInput?.value || 0);
        if (!subtotal && priceInput && berat) {
          subtotal = Number(priceInput.value || 0) * berat;
        }
        if (sampahId && berat > 0) {
          items.push({
            sampah_id: sampahId,
            berat,
            subtotal,
          });
        }
      });
    }

    if (!items.length) {
      notify("warning", "Item transaksi minimal satu dan wajib lengkap.");
      return;
    }

    const payload = {
      user_id: Number(currentUserId),
      nasabah_id: Number(nasabahId),
      tanggal: tanggalValue,
      items,
    };

    apiRequest("/transaksi", {
      method: "POST",
      body: JSON.stringify(payload),
    })
      .then((response) => {
        notify("success", response?.message || "Transaksi berhasil disimpan.");
        resetTransaksiFormState();
        setActivePage("transaksi");
        loadTransaksiList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal menyimpan transaksi.");
      });
  });
}

if (searchNasabahTransaksi && nasabahTransaksiResults) {
  const filterTransaksiNasabahOptions = () => {
    const keyword = searchNasabahTransaksi.value.trim().toLowerCase();
    let visibleCount = 0;

    const options = nasabahTransaksiResults.querySelectorAll(".nasabah-option");
    options.forEach((option) => {
      const isVisible = !keyword || option.dataset.value.toLowerCase().includes(keyword);
      option.classList.toggle("hidden", !isVisible);
      if (isVisible) visibleCount += 1;
    });

    nasabahTransaksiResults.classList.toggle("hidden", visibleCount === 0);
  };

  searchNasabahTransaksi.addEventListener("focus", filterTransaksiNasabahOptions);
  searchNasabahTransaksi.addEventListener("input", filterTransaksiNasabahOptions);

  nasabahTransaksiResults.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const option = target.closest(".nasabah-option");
    if (!option) return;
    searchNasabahTransaksi.value = option.dataset.value || "";
    searchNasabahTransaksi.dataset.nasabahId = option.dataset.id || "";
    nasabahTransaksiResults.classList.add("hidden");
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

    const options = nasabahSearchResults.querySelectorAll(".nasabah-option");
    options.forEach((option) => {
      const isVisible = !keyword || option.dataset.value.toLowerCase().includes(keyword);
      option.classList.toggle("hidden", !isVisible);
      if (isVisible) visibleCount += 1;
    });

    nasabahSearchResults.classList.toggle("hidden", visibleCount === 0);
  };

  searchNasabahPembayaran.addEventListener("focus", filterNasabahOptions);
  searchNasabahPembayaran.addEventListener("input", filterNasabahOptions);

  nasabahSearchResults.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const option = target.closest(".nasabah-option");
    if (!option) return;
    searchNasabahPembayaran.value = option.dataset.value || "";
    searchNasabahPembayaran.dataset.nasabahId = option.dataset.id || "";
    nasabahSearchResults.classList.add("hidden");
  });

  document.addEventListener("click", (event) => {
    const target = event.target;
    const clickedInsideSearch = target === searchNasabahPembayaran || nasabahSearchResults.contains(target);
    if (!clickedInsideSearch) {
      nasabahSearchResults.classList.add("hidden");
    }
  });
}

if (idTransaksiPembayaran) {
  idTransaksiPembayaran.addEventListener("change", () => {
    const transaksi = cachedTransaksi.find((item) => String(item.id) === String(idTransaksiPembayaran.value));
    if (transaksi && jumlahBayar) {
      jumlahBayar.value = transaksi.total_harga ? transaksi.total_harga : "";
    }
    if (transaksi && searchNasabahPembayaran) {
      searchNasabahPembayaran.value = transaksi.nasabah?.nama || "";
      searchNasabahPembayaran.dataset.nasabahId = transaksi.nasabah?.id || "";
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

if (transactionItems) {
  transactionItems.addEventListener("input", (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) return;
    if (target.classList.contains("transaction-sampah-input")) return;

    const item = target.closest(".transaction-item");
    if (!item) return;
    updateTransactionItemSubtotal(item);
    updateTransaksiTotals();
  });
}

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
    const item = container ? container.closest(".transaction-item") : null;
    const { priceInput, weightInput, subtotalInput } = getTransactionItemInputs(item);
    const priceValue = Number(option.dataset.price || 0);
    if (input) input.value = option.dataset.value || "";
    if (item) item.dataset.sampahId = option.dataset.id || "";
    if (priceInput) priceInput.value = priceValue ? priceValue : "";
    if (weightInput && subtotalInput) {
      const berat = Number(weightInput.value || 0);
      subtotalInput.value = berat && priceValue ? berat * priceValue : "";
    }
    if (results) results.classList.add("hidden");
    updateTransaksiTotals();
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
    resetPembayaranFormState();
    if (!cachedNasabah.length) {
      apiRequest("/nasabah")
        .then((payload) => {
          cachedNasabah = normalizeCollection(payload);
          refreshNasabahSearch(nasabahSearchResults, searchNasabahPembayaran);
        })
        .catch(() => null);
    }
    if (!cachedTransaksi.length) {
      apiRequest("/transaksi")
        .then((payload) => {
          cachedTransaksi = normalizeCollection(payload);
          refreshPembayaranTransaksiOptions();
        })
        .catch(() => null);
    }
    setActivePage("pembayaranForm");
  });
}

if (backToPembayaran) {
  backToPembayaran.addEventListener("click", () => {
    resetPembayaranFormState();
    setActivePage("pembayaran");
  });
}

if (savePembayaran) {
  savePembayaran.addEventListener("click", () => {
    const transaksiId = idTransaksiPembayaran ? idTransaksiPembayaran.value : "";
    const jumlahValue = jumlahBayar ? parseCurrencyInput(jumlahBayar.value) : 0;
    const metodeValue = metodePembayaran ? metodePembayaran.value : "";
    const statusValue = statusPembayaran ? normalizePaymentStatus(statusPembayaran.value) : "";
    const tanggalValue = tanggalPembayaran ? tanggalPembayaran.value : "";

    if (!transaksiId || !tanggalValue || !metodeValue || !statusValue) {
      notify("warning", "Lengkapi transaksi, metode, status, dan tanggal pembayaran.");
      return;
    }

    apiRequest("/pembayaran", {
      method: "POST",
      body: JSON.stringify({
        transaksi_id: Number(transaksiId),
        jumlah: jumlahValue,
        metode: metodeValue,
        status: statusValue,
        tanggal: tanggalValue,
      }),
    })
      .then((response) => {
        notify("success", response?.message || "Pembayaran berhasil disimpan.");
        resetPembayaranFormState();
        setActivePage("pembayaran");
        loadPembayaranList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal menyimpan pembayaran.");
      });
  });
}

function filterPayments() {
  const idValue = filterPaymentId ? filterPaymentId.value.trim().toLowerCase() : "";
  const nasabahValue = filterPaymentNasabah ? filterPaymentNasabah.value.trim().toLowerCase() : "";
  const methodValue = filterPaymentMethod ? filterPaymentMethod.value : "";
  const statusValue = filterPaymentStatus ? filterPaymentStatus.value : "";
  const dateValue = filterPaymentDate ? filterPaymentDate.value : "";
  let visibleRows = 0;

  const rows = paymentTableBody ? paymentTableBody.querySelectorAll(".payment-row") : [];

  rows.forEach((row) => {
    const paymentId = (row.dataset.paymentId || "").toLowerCase();
    const paymentNasabah = (row.dataset.paymentNasabah || "").toLowerCase();
    const paymentMethod = row.dataset.paymentMethod || "";
    const paymentStatus = row.dataset.paymentStatus || "";
    const paymentDate = row.dataset.paymentDate || "";
    const matchId = !idValue || paymentId.includes(idValue);
    const matchNasabah = !nasabahValue || paymentNasabah.includes(nasabahValue);
    const matchMethod = !methodValue || paymentMethod === methodValue;
    const matchStatus = !statusValue || paymentStatus === statusValue;
    const matchDate = !dateValue || paymentDate === dateValue;
    const matchTab = activePaymentTab === "all" || paymentStatus === activePaymentTab;
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

  const rows = userTableBody ? userTableBody.querySelectorAll(".user-row") : [];

  rows.forEach((row) => {
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

if (paymentTableBody) {
  paymentTableBody.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const button = target.closest(".payment-action");
    if (!button) return;
    const row = button.closest(".payment-row");
    if (!row) return;

    const paymentId = row.dataset.paymentRecordId;
    const nextStatus = button.dataset.nextStatus;
    const transaksiId = row.dataset.paymentTransaksiId;
    const jumlahValue = Number(row.dataset.paymentJumlah || 0);
    const metodeValue = row.dataset.paymentMetode || "";
    const tanggalValue = row.dataset.paymentTanggal || "";

    if (!paymentId || !transaksiId || !metodeValue || !tanggalValue || !nextStatus) {
      notify("warning", "Data pembayaran belum lengkap.");
      return;
    }

    apiRequest(`/pembayaran/${paymentId}`, {
      method: "PUT",
      body: JSON.stringify({
        transaksi_id: Number(transaksiId),
        jumlah: jumlahValue,
        metode: metodeValue,
        status: nextStatus,
        tanggal: tanggalValue,
      }),
    })
      .then((response) => {
        notify("success", response?.message || "Status pembayaran diperbarui.");
        loadPembayaranList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal memperbarui status pembayaran.");
      });
  });
}

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

if (userTableBody) {
  userTableBody.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;

    const editButton = target.closest(".user-edit-button");
    if (editButton) {
      const row = editButton.closest(".user-row");
      openEditUserForm(row);
      return;
    }

    const deleteButton = target.closest(".user-delete-button");
    if (!deleteButton) return;
    const row = deleteButton.closest(".user-row");
    const userId = row?.dataset.userId;
    if (!userId) return;

    apiRequest(`/users/${userId}`, { method: "DELETE" })
      .then((response) => {
        notify("success", response?.message || "User berhasil dihapus.");
        loadUsersList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal menghapus user.");
      });
  });
}

if (backToUserList) {
  backToUserList.addEventListener("click", () => {
    resetUserFormState();
    setActivePage("user");
  });
}

if (saveUserButton) {
  saveUserButton.addEventListener("click", () => {
    const nama = newUserName ? newUserName.value.trim() : "";
    const email = newUserEmail ? newUserEmail.value.trim() : "";
    const password = newUserPassword ? newUserPassword.value.trim() : "";
    const role = roleToApi(newUserRole ? newUserRole.value : "petugas");
    const status = newUserStatus ? newUserStatus.value : "Aktif";
    const menuAccess = getCheckedValues(userCustomAccessSection);
    const operationalAccess = getCheckedValues(userOperationalAccessSection);

    if (!nama || !email) {
      notify("warning", "Nama dan email wajib diisi.");
      return;
    }

    const payload = {
      nama,
      email,
      role,
      status,
      menu_access: menuAccess,
      operational_access: operationalAccess,
    };

    if (password) {
      payload.password = password;
    }

    if (!editingUserRow && !password) {
      notify("warning", "Password wajib diisi untuk user baru.");
      return;
    }

    const userId = editingUserRow?.dataset.userId;
    const endpoint = userId ? `/users/${userId}` : "/users";
    const method = userId ? "PUT" : "POST";

    apiRequest(endpoint, {
      method,
      body: JSON.stringify(payload),
    })
      .then((response) => {
        notify("success", response?.message || "Data user berhasil disimpan.");
        resetUserFormState();
        setActivePage("user");
        loadUsersList();
      })
      .catch((error) => {
        notify("error", error?.message || "Gagal menyimpan data user.");
      });
  });
}

function createTransactionItem() {
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
  const container = item.querySelector(".transaction-sampah-search");
  refreshTransaksiSampahOptions(container);
  return item;
}

if (addTransactionItem && transactionItems) {
  addTransactionItem.addEventListener("click", () => {
    const item = createTransactionItem();
    transactionItems.appendChild(item);
    bindRemoveTransactionItems();
    updateTransaksiTotals();
  });
}

if (transaksiWrapper) {
  transaksiWrapper.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const button = target.closest(".detail-toggle");
    if (!button) return;
    const targetId = button.dataset.detailTarget;
    const targetDetail = targetId ? document.getElementById(targetId) : null;
    if (!targetDetail) return;

    const willOpen = targetDetail.classList.contains("hidden");
    transaksiWrapper.querySelectorAll(".transaction-detail").forEach((detail) => {
      detail.classList.add("hidden");
    });

    if (willOpen) {
      targetDetail.classList.remove("hidden");
    }
  });
}

function filterTransactionsByDate() {
  const startDate = filterTanggalMulai ? filterTanggalMulai.value : "";
  const endDate = filterTanggalSelesai ? filterTanggalSelesai.value : "";
  let visibleRows = 0;

  const rows = transaksiTableBody ? transaksiTableBody.querySelectorAll(".transaction-row") : [];
  const details = transaksiWrapper ? transaksiWrapper.querySelectorAll(".transaction-detail") : [];

  rows.forEach((row) => {
    const rowDate = row.dataset.transactionDate || "";
    const matchStart = !startDate || rowDate >= startDate;
    const matchEnd = !endDate || rowDate <= endDate;
    const isVisible = matchStart && matchEnd;

    row.classList.toggle("hidden", !isVisible);
    if (isVisible) visibleRows += 1;
  });

  details.forEach((detail) => {
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
clearStaticPlaceholders();
renderProfileAvatar(profileAvatar, "Admin Utama", currentProfilePhoto);
renderProfileAvatar(profilePreview, "Admin Utama", currentProfilePhoto);

if (getAuthToken()) {
  showDashboardApp();
  loadCurrentUser();
  setActivePage("dashboard");
} else {
  showLoginPage();
}
