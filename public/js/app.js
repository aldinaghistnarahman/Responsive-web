import React, { useState } from 'https://esm.sh/react@18.3.1';
import { createRoot } from 'https://esm.sh/react-dom@18.3.1/client?deps=react@18.3.1';

const h = React.createElement;
const page = window.dashboardPage;

function csrf(token) {
    return h('input', { type: 'hidden', name: '_token', value: token });
}

function ErrorText({ errors, name }) {
    return errors?.[name]?.length ? h('small', { className: 'field-error' }, errors[name][0]) : null;
}

function AppShell({ children, active }) {
    const [menuOpen, setMenuOpen] = useState(false);
    const links = [
        ['dashboard', '/dashboard', 'Beranda'],
        ['mahasiswa', '/mahasiswa', 'Mahasiswa'],
        ['prestasi', '/prestasi', 'Prestasi'],
    ];

    return h(React.Fragment, null,
        h('header', { className: 'topbar' },
            h('a', { className: 'brand', href: '/dashboard' },
                h('span', { className: 'brand-mark' }, 'R'),
                h('span', null, 'ruang', h('strong', null, 'kampus'))
            ),
            h('button', {
                className: 'menu-toggle',
                type: 'button',
                'aria-label': 'Buka menu',
                'aria-expanded': menuOpen,
                onClick: () => setMenuOpen(!menuOpen),
            }, menuOpen ? 'Tutup' : 'Menu'),
            h('nav', { className: `nav-links${menuOpen ? ' is-open' : ''}` },
                links.map(([key, href, label]) => h('a', {
                    key,
                    href,
                    className: active === key ? 'nav-link active' : 'nav-link',
                    'aria-current': active === key ? 'page' : undefined,
                    onClick: () => setMenuOpen(false),
                }, label)),
                h('form', { action: '/logout', method: 'post', className: 'logout-form' },
                    csrf(page.csrfToken),
                    h('button', { type: 'submit', className: 'logout-button' }, 'Keluar')
                )
            )
        ),
        h('main', { className: 'page-shell' }, children),
        h('footer', { className: 'footer' }, 'Ruang Kampus · Pengelolaan data akademik')
    );
}

function PageHeading({ eyebrow, title, description, action }) {
    return h('div', { className: 'page-heading' },
        h('div', null,
            h('p', { className: 'eyebrow' }, eyebrow),
            h('h1', null, title),
            description ? h('p', { className: 'page-description' }, description) : null
        ),
        action || null
    );
}

function Notice({ children }) {
    return children ? h('div', { className: 'notice', role: 'status' }, children) : null;
}

function Pagination({ pagination, filter = '' }) {
    if (!pagination || pagination.lastPage <= 1) return null;
    const pages = Array.from({ length: pagination.lastPage }, (_, index) => index + 1);
    const hrefFor = (number) => {
        const params = new URLSearchParams(window.location.search);
        if (filter) params.set('prestasi', filter);
        else params.delete('prestasi');
        params.set('page', number);
        return `${window.location.pathname}?${params.toString()}`;
    };

    return h('div', { className: 'pagination' },
        h('span', { className: 'pagination-summary' },
            `Menampilkan ${pagination.from ?? 0}–${pagination.to ?? 0} dari ${pagination.total} data`
        ),
        h('div', { className: 'pagination-buttons' },
            h('a', {
                className: `page-button${pagination.previousPageUrl ? '' : ' disabled'}`,
                href: pagination.previousPageUrl || undefined,
                'aria-disabled': !pagination.previousPageUrl,
            }, '← Sebelumnya'),
            pages.map(number => h('a', {
                key: number,
                className: `page-number${number === pagination.currentPage ? ' active' : ''}`,
                href: hrefFor(number),
                'aria-current': number === pagination.currentPage ? 'page' : undefined,
            }, number)),
            h('a', {
                className: `page-button${pagination.nextPageUrl ? '' : ' disabled'}`,
                href: pagination.nextPageUrl || undefined,
                'aria-disabled': !pagination.nextPageUrl,
            }, 'Berikutnya →')
        )
    );
}

function Dashboard({ data }) {
    return h(AppShell, { active: 'dashboard' },
        h('section', { className: 'hero' },
            h('div', { className: 'hero-copy' },
                h('span', { className: 'hero-kicker' }, 'PORTAL AKADEMIK'),
                h('h1', null, 'Prestasi besar dimulai dari ', h('span', null, 'langkah kecil.')),
                h('p', null, 'Kelola data mahasiswa dan catat pencapaian terbaik mereka, dalam satu ruang yang rapi dan mudah digunakan.'),
                h('div', { className: 'hero-actions' },
                    h('a', { className: 'button button-light', href: '/mahasiswa' }, 'Lihat mahasiswa', h('span', null, '→')),
                    h('a', { className: 'hero-text-link', href: '/prestasi/create' }, 'Tambah prestasi')
                )
            ),
            h('div', { className: 'hero-art', 'aria-hidden': 'true' },
                h('div', { className: 'orbit orbit-one' }),
                h('div', { className: 'orbit orbit-two' }),
                h('div', { className: 'orbit orbit-three' }),
                h('div', { className: 'orbit-core' }, '✦'),
                h('span', { className: 'spark spark-one' }, '✧'),
                h('span', { className: 'spark spark-two' }, '✦')
            )
        ),
        h('div', { className: 'stats-grid' },
            h('article', { className: 'stat-card' },
                h('span', { className: 'stat-icon purple' }, '01'),
                h('div', null, h('p', null, 'Total mahasiswa'), h('strong', null, data.mahasiswaCount))
            ),
            h('article', { className: 'stat-card' },
                h('span', { className: 'stat-icon amber' }, '✦'),
                h('div', null, h('p', null, 'Total prestasi'), h('strong', null, data.prestasiCount))
            ),
            h('article', { className: 'stat-card stat-action' },
                h('div', null, h('p', null, 'Ada pencapaian baru?'), h('strong', null, 'Bagikan ceritanya')),
                h('a', { href: '/prestasi/create', 'aria-label': 'Tambah prestasi' }, '↗')
            )
        ),
        h('section', { className: 'section-block' },
            h('div', { className: 'section-heading' },
                h('div', null, h('p', { className: 'eyebrow' }, 'SOROTAN TERBARU'), h('h2', null, 'Prestasi pilihan')),
                h('a', { className: 'text-link', href: '/prestasi' }, 'Semua prestasi →')
            ),
            data.recentPrestasis.length
                ? h('div', { className: 'highlight-grid' }, data.recentPrestasis.map(item =>
                    h('article', { className: 'highlight-card', key: item.id },
                        h('span', { className: 'achievement-star' }, '✦'),
                        h('span', { className: 'level-pill' }, item.tingkat),
                        h('h3', null, item.nama_prestasi),
                        h('p', { className: 'highlight-year' }, item.tahun),
                        h('p', { className: 'student-names' }, item.mahasiswa.length ? item.mahasiswa.join(', ') : 'Belum ada mahasiswa terhubung')
                    )
                ))
                : h('div', { className: 'empty-state' }, h('strong', null, 'Belum ada prestasi'), h('p', null, 'Prestasi yang ditambahkan akan muncul di sini.'))
        )
    );
}

function StudentFields({ values, errors }) {
    return h(React.Fragment, null,
        ['nama', 'nim', 'email'].map(name => {
            const labels = { nama: 'Nama lengkap', nim: 'NIM', email: 'Alamat email' };
            const placeholders = { nama: 'Contoh: Andi Pratama', nim: 'Nomor induk mahasiswa', email: 'nama@kampus.ac.id' };
            return h('label', { className: 'field', key: name },
                h('span', null, labels[name]),
                h('input', {
                    name,
                    type: name === 'email' ? 'email' : 'text',
                    defaultValue: values[name] ?? '',
                    placeholder: placeholders[name],
                    required: true,
                }),
                h(ErrorText, { errors, name })
            );
        })
    );
}

function StudentIndex({ data }) {
    const values = data.old || {};
    return h(AppShell, { active: 'mahasiswa' },
        h(PageHeading, {
            eyebrow: 'DATA AKADEMIK',
            title: 'Daftar mahasiswa',
            description: 'Kelola informasi mahasiswa dan temukan mereka melalui prestasi yang diraih.',
        }),
        h(Notice, null, data.success),
        h('div', { className: 'content-grid' },
            h('section', { className: 'panel create-panel' },
                h('div', { className: 'panel-heading' }, h('span', { className: 'panel-icon' }, '+'), h('div', null, h('h2', null, 'Tambah mahasiswa'), h('p', null, 'Lengkapi informasi di bawah ini.'))),
                h('form', { action: '/mahasiswa', method: 'post', className: 'form-stack' },
                    csrf(data.csrfToken),
                    h(StudentFields, { values, errors: data.errors }),
                    h('button', { className: 'button button-primary full-button', type: 'submit' }, 'Simpan mahasiswa')
                )
            ),
            h('section', { className: 'panel list-panel' },
                h('div', { className: 'list-heading' },
                    h('div', null, h('h2', null, 'Semua mahasiswa'), h('p', null, 'Data mahasiswa yang terdaftar')),
                    h('span', { className: 'count-pill' }, `${data.pagination.total} data`)
                ),
                h('form', { className: 'filter-form', method: 'get', action: '/mahasiswa' },
                    h('label', { className: 'search-field' },
                        h('span', { 'aria-hidden': true }, '⌕'),
                        h('input', {
                            type: 'search',
                            name: 'prestasi',
                            list: 'prestasi-list',
                            defaultValue: data.filter,
                            placeholder: 'Pilih atau ketik nama prestasi…',
                            'aria-label': 'Cari mahasiswa berdasarkan nama prestasi',
                        }),
                        h('datalist', { id: 'prestasi-list' }, data.prestasiOptions.map((item, index) => h('option', { key: index, value: item })))
                    ),
                    h('button', { className: 'button button-soft', type: 'submit' }, 'Cari'),
                    data.filter ? h('a', { href: '/mahasiswa', className: 'clear-filter' }, 'Hapus filter') : null
                ),
                data.mahasiswa.length
                    ? h('div', { className: 'student-list' }, data.mahasiswa.map(mhs =>
                        h('article', { className: 'student-card', key: mhs.id },
                            h('div', { className: 'student-avatar' }, mhs.nama.split(/\s+/).slice(0, 2).map(part => part[0]).join('').toUpperCase()),
                            h('div', { className: 'student-main' },
                                h('h3', null, mhs.nama),
                                h('p', { className: 'student-meta' }, mhs.nim || 'NIM belum diisi', h('span', null, '·'), mhs.email),
                                h('div', { className: 'tag-list' },
                                    mhs.prestasis.length
                                        ? mhs.prestasis.map((name, index) => h('span', { className: 'achievement-tag', key: index }, '✦ ', name))
                                        : h('span', { className: 'muted-tag' }, 'Belum memiliki prestasi')
                                )
                            ),
                            h('div', { className: 'card-actions' },
                                h('a', { className: 'icon-button', href: `/mahasiswa/${mhs.id}/edit`, 'aria-label': `Edit ${mhs.nama}` }, 'Edit'),
                                h('form', {
                                    action: `/mahasiswa/${mhs.id}`,
                                    method: 'post',
                                    onSubmit: event => { if (!window.confirm(`Hapus data ${mhs.nama}?`)) event.preventDefault(); },
                                },
                                    csrf(data.csrfToken),
                                    h('input', { type: 'hidden', name: '_method', value: 'DELETE' }),
                                    h('button', { className: 'icon-button danger', type: 'submit', 'aria-label': `Hapus ${mhs.nama}` }, 'Hapus')
                                )
                            )
                        )
                    ))
                    : h('div', { className: 'empty-state' },
                        h('span', { className: 'empty-icon' }, '⌕'),
                        h('strong', null, data.filter ? 'Mahasiswa tidak ditemukan' : 'Belum ada data mahasiswa'),
                        h('p', null, data.filter ? `Belum ada mahasiswa yang memiliki prestasi “${data.filter}”.` : 'Tambahkan mahasiswa pertama menggunakan formulir di sebelah kiri.')
                    ),
                h(Pagination, { pagination: data.pagination, filter: data.filter })
            )
        )
    );
}

function StudentEdit({ data }) {
    const values = { ...data.mahasiswa, ...(data.old || {}) };
    return h(AppShell, { active: 'mahasiswa' },
        h(PageHeading, { eyebrow: 'DATA AKADEMIK', title: 'Edit mahasiswa', description: 'Perbarui informasi mahasiswa.' }),
        h('section', { className: 'panel form-panel' },
            h('form', { action: `/mahasiswa/${data.mahasiswa.id}`, method: 'post', className: 'form-stack' },
                csrf(data.csrfToken),
                h('input', { type: 'hidden', name: '_method', value: 'PUT' }),
                h(StudentFields, { values, errors: data.errors }),
                h('div', { className: 'form-actions' },
                    h('a', { className: 'button button-soft', href: '/mahasiswa' }, 'Batal'),
                    h('button', { className: 'button button-primary', type: 'submit' }, 'Simpan perubahan')
                )
            )
        )
    );
}

function AchievementForm({ data, editing }) {
    const initial = data.prestasi || { nama_prestasi: '', tingkat: 'Kampus', tahun: new Date().getFullYear(), deskripsi: '', mahasiswa: [] };
    const old = data.old || {};
    const selected = Array.isArray(old.mahasiswa_ids)
        ? old.mahasiswa_ids.map(String)
        : initial.mahasiswa.map(item => String(item.id));
    const [selectedIds, setSelectedIds] = useState(selected);
    const values = { ...initial, ...old };
    const action = editing ? `/prestasi/${initial.id}` : '/prestasi';

    return h(AppShell, { active: 'prestasi' },
        h(PageHeading, {
            eyebrow: 'PENCAPAIAN MAHASISWA',
            title: editing ? 'Edit prestasi' : 'Tambah prestasi',
            description: 'Catat pencapaian dan hubungkan dengan mahasiswa yang meraihnya.',
        }),
        h('section', { className: 'panel form-panel achievement-form-panel' },
            h('form', { action, method: 'post', className: 'form-stack' },
                csrf(data.csrfToken),
                editing ? h('input', { type: 'hidden', name: '_method', value: 'PUT' }) : null,
                h('div', { className: 'form-two-columns' },
                    h('label', { className: 'field' },
                        h('span', null, 'Nama prestasi'),
                        h('input', { name: 'nama_prestasi', defaultValue: values.nama_prestasi || '', placeholder: 'Contoh: Juara 1 Hackathon 2026', required: true, maxLength: 255 }),
                        h(ErrorText, { errors: data.errors, name: 'nama_prestasi' })
                    ),
                    h('label', { className: 'field' },
                        h('span', null, 'Tingkat'),
                        h('select', { name: 'tingkat', defaultValue: values.tingkat || 'Kampus', required: true },
                            data.tingkatOptions.map(level => h('option', { value: level, key: level }, level))
                        ),
                        h(ErrorText, { errors: data.errors, name: 'tingkat' })
                    ),
                    h('label', { className: 'field' },
                        h('span', null, 'Tahun'),
                        h('input', { name: 'tahun', type: 'number', min: 1900, max: new Date().getFullYear() + 1, defaultValue: values.tahun || '', required: true }),
                        h(ErrorText, { errors: data.errors, name: 'tahun' })
                    ),
                    h('label', { className: 'field field-full' },
                        h('span', null, 'Deskripsi'),
                        h('textarea', { name: 'deskripsi', rows: 4, maxLength: 5000, placeholder: 'Ceritakan pencapaian atau informasi pendukung…', required: true, defaultValue: values.deskripsi || '' }),
                        h(ErrorText, { errors: data.errors, name: 'deskripsi' })
                    ),
                    h('fieldset', { className: 'student-picker field-full', 'aria-describedby': 'student-select-help' },
                        h('legend', { className: 'field-label student-picker-legend' }, 'Mahasiswa peraih prestasi'),
                        h('div', { className: 'student-picker-heading' },
                            h('span', { className: 'student-picker-count' }, `${selectedIds.length} dipilih`)
                        ),
                        h('small', { id: 'student-select-help', className: 'field-hint' }, 'Pilih satu mahasiswa atau beberapa sekaligus.'),
                        data.mahasiswaOptions.length
                            ? h('div', { className: 'student-options' }, data.mahasiswaOptions.map((student, index) => {
                                const id = String(student.id);
                                const isSelected = selectedIds.includes(id);

                                return h('label', {
                                    className: `student-option${isSelected ? ' is-selected' : ''}`,
                                    key: student.id,
                                },
                                    h('span', { className: 'student-option-number' }, String(index + 1).padStart(2, '0')),
                                    h('input', {
                                        className: 'student-option-checkbox',
                                        type: 'checkbox',
                                        name: 'mahasiswa_ids[]',
                                        value: id,
                                        checked: isSelected,
                                        onChange: event => setSelectedIds(current => event.target.checked
                                            ? [...current, id]
                                            : current.filter(selectedId => selectedId !== id)),
                                    }),
                                    h('span', { className: 'student-option-info' },
                                        h('strong', null, student.nama),
                                        h('small', null, student.nim || 'NIM belum diisi')
                                    ),
                                    h('span', { className: 'student-option-check', 'aria-hidden': true }, '✓')
                                );
                            }))
                            : h('div', { className: 'student-picker-empty' },
                                h('strong', null, 'Belum ada data mahasiswa'),
                                h('span', null, 'Tambahkan mahasiswa terlebih dahulu sebelum mencatat prestasi.'),
                                h('a', { href: '/mahasiswa' }, 'Buka daftar mahasiswa →')
                            ),
                        h(ErrorText, { errors: data.errors, name: 'mahasiswa_ids' }),
                        h(ErrorText, { errors: data.errors, name: 'mahasiswa_ids.0' })
                    ),
                ),
                h('div', { className: 'form-actions' },
                    h('a', { className: 'button button-soft', href: '/prestasi' }, 'Batal'),
                    h('button', { className: 'button button-primary', type: 'submit' }, editing ? 'Simpan perubahan' : 'Simpan prestasi')
                )
            )
        )
    );
}

function AchievementIndex({ data }) {
    return h(AppShell, { active: 'prestasi' },
        h(PageHeading, {
            eyebrow: 'PENCAPAIAN MAHASISWA',
            title: 'Daftar prestasi',
            description: 'Satu pencapaian dapat diraih oleh beberapa mahasiswa sekaligus.',
            action: h('a', { className: 'button button-primary', href: '/prestasi/create' }, '+ Tambah prestasi'),
        }),
        h(Notice, null, data.success),
        data.prestasis.length
            ? h('div', { className: 'achievement-list' }, data.prestasis.map(item =>
                h('article', { className: 'achievement-card', key: item.id },
                    h('div', { className: 'achievement-card-top' },
                        h('span', { className: 'achievement-star' }, '✦'),
                        h('span', { className: 'level-pill' }, item.tingkat),
                        h('span', { className: 'achievement-year' }, item.tahun)
                    ),
                    h('h2', null, item.nama_prestasi),
                    h('p', { className: 'achievement-description' }, item.deskripsi),
                    h('div', { className: 'achievement-students' },
                        h('span', { className: 'field-label' }, `Mahasiswa (${item.mahasiswa.length})`),
                        item.mahasiswa.length
                            ? h('div', { className: 'tag-list' }, item.mahasiswa.map(student => h('span', { className: 'achievement-tag', key: student.id }, student.nama)))
                            : h('span', { className: 'muted-tag' }, 'Belum ada mahasiswa terhubung')
                    ),
                    h('div', { className: 'achievement-actions' },
                        h('a', { className: 'icon-button', href: `/prestasi/${item.id}/edit` }, 'Edit'),
                        h('form', {
                            action: `/prestasi/${item.id}`,
                            method: 'post',
                            onSubmit: event => { if (!window.confirm(`Hapus prestasi “${item.nama_prestasi}”?`)) event.preventDefault(); },
                        },
                            csrf(data.csrfToken),
                            h('input', { type: 'hidden', name: '_method', value: 'DELETE' }),
                            h('button', { className: 'icon-button danger', type: 'submit' }, 'Hapus')
                        )
                    )
                )
            ))
            : h('div', { className: 'empty-state page-empty' }, h('span', { className: 'empty-icon' }, '✦'), h('strong', null, 'Belum ada prestasi'), h('p', null, 'Mulai catat pencapaian mahasiswa dengan menambahkan prestasi pertama.')),
        h(Pagination, { pagination: data.pagination })
    );
}

const screens = {
    dashboard: () => h(Dashboard, { data: page }),
    'mahasiswa.index': () => h(StudentIndex, { data: page }),
    'mahasiswa.edit': () => h(StudentEdit, { data: page }),
    'prestasi.index': () => h(AchievementIndex, { data: page }),
    'prestasi.create': () => h(AchievementForm, { data: page, editing: false }),
    'prestasi.edit': () => h(AchievementForm, { data: page, editing: true }),
};

createRoot(document.getElementById('app')).render(
    screens[page.name] ? screens[page.name]() : h('p', null, 'Halaman tidak ditemukan.')
);
