document.addEventListener('DOMContentLoaded', function () {
    const el = id => document.getElementById(id);
    const protocol = el('protocol');
    const ip = el('ip');
    const port = el('port');
    const hex = el('hex');
    const output = el('output');

    function sanitizeHexInput(s) {
        return s.replace(/[^0-9a-fA-F\s]/g, '');
    }

    function parseHex(s) {
        s = sanitizeHexInput(s);
        const parts = s.trim().split(/\s+/).filter(Boolean);
        let bytes = [];
        if (parts.length > 1 && parts.every(p => p.length === 2)) {
            bytes = parts.map(p => p.toUpperCase());
        } else {
            const joined = s.replace(/\s+/g, '');
            if (joined.length % 2 !== 0) throw new Error('Hex string heeft oneven aantal tekens.');
            for (let i = 0; i < joined.length; i += 2) bytes.push(joined.substr(i, 2).toUpperCase());
        }
        if (bytes.some(b => !/^[0-9A-F]{2}$/.test(b))) throw new Error('Ongeldige hex byte gevonden.');
        return bytes;
    }

    function formatPayload(bytes, proto) {
        if (bytes.length === 0) return '';
        let joined = bytes.map(b => '\\x' + b).join('');
        if (proto === 'tcp') joined += '\\r\\n';
        return joined;
    }

    function generate() {
        try {
            const proto = protocol.value.trim().toLowerCase();
            const ipVal = ip.value.trim();
            const portVal = String(Number(port.value) || '');
            if (!proto && !ipVal && !port.value && !hex.value) {
                output.textContent = '';
                return;
            }
            const bytes = parseHex(hex.value);
            const payload = formatPayload(bytes, proto);
            output.textContent = `|${proto}|${ipVal}|${portVal}|${payload}|`;
        } catch (e) {
            output.textContent = 'Error: ' + e.message;
        }
    }

    el('generate').addEventListener('click', generate);
    el('copy').addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(output.textContent);
            alert('Gekopieerd!');
        } catch (e) {
            alert('Kopie mislukt');
        }
    });
});
