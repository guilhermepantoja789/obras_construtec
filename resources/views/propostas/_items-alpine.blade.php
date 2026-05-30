{{-- Shared Alpine.js logic for proposta item lists (create/edit) --}}
searchQuery: '',
showOnlyEtapas: false,
groupByEtapa: false,
collapsedGroups: {},
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
