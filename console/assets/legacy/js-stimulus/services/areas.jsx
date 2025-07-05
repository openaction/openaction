export function isAreaAlreadySelected(selectedAreas, area) {
    if (typeof selectedAreas[area.id] !== 'undefined') {
        return true;
    }

    return area.parent ? isAreaAlreadySelected(selectedAreas, area.parent) : false;
}
