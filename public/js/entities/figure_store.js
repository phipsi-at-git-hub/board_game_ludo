export const figure_store = {}; 

export function addFigureMesh(figure_id, mesh) {
    figure_store[figure_id] = mesh; 
}

export function getFigureMesh(figure_id) {
    return figure_store[figure_id]; 
}

export function hasFigureMesh(figure_id) {
    return figure_store[figure_id] !== undefined; 
}