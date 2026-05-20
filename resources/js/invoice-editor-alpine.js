export function registerInvoiceEditor(Alpine) {
    Alpine.data("invoiceEditor", () => ({
        routes: {},
        strings: {},
        theme: {},
        statusLabels: {},
        products: [],
        clients: [],
        templates: [],
        company: {},
        items: [],
        document: { version: 2, meta: {}, custom: {}, stamps: [] },
        invoice: {},
        client: null,
        templatePick: "",
        productPick: "",
        statusMessage: "",
        metaTimer: null,
        itemsTimer: null,
        designTimer: null,
        clientTimer: null,
        zoom: 1,
        stampDragging: false,
        selectedStampId: "",
        logoDragging: false,
        _stampDrag: null,
        _logoDrag: null,
        sidebar: {
            template: true,
            display: false,
            logo: false,
            invoice: true,
            client: true,
            items: true,
            notes: false,
            stamps: false,
        },

        init() {
            const p = window.__INVOICE_EDITOR__;
            if (!p) {
                return;
            }
            this.routes = p.routes;
            this.strings = p.strings;
            this.theme = p.theme;
            this.statusLabels = p.statusLabels || {};
            this.products = p.products || [];
            this.clients = p.clients || [];
            this.templates = p.templates || [];
            this.company = { ...p.company };
            this.items = JSON.parse(JSON.stringify(p.items || []));
            const rawDoc = p.document || {};
            const stamps = Array.isArray(rawDoc.stamps)
                ? JSON.parse(JSON.stringify(rawDoc.stamps))
                : [];
            if (stamps.length === 0 && rawDoc.stamp?.path) {
                stamps.push({
                    id:
                        typeof crypto !== "undefined" &&
                        typeof crypto.randomUUID === "function"
                            ? crypto.randomUUID()
                            : `stamp-${Date.now()}`,
                    path: rawDoc.stamp.path,
                    left_pct: Number(rawDoc.stamp.left_pct ?? 58),
                    top_pct: Number(rawDoc.stamp.top_pct ?? 52),
                    width_pct: Number(rawDoc.stamp.width_pct ?? 22),
                    rotation: Number(rawDoc.stamp.rotation ?? -8),
                });
            }
            this.document = {
                version: 2,
                meta: {
                    ...(rawDoc.meta || {}),
                    zoom: Number(rawDoc.meta?.zoom ?? 1) || 1,
                },
                custom: { ...(rawDoc.custom || {}) },
                logo: {
                    offset_x: Number(rawDoc.logo?.offset_x ?? 0),
                    offset_y: Number(rawDoc.logo?.offset_y ?? 0),
                    scale: Number(rawDoc.logo?.scale ?? 1) || 1,
                },
                stamps,
            };
            this.invoice = { ...p.invoice };
            if (
                this.invoice.client_id === null ||
                this.invoice.client_id === undefined
            ) {
                this.invoice.client_id = "";
            } else {
                this.invoice.client_id = String(this.invoice.client_id);
            }
            this.client = p.client ? { ...p.client } : null;
            this.templatePick = String(this.invoice.invoice_template_id || "");
            this.zoom = Number(this.document?.meta?.zoom ?? 1) || 1;
            this.applyZoomCss();
            this.pickDefaultSelectedStampId();
        },

        pickDefaultSelectedStampId() {
            const list = this.document.stamps || [];
            const firstWithPath = list.find((s) => s && s.path);
            this.selectedStampId = firstWithPath
                ? firstWithPath.id
                : list[0]?.id || "";
        },

        stampsOnPage() {
            return (this.document.stamps || []).filter((s) => s && s.path);
        },

        selectedStamp() {
            return (
                (this.document.stamps || []).find(
                    (s) => s.id === this.selectedStampId,
                ) || null
            );
        },

        csrf() {
            return (
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") || ""
            );
        },

        setStatus(msg) {
            this.statusMessage = msg || "";
            // Also update the header status span which lives outside x-data scope
            const el = document.getElementById("editor-status-msg");
            if (el) el.textContent = msg || "";
        },

        formatMoney(v) {
            return Number(v || 0).toFixed(2);
        },

        lineTotal(row) {
            const qty = Number(row.quantity) || 0;
            const price = Number(row.unit_price) || 0;
            const disc = Number(row.discount) || 0;
            const net = Math.max(0, qty * price - disc);
            const tax = (net * (Number(row.tax_rate) || 0)) / 100;

            return net + tax;
        },

        statusLabel() {
            return (
                this.statusLabels[this.invoice.status] || this.invoice.status
            );
        },

        badgeVariantClass() {
            const m = {
                paid: "badgePaid",
                pending: "badgePending",
                overdue: "badgeOverdue",
                draft: "badgeDraft",
                cancelled: "badgeCancelled",
            };

            const k = m[this.invoice.status] || "badgeDraft";

            return this.theme[k] || "";
        },

        applyZoomCss() {
            const el = document.getElementById("invoice-a4-scale");
            if (el) {
                el.style.transform = `scale(${this.zoom})`;
            }
        },

        zoomIn() {
            this.zoom = Math.min(2, Math.round((this.zoom + 0.1) * 10) / 10);
            this.document.meta = { ...this.document.meta, zoom: this.zoom };
            this.applyZoomCss();
            this.queueDesignSave();
        },

        zoomOut() {
            this.zoom = Math.max(0.45, Math.round((this.zoom - 0.1) * 10) / 10);
            this.document.meta = { ...this.document.meta, zoom: this.zoom };
            this.applyZoomCss();
            this.queueDesignSave();
        },

        queueMetaSave() {
            clearTimeout(this.metaTimer);
            this.metaTimer = setTimeout(() => this.flushMeta(), 550);
        },

        async flushMeta() {
            this.setStatus(this.strings.saving);
            try {
                const res = await fetch(this.routes.meta, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrf(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({
                        client_id:
                            this.invoice.client_id === "" ||
                            this.invoice.client_id === null
                                ? null
                                : parseInt(this.invoice.client_id, 10),
                        status: this.invoice.status,
                        invoice_date: this.invoice.invoice_date,
                        due_date: this.invoice.due_date || null,
                        currency: this.invoice.currency,
                        notes: this.invoice.notes,
                        terms: this.invoice.terms,
                        shipping_total: this.invoice.shipping_total,
                        direction: this.invoice.direction,
                    }),
                });
                const data = await res.json();
                if (!data.ok) {
                    throw new Error("meta");
                }
                Object.assign(this.invoice, data.invoice);
                this.invoice.client_id =
                    this.invoice.client_id === null ||
                    this.invoice.client_id === undefined
                        ? ""
                        : String(this.invoice.client_id);
                if (data.client !== undefined) {
                    this.client = data.client;
                }
                this.setStatus(this.strings.saved);
            } catch (e) {
                this.setStatus(this.strings.failed);
            }
        },

        queueClientSave() {
            if (!this.client) {
                return;
            }
            clearTimeout(this.clientTimer);
            this.clientTimer = setTimeout(() => this.flushClient(), 550);
        },

        async flushClient() {
            if (!this.client) {
                return;
            }
            this.setStatus(this.strings.saving);
            try {
                const res = await fetch(this.routes.client, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrf(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({
                        name: this.client.name,
                        company: this.client.company,
                        email: this.client.email,
                        phone: this.client.phone,
                        address: this.client.address,
                        vat_number: this.client.vat_number,
                    }),
                });
                const data = await res.json();
                if (!data.ok) {
                    throw new Error("client");
                }
                if (data.client) {
                    this.client = data.client;
                }
                this.setStatus(this.strings.saved);
            } catch (e) {
                this.setStatus(this.strings.failed);
            }
        },

        queueItemsSave() {
            clearTimeout(this.itemsTimer);
            this.itemsTimer = setTimeout(() => this.flushItems(), 450);
        },

        async flushItems() {
            this.setStatus(this.strings.saving);
            const payload = {
                items: this.items.map((row) => ({
                    id: row.id || null,
                    product_id: row.product_id || null,
                    name: row.name || "—",
                    description: row.description || null,
                    quantity: row.quantity,
                    unit_price: row.unit_price,
                    tax_rate: row.tax_rate ?? 0,
                    discount: row.discount ?? 0,
                })),
            };
            try {
                const res = await fetch(this.routes.items, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrf(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!data.ok) {
                    throw new Error("items");
                }
                this.items = data.items;
                Object.assign(this.invoice, data.invoice);
                this.setStatus(this.strings.saved);
            } catch (e) {
                this.setStatus(this.strings.failed);
            }
        },

        queueDesignSave() {
            clearTimeout(this.designTimer);
            this.designTimer = setTimeout(() => this.flushDesign(), 600);
        },

        async flushDesign() {
            const doc = {
                version: 2,
                meta: {
                    direction: this.invoice.direction,
                    zoom: this.zoom,
                },
                custom: { ...this.document.custom },
                logo: {
                    offset_x: Number(this.document.logo?.offset_x ?? 0),
                    offset_y: Number(this.document.logo?.offset_y ?? 0),
                    scale: Number(this.document.logo?.scale ?? 1) || 1,
                },
                stamps: (this.document.stamps || []).map((s) => ({
                    id: String(s.id),
                    path: s.path ?? null,
                    left_pct: Number(s.left_pct ?? 58),
                    top_pct: Number(s.top_pct ?? 52),
                    width_pct: Number(s.width_pct ?? 22),
                    rotation: Number(s.rotation ?? 0),
                })),
            };
            try {
                const res = await fetch(this.routes.design, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrf(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({ document: doc }),
                });
                const data = await res.json();
                if (!data.ok) {
                    throw new Error("design");
                }
                this.document = {
                    ...this.document,
                    ...doc,
                    custom: doc.custom,
                    meta: doc.meta,
                    logo: doc.logo,
                    stamps: doc.stamps,
                };
                this.setStatus(this.strings.saved);
            } catch (e) {
                this.setStatus(this.strings.failed);
            }
        },

        addBlankRow() {
            this.items.push({
                id: null,
                product_id: null,
                name: "",
                description: null,
                quantity: 1,
                unit_price: 0,
                tax_rate: 0,
                discount: 0,
                line_total: 0,
            });
            this.queueItemsSave();
        },

        addProductRow() {
            const pid = this.productPick;
            if (!pid) {
                return;
            }
            const p = this.products.find((x) => String(x.id) === String(pid));
            if (!p) {
                return;
            }
            this.items.push({
                id: null,
                product_id: p.id,
                name: p.name,
                description: null,
                quantity: 1,
                unit_price: p.price,
                tax_rate: p.tax_rate ?? 0,
                discount: 0,
                line_total: 0,
            });
            this.productPick = "";
            this.queueItemsSave();
        },

        removeRow(idx) {
            this.items.splice(idx, 1);
            this.queueItemsSave();
        },

        onClientAssigned() {
            this.queueMetaSave();
        },

        async applyTemplate() {
            const id = parseInt(this.templatePick, 10);
            if (!id) {
                return;
            }
            this.setStatus(this.strings.saving);
            try {
                const res = await fetch(this.routes.template, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrf(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({ invoice_template_id: id }),
                });
                const data = await res.json();
                if (!data.ok) {
                    throw new Error("tpl");
                }
                if (data.reload) {
                    window.location.reload();
                }
            } catch (e) {
                this.setStatus(this.strings.failed);
            }
        },

        async saveAll() {
            clearTimeout(this.metaTimer);
            clearTimeout(this.itemsTimer);
            clearTimeout(this.designTimer);
            clearTimeout(this.clientTimer);
            await this.flushItems();
            await this.flushMeta();
            if (this.client) {
                await this.flushClient();
            }
            await this.flushDesign();
        },

        hasStamp() {
            return this.stampsOnPage().length > 0;
        },

        publicStorageUrl(path) {
            if (!path || typeof path !== "string") {
                return "";
            }
            if (path.startsWith("http://") || path.startsWith("https://")) {
                return path;
            }

            return `/storage/${String(path).replace(/^\/+/, "")}`;
        },

        stampUrlFor(stamp) {
            return this.publicStorageUrl(stamp?.path);
        },

        stampBoxStyle(st) {
            return `position:absolute;left:${Number(st.left_pct ?? 0)}%;top:${Number(st.top_pct ?? 0)}%;width:${Number(st.width_pct ?? 20)}%;transform:rotate(${Number(st.rotation ?? 0)}deg);transform-origin:center center;`;
        },

        openLogoPicker() {
            this.$refs.logoFile?.click();
        },

        logoImageStyle() {
            const ox = Number(this.document.logo?.offset_x ?? 0);
            const oy = Number(this.document.logo?.offset_y ?? 0);
            const sc = Number(this.document.logo?.scale ?? 1) || 1;

            return `transform: translate(${ox}px, ${oy}px) scale(${sc});`;
        },

        logoPointerDown(e) {
            if (e.button !== 0) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            this.logoDragging = true;
            const ox = Number(this.document.logo.offset_x);
            const oy = Number(this.document.logo.offset_y);
            this._logoDrag = { startX: e.clientX, startY: e.clientY, ox, oy };
            this._boundLogoMove = (ev) => this.logoDragMove(ev);
            this._boundLogoUp = () => this.logoDragUp();
            window.addEventListener("pointermove", this._boundLogoMove);
            window.addEventListener("pointerup", this._boundLogoUp, {
                capture: true,
            });
        },

        logoDragMove(ev) {
            if (!this._logoDrag) {
                return;
            }
            const dx = ev.clientX - this._logoDrag.startX;
            const dy = ev.clientY - this._logoDrag.startY;
            let nx = this._logoDrag.ox + dx;
            let ny = this._logoDrag.oy + dy;
            nx = Math.max(-24, Math.min(24, nx));
            ny = Math.max(-24, Math.min(24, ny));
            this.document.logo.offset_x = nx;
            this.document.logo.offset_y = ny;
        },

        logoDragUp() {
            if (this._boundLogoMove) {
                window.removeEventListener("pointermove", this._boundLogoMove);
            }
            if (this._boundLogoUp) {
                window.removeEventListener("pointerup", this._boundLogoUp, {
                    capture: true,
                });
            }
            this._boundLogoMove = null;
            this._boundLogoUp = null;
            this._logoDrag = null;
            this.logoDragging = false;
            this.queueDesignSave();
        },

        async uploadLogoFile(e) {
            const file = e.target.files?.[0];
            e.target.value = "";
            if (!file) {
                return;
            }
            this.setStatus(this.strings.saving);
            const fd = new FormData();
            fd.append("logo", file);
            try {
                const res = await fetch(this.routes.logo, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": this.csrf(),
                        Accept: "application/json",
                    },
                    body: fd,
                });
                const data = await res.json();
                if (!res.ok || !data.logo_url) {
                    throw new Error("logo");
                }
                this.company.logo_url = data.logo_url;
                this.setStatus(this.strings.saved);
            } catch (err) {
                this.setStatus(this.strings.failed);
            }
        },

        stampPointerDown(e, stamp) {
            if (e.button !== 0) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            this.selectedStampId = stamp.id;
            this.stampDragging = true;
            const canvas = document.getElementById("invoice-a4-canvas");
            if (!canvas) {
                return;
            }
            const rect = canvas.getBoundingClientRect();
            this._stampDrag = {
                stampId: stamp.id,
                rectW: rect.width,
                rectH: rect.height,
                startX: e.clientX,
                startY: e.clientY,
                startLeft: Number(stamp.left_pct),
                startTop: Number(stamp.top_pct),
            };
            this._boundStampMove = (ev) => this.stampDragMove(ev);
            this._boundStampUp = () => this.stampDragUp();
            window.addEventListener("pointermove", this._boundStampMove);
            window.addEventListener("pointerup", this._boundStampUp, {
                capture: true,
            });
        },

        stampDragMove(ev) {
            if (!this._stampDrag) {
                return;
            }
            const st = (this.document.stamps || []).find(
                (s) => s.id === this._stampDrag.stampId,
            );
            if (!st) {
                return;
            }
            const dx = ev.clientX - this._stampDrag.startX;
            const dy = ev.clientY - this._stampDrag.startY;
            const dlp = (dx / this._stampDrag.rectW) * 100;
            const dtp = (dy / this._stampDrag.rectH) * 100;
            let nl = this._stampDrag.startLeft + dlp;
            let nt = this._stampDrag.startTop + dtp;
            const w = Number(st.width_pct) || 20;
            nl = Math.max(0, Math.min(100 - w * 0.35, nl));
            nt = Math.max(0, Math.min(92, nt));
            st.left_pct = nl;
            st.top_pct = nt;
        },

        stampDragUp() {
            if (this._boundStampMove) {
                window.removeEventListener("pointermove", this._boundStampMove);
            }
            if (this._boundStampUp) {
                window.removeEventListener("pointerup", this._boundStampUp, {
                    capture: true,
                });
            }
            this._boundStampMove = null;
            this._boundStampUp = null;
            this._stampDrag = null;
            this.stampDragging = false;
            this.queueDesignSave();
        },

        async uploadStampFile(e) {
            const file = e.target.files?.[0];
            e.target.value = "";
            if (!file) {
                return;
            }
            this.setStatus(this.strings.saving);
            const fd = new FormData();
            fd.append("stamp", file);
            try {
                const res = await fetch(this.routes.stamp, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": this.csrf(),
                        Accept: "application/json",
                    },
                    body: fd,
                });
                const data = await res.json();
                if (!res.ok || !data.ok || !data.document) {
                    throw new Error("stamp");
                }
                this.document.stamps = data.document.stamps || [];
                if (data.new_stamp_id) {
                    this.selectedStampId = data.new_stamp_id;
                } else {
                    this.pickDefaultSelectedStampId();
                }
                this.setStatus(this.strings.saved);
            } catch (err) {
                this.setStatus(this.strings.failed);
            }
        },

        async removeStampById(stampId) {
            if (!stampId) {
                return;
            }
            this.setStatus(this.strings.saving);
            try {
                const res = await fetch(this.routes.stampRemove, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrf(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({ stamp_id: stampId }),
                });
                const data = await res.json();
                if (!res.ok || !data.ok || !data.document) {
                    throw new Error("stamp");
                }
                this.document.stamps = data.document.stamps || [];
                this.pickDefaultSelectedStampId();
                this.setStatus(this.strings.saved);
            } catch (err) {
                this.setStatus(this.strings.failed);
            }
        },

        async removeSelectedStamp() {
            const s = this.selectedStamp();
            if (!s?.path) {
                return;
            }
            await this.removeStampById(s.id);
        },

        resetStampPosition() {
            const s = this.selectedStamp();
            if (!s?.path) {
                return;
            }
            s.left_pct = 58;
            s.top_pct = 52;
            s.width_pct = 22;
            s.rotation = -8;
            this.queueDesignSave();
        },

        exportPdf() {
            window.location.href = this.routes.dompdf;
        },

        print() {
            const node = document.getElementById("invoice-a4-canvas");
            if (!node) return;

            const dir = document.documentElement.dir || "ltr";
            const lang = document.documentElement.lang || "en";
            const cssLinks = Array.from(
                document.querySelectorAll('link[rel="stylesheet"]'),
            )
                .map((l) => `<link rel="stylesheet" href="${l.href}">`)
                .join("");

            const w = window.open("", "_blank");
            w.document.write(
                `<!DOCTYPE html><html lang="${lang}" dir="${dir}"><head>` +
                    `<meta charset="utf-8"><title>Invoice</title>${cssLinks}` +
                    `<style>` +
                    `@page{size:A4 portrait;margin:0}` +
                    `html{margin:0;padding:0;background:#fff;}body{margin:0 auto;padding:0;background:#fff!important;zoom:0.95;width:210mm;}` +
                    `#invoice-a4-canvas{width:210mm!important;min-height:unset!important;box-shadow:none!important;border-radius:0!important;background:#fff!important;padding:10mm 12mm;box-sizing:border-box;}` +
                    `</style></head><body>${node.outerHTML}</body></html>`,
            );
            w.document.close();

            w.addEventListener("load", () => {
                w.focus();
                w.print();
                w.close();
            });
        },
    }));
}
