<script>
function propostaForm(config) {
    return {
        items: config.items ?? [],
        encargos: config.encargos ?? [],
        importUrl: config.importUrl,
        csrfToken: config.csrfToken,
        activeTab: 'itens',
        showItemSheet: false,
        editingIndex: null,
        draftItem: null,
        searchQuery: '',
        showOnlyEtapas: false,
        groupByEtapa: true,
        collapsedGroups: {},
        isLoading: false,

        initPropostaForm() {
            this.initPropostaItems();
            if (this.items.length === 0) {
                this.addItem();
            }
        },

        addItem() {
            this.items.push({
                descricao: '',
                unidade: 'un',
                quantidade: 1,
                valor_unitario: 0,
                is_etapa: false,
                ordem: String(this.items.length + 1),
            });
        },

        initPropostaItems() {
            this.items.forEach((item) => {
                if (item.is_etapa && item.ordem) {
                    this.collapsedGroups[item.ordem] = false;
                }
                item.is_etapa = !!item.is_etapa;
            });
        },

        toggleGroup(ordem) {
            this.collapsedGroups[ordem] = !this.collapsedGroups[ordem];
        },

        isGroupCollapsed(ordem) {
            return !!this.collapsedGroups[ordem];
        },

        ordemDepth(ordem) {
            if (!ordem && ordem !== 0) return 0;
            return String(ordem).split('.').length;
        },

        compareOrdem(a, b) {
            const partsA = String(a || '999999').split('.').map(n => parseInt(n, 10) || 0);
            const partsB = String(b || '999999').split('.').map(n => parseInt(n, 10) || 0);
            const max = Math.max(partsA.length, partsB.length);
            for (let i = 0; i < max; i++) {
                const segA = partsA[i] ?? 0;
                const segB = partsB[i] ?? 0;
                if (segA !== segB) return segA - segB;
            }
            return 0;
        },

        getParentEtapaOrdem(itemIndex) {
            const sorted = this.items
                .map((item, index) => ({ item, index }))
                .sort((a, b) => this.compareOrdem(a.item.ordem, b.item.ordem));
            let parentOrdem = null;
            for (const entry of sorted) {
                if (entry.index === itemIndex) break;
                if (entry.item.is_etapa) parentOrdem = entry.item.ordem;
            }
            return parentOrdem;
        },

        itemMatchesSearch(item) {
            if (!this.searchQuery.trim()) return true;
            const q = this.searchQuery.toLowerCase();
            return String(item.descricao || '').toLowerCase().includes(q)
                || String(item.ordem || '').toLowerCase().includes(q);
        },

        itemVisible(item, index) {
            if (!this.itemMatchesSearch(item)) return false;
            if (this.showOnlyEtapas && !item.is_etapa) return false;
            if (this.groupByEtapa && !item.is_etapa) {
                const parent = this.getParentEtapaOrdem(index);
                if (parent && this.isGroupCollapsed(parent)) return false;
            }
            return true;
        },

        get filteredItems() {
            return this.items.filter((item, index) => this.itemVisible(item, index));
        },

        openNewItem() {
            this.editingIndex = null;
            this.draftItem = {
                descricao: '',
                unidade: 'un',
                quantidade: 1,
                valor_unitario: 0,
                is_etapa: false,
                ordem: String(this.items.length + 1),
            };
            this.showItemSheet = true;
        },

        openEditItem(index) {
            this.editingIndex = index;
            this.draftItem = { ...this.items[index] };
            this.showItemSheet = true;
        },

        saveDraftItem() {
            if (!this.draftItem.descricao?.trim()) {
                alert('Informe a descrição do item.');
                return;
            }
            if (this.editingIndex === null) {
                this.items.push({ ...this.draftItem });
            } else {
                this.items[this.editingIndex] = { ...this.draftItem };
            }
            this.showItemSheet = false;
            this.draftItem = null;
        },

        removeItemAt(index) {
            if (confirm('Remover este item?')) {
                this.items.splice(index, 1);
                if (this.editingIndex === index) this.showItemSheet = false;
            }
        },

        getSubtotalItens() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.quantidade) * parseFloat(item.valor_unitario) || 0), 0);
        },

        getEncargoValor(encargo) {
            if (!encargo.ativo) return 0;
            return this.getSubtotalItens() * (parseFloat(encargo.percent) || 0) / 100;
        },

        getTotalComEncargos() {
            let total = this.getSubtotalItens();
            this.encargos.forEach(e => {
                const v = this.getEncargoValor(e);
                total += e.subtrai ? -v : v;
            });
            return total;
        },

        formatMoney(value) {
            return (parseFloat(value) || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        itemSubtotal(item) {
            return (parseFloat(item.quantidade) || 0) * (parseFloat(item.valor_unitario) || 0);
        },

        async importExcel(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', this.csrfToken);

            this.isLoading = true;
            try {
                const response = await fetch(this.importUrl, { method: 'POST', body: formData });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Falha na importação');

                if (this.items.length > 0 && this.items[0].descricao !== '') {
                    if (confirm('Substituir itens atuais pelos do arquivo?')) {
                        this.items = data.items;
                    } else {
                        data.items.forEach(item => this.items.push(item));
                    }
                } else {
                    this.items = data.items;
                }

                this.initPropostaItems();
                this.activeTab = 'itens';
                alert('Importação concluída: ' + data.items.length + ' itens.');
            } catch (error) {
                alert('Erro: ' + error.message);
            } finally {
                this.isLoading = false;
                event.target.value = '';
            }
        },
    };
}
</script>
